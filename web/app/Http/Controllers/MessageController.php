<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class MessageController extends Controller
{
    public function main(Request $request)
    {
        $currentUserId = session('usr_id');
        $search = $request->query('search', '');

        $query = DB::table('message_groups as mg')
            ->join('messages as last_msg', function ($join) {
                // Subquery: get the latest mes_id per mesg_group_id
                $join->on('last_msg.mes_id', '=', DB::raw(
                    '(SELECT m2.mes_id FROM messages m2
                      WHERE m2.mesg_group_id = mg.mesg_group_id
                        AND m2.mes_active = 1
                      ORDER BY m2.mes_date_created DESC
                      LIMIT 1)'
                ));
            })
            ->join('users as sender', 'sender.usr_id', '=', 'last_msg.mes_created_by')
            ->where('mg.usr_id', $currentUserId)   // current user is a member
            ->where('mg.mesg_active', 1);

        // Search: filter by group name OR member name
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('mg.mesg_group_name', 'LIKE', "%$search%")
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->select(DB::raw(1))
                            ->from('message_groups as mg2')
                            ->join('users as u2', 'u2.usr_id', '=', 'mg2.usr_id')
                            ->whereColumn('mg2.mesg_group_id', 'mg.mesg_group_id')
                            ->where('mg2.mesg_active', 1)
                            ->where(DB::raw("CONCAT(u2.usr_first_name, ' ', u2.usr_last_name)"), 'LIKE', "%$search%");
                    });
            });
        }

        $query->select(
            'mg.mesg_group_id',
            'mg.mesg_group_name',
            'mg.mesg_group_image',
            'last_msg.mes_content',
            'last_msg.mes_date_created as last_message_date',
            DB::raw("CONCAT(sender.usr_first_name, ' ', sender.usr_last_name) as sender_name"),
            'last_msg.mes_created_by as sender_id'
        )
            ->orderBy('last_msg.mes_date_created', 'desc');

        // Limit to 10 per page only when NOT searching
        if (empty($search)) {
            $conversations = $query->paginate(10);
        } else {
            $conversations = $query->get();
        }

        // For each conversation, fetch all members (to build display name & avatar)
        $groupIds = $conversations instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $conversations->pluck('mesg_group_id')->toArray()
            : $conversations->pluck('mesg_group_id')->toArray();

        $members = DB::table('message_groups as mg')
            ->join('users as u', 'u.usr_id', '=', 'mg.usr_id')
            ->whereIn('mg.mesg_group_id', $groupIds)
            ->where('mg.mesg_active', 1)
            ->select('mg.mesg_group_id', 'u.usr_id', 'u.usr_first_name', 'u.usr_last_name')
            ->get()
            ->groupBy('mesg_group_id');

        // Fetch all users for compose modal dropdown
        $users = DB::table('users')
            ->where('usr_id', '!=', $currentUserId)
            ->select('usr_id', 'usr_first_name', 'usr_middle_name', 'usr_last_name')
            ->orderBy('usr_first_name')
            ->get();

        return view('messages.main', compact('conversations', 'members', 'users', 'search'));
    }

    public function personal(Request $request, $mesg_group_id)
    {
        $currentUserId = session('usr_id');

        // Verify user belongs to this group
        $isMember = DB::table('message_groups')
            ->where('mesg_group_id', $mesg_group_id)
            ->where('usr_id', $currentUserId)
            ->where('mesg_active', 1)
            ->exists();

        if (!$isMember) {
            abort(403, 'Unauthorized');
        }

        // Fetch group info
        $groupInfo = DB::table('message_groups')
            ->where('mesg_group_id', $mesg_group_id)
            ->where('mesg_active', 1)
            ->first();

        // Fetch all members of this group
        $members = DB::table('message_groups as mg')
            ->join('users as u', 'u.usr_id', '=', 'mg.usr_id')
            ->where('mg.mesg_group_id', $mesg_group_id)
            ->where('mg.mesg_active', 1)
            ->select('u.usr_id', 'u.usr_first_name', 'u.usr_last_name')
            ->get();

        // Determine if group or private
        $isGroup = $members->count() > 2 || !empty($groupInfo->mesg_group_name);

        // Fetch messages paginated (oldest first for chat display)
        $messages = DB::table('messages as m')
            ->join('users as u', 'u.usr_id', '=', 'm.mes_created_by')
            ->where('m.mesg_group_id', $mesg_group_id)
            ->where('m.mes_active', 1)
            ->select(
                'm.mes_id',
                'm.mes_uuid',
                'm.mes_content',
                'm.mes_date_created',
                'm.mes_created_by',
                DB::raw("CONCAT(u.usr_first_name, ' ', u.usr_last_name) as sender_name"),
                'u.usr_first_name'
            )
            ->orderBy('m.mes_date_created', 'asc')
            ->paginate(50);

        // Fetch all conversations for left panel (same query as main, reused)
        $leftConversations = DB::table('message_groups as mg')
            ->join('messages as last_msg', function ($join) {
                $join->on('last_msg.mes_id', '=', DB::raw(
                    '(SELECT m2.mes_id FROM messages m2
                      WHERE m2.mesg_group_id = mg.mesg_group_id
                        AND m2.mes_active = 1
                      ORDER BY m2.mes_date_created DESC
                      LIMIT 1)'
                ));
            })
            ->join('users as sender', 'sender.usr_id', '=', 'last_msg.mes_created_by')
            ->where('mg.usr_id', $currentUserId)
            ->where('mg.mesg_active', 1)
            ->select(
                'mg.mesg_group_id',
                'mg.mesg_group_name',
                'mg.mesg_group_image',
                'last_msg.mes_content',
                'last_msg.mes_date_created as last_message_date',
                DB::raw("CONCAT(sender.usr_first_name, ' ', sender.usr_last_name) as sender_name"),
                'last_msg.mes_created_by as sender_id'
            )
            ->orderBy('last_msg.mes_date_created', 'desc')
            ->limit(10)
            ->get();

        $groupIds = $leftConversations->pluck('mesg_group_id')->toArray();
        $leftMembers = DB::table('message_groups as mg')
            ->join('users as u', 'u.usr_id', '=', 'mg.usr_id')
            ->whereIn('mg.mesg_group_id', $groupIds)
            ->where('mg.mesg_active', 1)
            ->select('mg.mesg_group_id', 'u.usr_id', 'u.usr_first_name', 'u.usr_last_name')
            ->get()
            ->groupBy('mesg_group_id');

        // Fetch all users for compose modal
        $users = DB::table('users')
            ->where('usr_id', '!=', $currentUserId)
            ->select('usr_id', 'usr_first_name', 'usr_middle_name', 'usr_last_name')
            ->orderBy('usr_first_name')
            ->get();

        return view('messages.personal', compact(
            'messages',
            'members',
            'groupInfo',
            'isGroup',
            'mesg_group_id',
            'leftConversations',
            'leftMembers',
            'users'
        ));
    }

    public function send(Request $request)
    {
        $request->validate([
            'mesg_group_id' => 'required|integer',
            'mes_content' => 'required|string|max:5000',
        ]);

        $currentUserId = session('usr_id');

        // Verify membership
        $isMember = DB::table('message_groups')
            ->where('mesg_group_id', $request->mesg_group_id)
            ->where('usr_id', $currentUserId)
            ->where('mesg_active', 1)
            ->exists();

        if (!$isMember) {
            abort(403);
        }

        DB::table('messages')->insert([
            'mes_uuid' => generateuuid(),
            'mesg_group_id' => $request->mesg_group_id,
            'mes_content' => $request->mes_content,
            'mes_date_created' => Carbon::now(),
            'mes_created_by' => $currentUserId,
            'mes_active' => 1,
        ]);

        return redirect()->to(url('messages/chat/' . $request->mesg_group_id));
    }

    public function compose(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'integer|exists:users,usr_id',
            'mes_content' => 'required|string|max:5000',
        ]);

        $currentUserId = session('usr_id');
        $recipients = $request->recipients;

        // Full participant list = sender + all recipients (sorted for matching)
        $allParticipants = array_unique(array_merge([$currentUserId], $recipients));
        sort($allParticipants);

        // Try to find existing group with EXACTLY these members
        $existingGroupId = $this->find_exact_group($allParticipants);

        if ($existingGroupId) {
            $groupId = $existingGroupId;
        } else {
            // Create new group — get next group id
            $lastGroup = DB::table('message_groups')->max('mesg_group_id');
            $groupId = $lastGroup ? $lastGroup + 1 : 1;

            // Insert one row per participant
            $now = Carbon::now();
            foreach ($allParticipants as $userId) {
                DB::table('message_groups')->insert([
                    'mesg_uuid' => generateuuid(),
                    'mesg_group_id' => $groupId,
                    'mesg_group_name' => null,
                    'mesg_group_image' => null,
                    'usr_id' => $userId,
                    'mesg_date_created' => $now,
                    'mesg_created_by' => $currentUserId,
                    'mesg_active' => 1,
                ]);
            }
        }

        // Insert the message
        DB::table('messages')->insert([
            'mes_uuid' => generateuuid(),
            'mesg_group_id' => $groupId,
            'mes_content' => $request->mes_content,
            'mes_date_created' => Carbon::now(),
            'mes_created_by' => $currentUserId,
            'mes_active' => 1,
        ]);

        return redirect()->to(url('messages/chat/' . $groupId));
    }

    private function find_exact_group(array $participantIds): ?int
    {
        $count = count($participantIds);

        // Get all group_ids where member count matches
        $candidateGroups = DB::table('message_groups')
            ->where('mesg_active', 1)
            ->select('mesg_group_id')
            ->groupBy('mesg_group_id')
            ->havingRaw('COUNT(DISTINCT usr_id) = ?', [$count])
            ->pluck('mesg_group_id');

        foreach ($candidateGroups as $groupId) {
            $groupMembers = DB::table('message_groups')
                ->where('mesg_group_id', $groupId)
                ->where('mesg_active', 1)
                ->pluck('usr_id')
                ->sort()
                ->values()
                ->toArray();

            $sortedParticipants = $participantIds;
            sort($sortedParticipants);

            if ($groupMembers === $sortedParticipants) {
                return $groupId;
            }
        }

        return null;
    }
}