<style>
    /* Conversation list hover */
    .msg-list-item {
        transition: background .15s;
    }

    .msg-list-item:hover {
        background: #f8f9fa;
    }

    /* Truncate display name to keep uniform height */
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

    /* Make sidebar fill viewport nicely */
    #msg-sidebar .card,
    #msg-main-panel .card {
        border-radius: 0;
        border-top: none;
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
                        <li class="breadcrumb-item active">Messages</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        @include('layouts.partials.onclick')
        @include('layouts.partials.alerts')

        <div class="container-fluid">
            <div class="row">

                {{-- LEFT PANEL --}}
                <div class="col-md-4 col-lg-3 px-0" id="msg-sidebar">
                    <div class="card card-primary card-outline h-100 mb-0">

                        {{-- Sidebar header --}}
                        <div class="card-header d-flex align-items-center justify-content-between py-2">
                            <span class="font-weight-bold">Chats</span>
                            <a href="#" data-toggle="modal" data-target="#composeModal" class="btn btn-primary btn-sm"
                                title="New Message">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>

                        {{-- Search --}}
                        <div class="card-header p-2">
                            <form method="GET" action="{{ url('messages') }}">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search messages..." value="{{ $search }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Conversation list --}}
                        <div class="card-body p-0" style="overflow-y:auto; max-height:calc(100vh - 260px);">
                            @php $currentUserId = session('usr_id'); @endphp

                            @forelse ($conversations as $conv)
                                @php
                                    $grpId = $conv->mesg_group_id;
                                    $grpMembers = $members[$grpId] ?? collect();
                                    $otherMembers = $grpMembers->where('usr_id', '!=', $currentUserId);
                                    $isGroup = $grpMembers->count() > 2 || !empty($conv->mesg_group_name);

                                    // Build display name
                                    if (!empty($conv->mesg_group_name)) {
                                        $displayName = $conv->mesg_group_name;
                                    } else {
                                        $nameParts = $otherMembers
                                            ->map(fn($m) => $m->usr_first_name . ' ' . $m->usr_last_name)
                                            ->toArray();
                                        $displayName = implode(', ', $nameParts);
                                    }

                                    // Build preview sub-line
                                    $isSelf = $conv->sender_id == $currentUserId;
                                    $preview =
                                        $isGroup && !$isSelf
                                            ? $conv->sender_name . ': ' . $conv->mes_content
                                            : ($isSelf
                                                ? 'You: ' . $conv->mes_content
                                                : $conv->mes_content);
                                @endphp

                                <a href="{{ url('messages/chat/' . $grpId) }}"
                                    class="msg-list-item d-flex align-items-center px-3 py-2 text-dark text-decoration-none border-bottom">

                                    {{-- Avatar --}}
                                    <div class="flex-shrink-0 mr-2">
                                        @if ($isGroup && !empty($conv->mesg_group_image))
                                            <img src="{{ asset('images/messages/group_images/' . $conv->mesg_group_image) }}"
                                                class="img-circle elevation-1"
                                                style="width:40px;height:40px;object-fit:cover;" alt="Group">
                                        @elseif ($isGroup)
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

                                    {{-- Name + preview --}}
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="font-weight-bold text-truncate msg-display-name">
                                            {{ $displayName }}
                                        </div>
                                        <small class="text-muted text-truncate d-block msg-preview">
                                            {{ $preview }}
                                        </small>
                                    </div>

                                    {{-- Time --}}
                                    <div class="flex-shrink-0 ml-2 text-muted" style="font-size:11px;white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($conv->last_message_date)->format('M j') }}
                                    </div>
                                </a>
                            @empty
                                <div class="p-3 text-muted text-center">
                                    @if (!empty($search))
                                        No conversations found for "<strong>{{ $search }}</strong>".
                                    @else
                                        No conversations yet.
                                    @endif
                                </div>
                            @endforelse
                        </div>

                        {{-- Pagination --}}
                        @if (empty($search) &&
                                $conversations instanceof \Illuminate\Pagination\LengthAwarePaginator &&
                                $conversations->hasPages())
                            <div class="card-footer p-2">
                                {{ $conversations->links() }}
                            </div>
                        @endif

                    </div>
                </div>

                {{-- RIGHT PANEL --}}
                <div class="col-md-8 col-lg-9 px-0 d-none d-md-flex flex-column" id="msg-main-panel">
                    <div class="card h-100 mb-0 d-flex align-items-center justify-content-center" style="min-height:500px;">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-comments fa-4x mb-3 d-block" style="color:#ced4da;"></i>
                            <h5 class="mb-1">Your Messages</h5>
                            <p class="mb-3">Select a conversation or start a new one.</p>
                            <a href="#" data-toggle="modal" data-target="#composeModal" class="btn btn-primary">
                                <i class="fas fa-edit mr-1"></i> New Message
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- COMPOSE MODAL --}}
    <div class="modal fade" id="composeModal" tabindex="-1" role="dialog" aria-labelledby="composeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ url('messages/compose') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="composeModalLabel">
                            <i class="fas fa-edit mr-1"></i> New Message
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        {{-- Recipients --}}
                        <div class="form-group">
                            <label for="recipients">To <span class="text-danger">*</span></label>
                            <select name="recipients[]" id="recipients" class="select3 form-control" multiple="multiple"
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
                            <small class="form-text text-muted">
                                You can select multiple recipients for a group message.
                            </small>
                        </div>

                        {{-- Message content --}}
                        <div class="form-group mb-0">
                            <label for="mes_content">Message <span class="text-danger">*</span></label>
                            <textarea name="mes_content" id="mes_content" class="form-control" rows="4"
                                placeholder="Type your message..." required></textarea>
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