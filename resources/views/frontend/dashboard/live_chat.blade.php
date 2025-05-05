@extends('frontend.frontend_dashboard')
@section('main')

<!-- Include CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/chat.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Enhanced Chat UI Styles */
    .chat-container {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        height: 80vh;
        display: flex;
        flex-direction: row;
    }

    /* Ensure the chat area takes full height */
    .chat-area-column {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .chat-list-column {
        background-color: #f8f9fa;
        border-right: 1px solid #e9ecef;
    }

    .chat-section-title {
        padding: 20px;
        margin: 0;
        font-weight: 600;
        color: #333;
        border-bottom: 1px solid #e9ecef;
        background-color: #fff;
    }

    .chat-list {
        overflow-y: auto;
        padding: 10px;
    }

    .list-group-item {
        border: none;
        border-radius: 10px !important;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        padding: 12px;
    }

    .list-group-item:hover {
        background-color: #e9ecef;
    }

    .list-group-item.active {
        background-color: #0d6efd !important;
        color: white !important;
    }

    .list-group-item.active .text-muted {
        color: rgba(255,255,255,0.7) !important;
    }

    .agent-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        flex-shrink: 0;
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        box-shadow: 0 3px 5px rgba(0,0,0,0.1);
    }

    .chat-header {
        padding: 20px;
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
    }

    .chat-message-area {
        padding: 20px;
        background-color: #f8f9fa;
        overflow-y: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        max-height: calc(80vh - 130px); /* Adjust based on header and input area heights */
    }

    .message-bubble {
        max-width: 75%;
        padding: 12px 16px;
        margin-bottom: 15px;
        border-radius: 18px;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        word-break: break-word;
    }

    .message-sent {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }

    .message-received {
        background-color: white;
        color: #333;
        margin-right: auto;
        border-bottom-left-radius: 4px;
        border: 1px solid #e9ecef;
    }

    .message-meta {
        font-size: 0.7rem;
        margin-top: 6px;
        opacity: 0.8;
    }

    .chat-input-area {
        padding: 15px 20px;
        background-color: #fff;
        border-top: 1px solid #e9ecef;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }

    .chat-input-area .input-group {
        background-color: #fff;
        border-radius: 30px;
        padding: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
    }

    .chat-input-area .form-control {
        border-radius: 30px;
        padding: 12px 20px;
        border: none;
        background-color: #fff;
        height: auto;
    }

    .chat-input-area .form-control:focus {
        box-shadow: none;
        outline: none;
    }

    .chat-input-area .btn {
        border-radius: 50%;
        width: 45px;
        height: 45px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-right: 5px;
    }

    .typing-indicator {
        display: none;
        background-color: white;
        padding: 8px 15px;
        border-radius: 18px;
        margin-bottom: 10px;
        width: auto;
        max-width: 100px;
        align-self: flex-start;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #0d6efd;
        opacity: 0.5;
    }

    /* Animation for messages */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message-bubble {
        animation: fadeIn 0.3s ease;
    }
</style>

<!-- Chat Interface -->
<div class="page-content">
    <div class="row profile-body">
        @include('frontend.dashboard.dashboard_sidebar')

        <div class="col-md-8 col-xl-9 middle-wrapper">
            <!-- Chat Interface Wrapper -->
            <div id="user-chat-interface" class="chat-container row g-0">
                <!-- Agent List Sidebar -->
                <div class="col-md-4 col-lg-4 chat-list-column d-flex flex-column p-0">
                    <h6 class="chat-section-title">
                        <i class="fas fa-comments me-2"></i>Conversations
                    </h6>
                    <div class="chat-list flex-grow-1">
                        <ul class="list-group list-group-flush" id="agent-list">
                            <li class="list-group-item text-muted text-center" id="loading-agents">
                                <div class="d-flex justify-content-center">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Loading conversations...
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Chat Area Column -->
                <div class="col-md-8 col-lg-8 chat-area-column p-0">
                    <div class="chat-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0" id="selected-user-name">
                            <i class="fas fa-user-circle me-2"></i>Select a Conversation
                        </h6>
                        <input type="hidden" id="selected-user-id" value="">
                    </div>

                    <div class="chat-message-area flex-grow-1" id="chat-message-area">
                        <div class="empty-state" id="chat-placeholder">
                            <i class="fas fa-comments"></i>
                            <h5>Start a Conversation</h5>
                            <p class="text-muted mb-2">Select an agent from the list to start chatting.</p>
                            <p class="small text-muted">If you don't see any conversations, contact an agent from a property listing.</p>
                        </div>
                        <div class="typing-indicator" id="typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="chat-input-area">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type your message here..." id="chat-message-input" disabled>
                            <button class="btn btn-primary" type="button" id="send-message-button" disabled>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS Includes -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    let selectedUserId = null;
    const currentUserId = {{ Auth::id() }};
    let selectedUserName = '';
    let currentPropertyId = null;

    function scrollToBottom() {
        const chatArea = document.getElementById('chat-message-area');
        if (chatArea) chatArea.scrollTop = chatArea.scrollHeight;
    }

    function formatTime(dateTimeString) {
        const date = new Date(dateTimeString);
        return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
    }

    function appendMessage(message, isNew = false) {
        const chatArea = $('#chat-message-area');
        const isSentByMe = message.sender_id === currentUserId;

        const messageClass = isSentByMe ? 'message-sent' : 'message-received';
        const alignClass = isSentByMe ? 'text-start' : 'text-end';
        const senderName = isSentByMe ? 'You' : selectedUserName;

        // Format the message content with emojis and links
        let formattedMsg = message.msg;

        // Convert URLs to clickable links
        formattedMsg = formattedMsg.replace(
            /(https?:\/\/[^\s]+)/g,
            '<a href="$1" target="_blank" class="text-decoration-underline">$1</a>'
        );

        const messageHtml = `
            <div class="message-bubble ${messageClass}" id="msg_${message.id || 'temp_' + Date.now()}">
                <div class="message-content">${formattedMsg}</div>
                <div class="message-meta ${alignClass}">
                    <span class="sender-name">${senderName}</span>
                    <span class="message-time">${formatTime(message.created_at)}</span>
                </div>
            </div>`;

        chatArea.append(messageHtml);
        if (isNew) scrollToBottom();
    }

    function displayMessages(messages, userName) {
        const chatArea = $('#chat-message-area');
        chatArea.empty();

        if (messages && messages.length > 0) {
            // Group messages by date
            let currentDate = null;

            messages.forEach(msg => {
                // Check if we need to add a date separator
                const messageDate = new Date(msg.created_at).toLocaleDateString();
                if (messageDate !== currentDate) {
                    currentDate = messageDate;

                    // Format the date nicely
                    const today = new Date().toLocaleDateString();
                    const yesterday = new Date();
                    yesterday.setDate(yesterday.getDate() - 1);
                    const yesterdayStr = yesterday.toLocaleDateString();

                    let dateDisplay = messageDate;
                    if (messageDate === today) {
                        dateDisplay = 'Today';
                    } else if (messageDate === yesterdayStr) {
                        dateDisplay = 'Yesterday';
                    }

                    // Add date separator
                    chatArea.append(`
                        <div class="text-center my-3">
                            <span class="badge bg-light text-dark px-3 py-2 shadow-sm">
                                ${dateDisplay}
                            </span>
                        </div>
                    `);
                }

                appendMessage(msg);
            });
        } else {
            // Show an empty state with a nice illustration
            chatArea.append(`
                <div class="empty-state">
                    <i class="fas fa-comment-dots"></i>
                    <h5>No messages yet</h5>
                    <p class="text-muted">Start the conversation by sending a message below!</p>
                </div>
            `);
        }
        scrollToBottom();
    }

    function sendMessage() {
        const messageInput = $('#chat-message-input');
        const sendButton = $('#send-message-button');
        const message = messageInput.val().trim();
        const receiverId = $('#selected-user-id').val();

        // Validate input
        if (!message || !receiverId) return;

        // Disable controls to prevent duplicate sends
        sendButton.prop('disabled', true);
        messageInput.prop('disabled', true);

        // Create a temporary message ID to track this message
        const tempMsgId = 'temp_' + Date.now();

        // Immediately show message in UI with sending indicator
        const tempMsg = {
            id: tempMsgId,
            sender_id: currentUserId,
            receiver_id: receiverId,
            msg: message,
            created_at: new Date().toISOString()
        };

        // Add message to UI
        appendMessage(tempMsg, true);

        // Add sending indicator
        $(`#msg_${tempMsgId} .message-meta`).append('<span class="sending-status ms-2"><i class="fas fa-circle-notch fa-spin"></i></span>');

        // Clear input field
        messageInput.val('');

        // Re-enable input but keep focus
        messageInput.prop('disabled', false).focus();

        // Send to backend
        $.ajax({
            url: '{{ route("send.msg") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                receiver_id: receiverId,
                property_id: currentPropertyId,
                msg: message
            },
            success: function(data) {
                console.log("Message sent:", data);

                // Update the sending indicator to show success
                $(`#msg_${tempMsgId} .sending-status`).html('<i class="fas fa-check text-success"></i>').fadeOut(2000);

                // Re-enable the send button
                sendButton.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error("Failed to send message:", error);

                // Update the sending indicator to show error with retry option
                $(`#msg_${tempMsgId} .sending-status`).html(`
                    <span class="text-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <a href="#" onclick="resendMessage('${tempMsgId}', '${message.replace(/'/g, "\\'")}', ${receiverId}, ${currentPropertyId || 'null'}); return false;">
                            Retry
                        </a>
                    </span>
                `);

                // Re-enable the send button
                sendButton.prop('disabled', false);
            }
        });
    }

    // Function to resend a failed message
    function resendMessage(msgId, message, receiverId, propertyId) {
        // Update the sending indicator
        $(`#msg_${msgId} .sending-status`).html('<i class="fas fa-circle-notch fa-spin"></i>');

        // Send to backend
        $.ajax({
            url: '{{ route("send.msg") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                receiver_id: receiverId,
                property_id: propertyId,
                msg: message
            },
            success: function(data) {
                console.log("Message resent successfully:", data);

                // Update the sending indicator to show success
                $(`#msg_${msgId} .sending-status`).html('<i class="fas fa-check text-success"></i>').fadeOut(2000);
            },
            error: function(xhr, status, error) {
                console.error("Failed to resend message:", error);

                // Update the sending indicator to show error with retry option
                $(`#msg_${msgId} .sending-status`).html(`
                    <span class="text-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <a href="#" onclick="resendMessage('${msgId}', '${message.replace(/'/g, "\\'")}', ${receiverId}, ${propertyId || 'null'}); return false;">
                            Retry
                        </a>
                    </span>
                `);
            }
        });
    }

    function selectUser(userId, userName, propertyId = null) {
        // Update global variables
        selectedUserId = userId;
        selectedUserName = userName;
        currentPropertyId = propertyId;

        // Update UI elements
        $('#selected-user-id').val(userId);
        $('#selected-user-name').html(`<i class="fas fa-user-circle me-2"></i> ${userName}`);

        // Update active state in the sidebar
        $('.list-group-item').removeClass('active');
        $('#agent-' + userId).addClass('active');

        // Enable input controls
        $('#chat-message-input').prop('disabled', false).val('').focus();
        $('#send-message-button').prop('disabled', false);

        // Hide placeholder and show loading animation
        $('#chat-placeholder').hide();
        $('#chat-message-area').html(`
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-muted">Loading conversation...</p>
                </div>
            </div>
        `);

        // Fetch and display messages
        $.ajax({
            url: '{{ url("/user-message") }}/' + userId,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                displayMessages(data.messages || []);

                // Remove any unread badge from the sidebar
                const unreadBadge = $('#agent-' + userId).find('.badge');
                if (unreadBadge.length) {
                    unreadBadge.fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            },
            error: function(xhr, status, error) {
                // Show error message
                $('#chat-message-area').html(`
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        <h5>Failed to load messages</h5>
                        <p class="text-muted mb-3">There was a problem loading your conversation.</p>
                        <button class="btn btn-outline-primary" onclick="selectUser(${userId}, '${userName.replace(/'/g, "\\'")}', ${propertyId || 'null'})">
                            <i class="fas fa-sync-alt me-1"></i> Try Again
                        </button>
                    </div>
                `);
                console.error("Error loading messages:", error);
            }
        });
    }

    function loadAgentList() {
        const agentList = $('#agent-list');
        agentList.empty().append(`
            <li class="list-group-item text-muted text-center">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Loading conversations...
                </div>
            </li>
        `);

        $.ajax({
            url: '/user-all',
            method: 'GET',
            dataType: 'json',
            success: function(agents) {
                agentList.empty();
                if (agents.length === 0) {
                    agentList.append(`
                        <li class="list-group-item text-center">
                            <div class="empty-state py-4">
                                <i class="fas fa-comments"></i>
                                <p class="mb-0">No conversations yet</p>
                                <small class="text-muted">Contact an agent from a property listing</small>
                            </div>
                        </li>
                    `);
                    return;
                }

                agents.forEach(agent => {
                    // Format last message preview
                    let lastMessagePreview = 'No messages yet';
                    let timeAgo = '';

                    if (agent.last_message && agent.last_message.msg) {
                        // Truncate message if too long
                        lastMessagePreview = agent.last_message.msg.length > 30
                            ? agent.last_message.msg.substring(0, 30) + '...'
                            : agent.last_message.msg;

                        // Add time ago
                        const msgDate = new Date(agent.last_message.created_at);
                        const now = new Date();
                        const diffMs = now - msgDate;
                        const diffMins = Math.floor(diffMs / 60000);

                        if (diffMins < 1) {
                            timeAgo = 'Just now';
                        } else if (diffMins < 60) {
                            timeAgo = `${diffMins}m ago`;
                        } else if (diffMins < 1440) {
                            timeAgo = `${Math.floor(diffMins/60)}h ago`;
                        } else {
                            timeAgo = `${Math.floor(diffMins/1440)}d ago`;
                        }
                    }

                    // Create unread badge if needed
                    const unreadBadge = agent.unread_count > 0
                        ? `<span class="badge bg-danger rounded-pill">${agent.unread_count}</span>`
                        : '';

                    // Determine if this agent is online (for UI purposes)
                    const isOnline = Math.random() > 0.5; // Random for demo
                    const onlineStatus = isOnline
                        ? '<span class="position-absolute top-0 start-0 translate-middle p-1 bg-success border border-light rounded-circle" style="margin-left: 35px;"></span>'
                        : '';

                    const listItem = `
                        <li class="list-group-item list-group-item-action"
                            onclick="selectUser(${agent.id}, '${agent.name.replace(/'/g, "\\'")}', ${agent.property_id || 'null'})"
                            id="agent-${agent.id}">
                            <div class="d-flex align-items-center">
                                <div class="position-relative">
                                    <div class="agent-avatar me-3">
                                        ${agent.name.charAt(0).toUpperCase()}
                                    </div>
                                    ${onlineStatus}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-bold">${agent.name}</div>
                                        <small class="text-muted ms-2">${timeAgo}</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted text-truncate">${lastMessagePreview}</small>
                                        ${unreadBadge}
                                    </div>
                                </div>
                            </div>
                        </li>`;
                    agentList.append(listItem);
                });
            },
            error: function() {
                agentList.empty().append(`
                    <li class="list-group-item text-center">
                        <div class="py-4">
                            <i class="fas fa-exclamation-circle text-danger mb-3" style="font-size: 2rem;"></i>
                            <p class="text-danger mb-1">Failed to load conversations</p>
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadAgentList()">
                                <i class="fas fa-sync-alt me-1"></i> Try Again
                            </button>
                        </div>
                    </li>
                `);
            }
        });
    }

    $(document).ready(function() {
        // Load agents on page load
        loadAgentList();

        // Check if we have a specific agent to auto-select (from property page)
        @if(isset($agentUser) && isset($propId))
            console.log("Auto-selecting agent from property page:", {{ $agentUser->id }}, "{{ $agentUser->name }}", {{ $propId }});
            // We'll select this agent once the agent list is loaded
            const autoSelectAgentId = {{ $agentUser->id }};
            const autoSelectAgentName = "{{ $agentUser->name }}";
            const autoSelectPropertyId = {{ $propId }};

            // Set a small delay to ensure the agent list is loaded
            setTimeout(function() {
                // Check if the agent is in the list
                if ($('#agent-' + autoSelectAgentId).length) {
                    // Agent is in the list, select them
                    selectUser(autoSelectAgentId, autoSelectAgentName, autoSelectPropertyId);
                } else {
                    // Agent not in list yet, but we know they exist because we have a welcome message
                    // Force select them anyway
                    selectUser(autoSelectAgentId, autoSelectAgentName, autoSelectPropertyId);

                    // Reload the agent list to make sure they appear
                    setTimeout(loadAgentList, 1000);
                }
            }, 500);
        @endif

        // Handle send message
        $('#send-message-button').on('click', sendMessage);
        $('#chat-message-input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Optional: Real-time updates with Echo
        if (typeof Echo !== 'undefined') {
            Echo.private(`chat.${currentUserId}`).listen('.chat-message', (e) => {
                if (selectedUserId === e.message.sender_id) {
                    appendMessage(e.message, true);
                } else {
                    loadAgentList(); // Refresh agent list if message is from another agent
                }
            });
        }
    });
</script>
@endsection