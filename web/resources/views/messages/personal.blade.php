<style>
    /* ── Sidebar list ─────────── */
    .msg-list-item {
        transition: background .15s;
    }

    .msg-list-item:hover {
        background: #f8f9fa;
    }

    .msg-list-active {
        background: #e9f0fb !important;
        border-left: 3px solid #007bff;
    }

    .msg-display-name {
        max-width: 160px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .msg-preview {
        max-width: 160px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    /* ── Chat bubbles ─────────── */
    .msg-bubble {
    padding: 8px 14px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.4;
    word-break: break-word;
    white-space: pre-wrap;
    text-align: left;
    display: block;        /* ← change from inline-block to block */
    width: fit-content;    /* ← shrink to content width */
    max-width: 100%;       /* ← but never overflow the container */
}

    .msg-bubble-mine {
        background: #007bff;
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .msg-bubble-other {
    background: #fff;
    color: #212529;
    border: 1px solid #dee2e6;
    border-bottom-left-radius: 4px;
    text-align: left;
    display: block;           /* force it to fill container */
    width: 100%;              /* ensure it spans max-width of parent */
}

    /* ── Layout ───────────────── */
    #msg-sidebar .card,
    #msg-chat-panel .card {
        border-radius: 0;
        border-top: none;
    }

    #msg-chat-panel {
        min-height: calc(100vh - 200px);
    }

    /* ── Select2 ──────────────── */
    .select2-container {
        width: 100% !important;
    }

    /* --- Modal chrome --- */
    #composeModal .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 14px 20px;
    }

    #composeModal .modal-title {
        font-size: 15px;
        font-weight: 600;
        color: #000000;
    }

    #composeModal .modal-body {
        padding: 20px;
    }

    #composeModal .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 12px 20px;
    }

    /* --- Labels --- */
    #composeModal label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #000000;
        margin-bottom: 6px;
    }

    /* --- Textarea --- */
    #composeModal textarea.form-control {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        transition: border-color 0.15s, box-shadow 0.15s;
        resize: none;
    }

    #composeModal textarea.form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
        outline: none;
    }

    /* --- Buttons --- */
    #composeModal .btn-secondary {
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        padding: 7px 16px;
        border: 1.5px solid #dee2e6;
        background: #fff;
        color: #000000;
        transition: background 0.15s, color 0.15s;
    }

    #composeModal .btn-secondary:hover {
        background: #f1f3f5;
        color: #333;
    }

    /* PRIMARY SEND BUTTON */
    #composeModal .btn-primary {
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        padding: 7px 18px;
        background-color: #0d6efd;
        border-color: #0d6efd;
        transition: background 0.15s, transform 0.1s;
    }

    #composeModal .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0b5ed7;
    }

    #composeModal .btn-primary:active {
        transform: scale(0.97);
    }

    /* Select2 */
    #composeModal .select2-container--default .select2-selection--multiple {
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 8px !important;
        min-height: 42px !important;
        max-height: 160px !important;
        overflow-y: auto !important;
        padding: 4px 6px !important;
        background-color: #fff !important;
        transition: border-color 0.15s, box-shadow 0.15s !important;
    }

    #composeModal .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12) !important;
    }

    /* Tags */
    #composeModal .select2-selection__choice {
        background-color: #e7f1ff !important;
        border-radius: 20px !important;
        color: #0b5ed7 !important;
        font-size: 12px !important;
        padding: 3px 10px !important;
        margin: 3px 4px 3px 0 !important;
    }

    #composeModal .select2-selection__choice__remove {
        color: #0d6efd !important;
        font-weight: 700 !important;
    }

    #composeModal .select2-selection__choice__remove:hover {
        color: #0b5ed7 !important;
    }

    /* Dropdown */
    .select2-dropdown {
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08) !important;
        font-size: 13px !important;
    }

    .select2-container--default .select2-results__option {
        padding: 9px 14px !important;
    }

    .select2-container--default .select2-results__option--highlighted {
        background-color: #e7f1ff !important;
        color: #0b5ed7 !important;
    }

    .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: #cfe2ff !important;
        color: #084298 !important;
        font-weight: 500 !important;
    }

    /* Search box */
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
    }

    .select2-search--dropdown .select2-search__field:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1) !important;
    }

    /* Full width */
    .select2-container {
        width: 100% !important;
    }
</style>

@extends('layouts.themes.main')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Messages</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ action('App\Http\Controllers\AdminController@home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('messages') }}">Messages</a>
                        </li>
                        <li class="breadcrumb-item active">Chat</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        @include('layouts.partials.onclick')
        @include('layouts.partials.alerts')

        <div class="container-fluid">
            <div class="row" style="min-height: calc(100vh - 200px);">

                {{-- LEFT PANEL --}}
                <div class="col-md-4 col-lg-3 px-0" id="msg-sidebar">
                    <div class="card card-primary card-outline h-100 mb-0">

                        <div class="card-header d-flex align-items-center justify-content-between py-2">
                            <span class="font-weight-bold">Chats</span>
                            <a href="#" data-toggle="modal" data-target="#composeModal" class="btn btn-primary btn-sm"
                                title="New Message">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>

                        <div class="card-body p-0" style="overflow-y:auto; max-height:calc(100vh - 220px);">
                            @php $currentUserId = session('usr_id'); @endphp

                            @forelse ($leftConversations as $conv)
                                @php
                                    $grpId = $conv->mesg_group_id;
                                    $grpMembers = $leftMembers[$grpId] ?? collect();
                                    $otherMembers = $grpMembers->where('usr_id', '!=', $currentUserId);
                                    $isGrp = $grpMembers->count() > 2 || !empty($conv->mesg_group_name);
                                    $isActive = $grpId == $mesg_group_id;

                                    if (!empty($conv->mesg_group_name)) {
                                        $displayName = $conv->mesg_group_name;
                                    } else {
                                        $nameParts = $otherMembers
                                            ->map(fn($m) => $m->usr_first_name . ' ' . $m->usr_last_name)
                                            ->toArray();
                                        $displayName = implode(', ', $nameParts);
                                    }

                                    $isSelf = $conv->sender_id == $currentUserId;
                                    $preview =
                                        $isGrp && !$isSelf
                                            ? $conv->sender_name . ': ' . $conv->mes_content
                                            : ($isSelf
                                                ? 'You: ' . $conv->mes_content
                                                : $conv->mes_content);
                                @endphp

                                <a href="{{ url('messages/chat/' . $grpId) }}"
                                    class="msg-list-item d-flex align-items-center px-3 py-2 text-dark text-decoration-none border-bottom
                                      {{ $isActive ? 'msg-list-active' : '' }}">

                                    <div class="flex-shrink-0 mr-2">
                                        @if ($isGrp && !empty($conv->mesg_group_image))
                                            <img src="{{ asset('images/messages/group_images/' . $conv->mesg_group_image) }}"
                                                class="img-circle elevation-1"
                                                style="width:40px;height:40px;object-fit:cover;" alt="Group">
                                        @elseif ($isGrp)
                                            <span
                                                class="img-circle elevation-1 d-flex align-items-center justify-content-center bg-secondary text-white"
                                                style="width:40px;height:40px;font-size:18px;">
                                                <i class="fas fa-users"></i>
                                            </span>
                                        @else
                                            @php $firstOther = $otherMembers->first(); @endphp
                                            <img src="{{ asset(getAvatar($firstOther->usr_id ?? $currentUserId)) }}"
                                                class="img-circle elevation-1"
                                                style="width:40px;height:40px;object-fit:cover;" alt="User">
                                        @endif
                                    </div>

                                    <div class="flex-grow-1 min-width-0">
                                        <div class="font-weight-bold text-truncate msg-display-name">
                                            {{ $displayName }}
                                        </div>
                                        <small class="text-muted text-truncate d-block msg-preview">
                                            {{ $preview }}
                                        </small>
                                    </div>

                                    <div class="flex-shrink-0 ml-2 text-muted" style="font-size:11px;white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($conv->last_message_date)->format('M j') }}
                                    </div>
                                </a>
                            @empty
                                <div class="p-3 text-muted text-center">No conversations yet.</div>
                            @endforelse
                        </div>

                    </div>
                </div>

                {{-- RIGHT PANEL --}}
                <div class="col-md-8 col-lg-9 px-0 d-flex flex-column" id="msg-chat-panel">
                    <div class="card h-100 mb-0 d-flex flex-column">

                        {{-- Chat header --}}
                        @php
                            $otherMembersAll = $members->where('usr_id', '!=', $currentUserId);

                            if (!empty($groupInfo->mesg_group_name)) {
                                $chatTitle = $groupInfo->mesg_group_name;
                            } else {
                                $chatTitle = $otherMembersAll
                                    ->map(fn($m) => $m->usr_first_name . ' ' . $m->usr_last_name)
                                    ->implode(', ');
                            }
                        @endphp

                        <div class="card-header d-flex align-items-center py-2">
                            {{-- Back link on mobile --}}
                            <a href="{{ url('messages') }}" class="mr-2 d-md-none text-secondary">
                                <i class="fas fa-arrow-left"></i>
                            </a>

                            {{-- Avatar --}}
                            <div class="flex-shrink-0 mr-2">
                                @if ($isGroup && !empty($groupInfo->mesg_group_image))
                                    <img src="{{ asset('images/messages/group_images/' . $groupInfo->mesg_group_image) }}"
                                        class="img-circle elevation-1" style="width:38px;height:38px;object-fit:cover;"
                                        alt="Group">
                                @elseif ($isGroup)
                                    <span
                                        class="img-circle elevation-1 d-flex align-items-center justify-content-center bg-secondary text-white"
                                        style="width:38px;height:38px;font-size:16px;">
                                        <i class="fas fa-users"></i>
                                    </span>
                                @else
                                    @php $firstOtherMember = $otherMembersAll->first(); @endphp
                                    <img src="{{ asset(getAvatar($firstOtherMember->usr_id ?? $currentUserId)) }}"
                                        class="img-circle elevation-1" style="width:38px;height:38px;object-fit:cover;"
                                        alt="User">
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <strong>{{ $chatTitle }}</strong>
                                @if ($isGroup)
                                    <small class="text-muted d-block">
                                        {{ $members->count() }} members
                                    </small>
                                @endif
                            </div>
                        </div>

                        {{-- Message thread --}}
                        <div class="card-body p-3" id="chatMessages" style="overflow-y:auto; flex:1; background:#f4f6f9;">

                            {{-- Older messages pagination --}}
                            @if ($messages->hasPages() && $messages->currentPage() > 1)
                                <div class="text-center mb-3">
                                    <a href="{{ $messages->previousPageUrl() }}&anchor=top"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-chevron-up mr-1"></i> Load older messages
                                    </a>
                                </div>
                            @endif

                            @forelse ($messages as $msg)
                                @php $isMine = $msg->mes_created_by == $currentUserId; @endphp

                                <div class="d-flex mb-3 {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}">

                                    {{-- Other user avatar --}}
                                    @if (!$isMine)
                                        <div class="flex-shrink-0 mr-2 align-self-end">
                                            <img src="{{ asset(getAvatar($msg->mes_created_by)) }}" class="img-circle"
                                                style="width:32px;height:32px;object-fit:cover;" alt="">
                                        </div>
                                    @endif

                                    <div style="max-width:65%; display:flex; flex-direction:column; align-items: {{ $isMine ? 'flex-end' : 'flex-start' }};">
    {{-- Sender name for group chats (other users only) --}}
    @if ($isGroup && !$isMine)
        <small class="text-muted d-block mb-1 ml-1">
            {{ $msg->usr_first_name }}
        </small>
    @endif

    <div class="msg-bubble {{ $isMine ? 'msg-bubble-mine' : 'msg-bubble-other' }}" style="text-align:left;">
        {{ $msg->mes_content }}
    </div>

                                        <small
                                            class="text-muted d-block mt-1 {{ $isMine ? 'text-right' : 'text-left' }} ml-1 mr-1">
                                            {{ \Carbon\Carbon::parse($msg->mes_date_created)->format('M j, g:i A') }}
                                        </small>
                                    </div>

                                    {{-- My avatar --}}
                                    @if ($isMine)
                                        <div class="flex-shrink-0 ml-2 align-self-end">
                                            <img src="{{ asset(getAvatar($currentUserId)) }}" class="img-circle"
                                                style="width:32px;height:32px;object-fit:cover;" alt="">
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-comment-slash fa-2x mb-2 d-block" style="color:#ced4da;"></i>
                                    No messages yet. Say hello!
                                </div>
                            @endforelse

                            {{-- Load more --}}
                            @if ($messages->hasMorePages())
                                <div class="text-center mt-3">
                                    <a href="{{ $messages->nextPageUrl() }}" class="btn btn-sm btn-outline-secondary">
                                        Load more
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Message input form --}}
                        <div class="card-footer p-2">
                            <form method="POST" action="{{ url('messages/send') }}"
                                class="d-flex align-items-end gap-2" id="sendForm">
                                @csrf
                                <input type="hidden" name="mesg_group_id" value="{{ $mesg_group_id }}">

                                <textarea name="mes_content" id="messageInput" class="form-control mr-2" placeholder="Type a message…"
                                    rows="1" style="resize:none; overflow:hidden;" required></textarea>

                                <button type="submit" class="btn btn-primary flex-shrink-0" style="height:38px;">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- COMPOSE MODAL --}}
    <div class="modal fade" id="composeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ url('messages/compose') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit mr-1"></i> New Message
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipients">To <span class="text-danger">*</span></label>
                            <select name="recipients[]" id="recipients" class="form-control select2" multiple="multiple"
                                style="width:100%;" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->usr_id }}">
                                        {{ $user->usr_first_name }}
                                        @if (!empty($user->usr_middle_name))
                                            {{ $user->usr_middle_name }}
                                        @endif
                                        {{ $user->usr_last_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select multiple recipients for a group message.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="compose_content">Message <span class="text-danger">*</span></label>
                            <textarea name="mes_content" id="compose_content" class="form-control" rows="4"
                                placeholder="Type your message…" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection