@extends('agent.agent_dashboard')
@section('agent')
<link rel="stylesheet" href="{{ asset('frontend/assets/css/chat.css') }}">
{{-- Include jQuery if not already globally available --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
{{-- Include Font Awesome for icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Dark Theme Adjustments */
    .chat-list {
        height: 70vh;
        overflow-y: auto;
    }
    .chat-message-area {
        height: calc(70vh - 70px); /* Adjust height considering input area */
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        padding: 15px;
        scroll-behavior: smooth;
        background-color: #1f2940; /* Darker background */
    }
    .message-bubble {
        padding: 8px 14px;
        border-radius: 18px;
        margin-bottom: 8px;
        max-width: 75%;
        word-wrap: break-word;
        box-shadow: 0 1px 1px rgba(0,0,0,0.15);
        color: #e0e0e0; /* Default light text for bubbles */
    }

    /* Enhanced User List Item Styling */
    .user-list-item {
        cursor: pointer !important;
        margin-bottom: 8px !important;
        border-radius: 8px !important;
        transition: all 0.2s ease;
        background-color: #2c3b5a;
        border: 1px solid #3a4d76 !important;
        color: #e0e0e0;
        padding: 12px !important;
        position: relative;
        overflow: hidden;
    }

    .user-list-item:hover {
        background-color: #3a4d76;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        border-color: #4a5d86 !important;
    }

    .user-list-item:active {
        transform: translateY(0);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .user-list-item.active {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: white !important;
    }

    .user-list-item.active .text-muted {
        color: rgba(255,255,255,0.8) !important;
    }

    /* Add a subtle highlight effect */
    .user-list-item::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(255,255,255,0.05), transparent);
        pointer-events: none;
    }
    .message-sent {
        background-color: #0d6efd; /* Use primary blue for sent */
        color: #ffffff; /* White text for sent */
        align-self: flex-end;
        margin-left: auto;
    }
    .message-received {
        background-color: #2c3248; /* Slightly lighter dark bg */
        /* border: 1px solid #3a415a; Remove border or make it subtle */
        align-self: flex-start;
        margin-right: auto;
    }
    .message-meta {
        font-size: 0.7rem;
        color: #adb5bd; /* Lighter gray for meta */
        margin-top: 4px;
        display: block;
    }
    .message-sent .message-meta {
         color: #cce5ff; /* Lighter meta for sent */
    }
     .message-received .message-meta {
         color: #adb5bd; /* Lighter meta for received */
    }
    .chat-list .list-group-item {
         border: none;
         padding: 10px 15px;
         background-color: transparent; /* Make list items transparent */
         color: #adb5bd; /* Default text color */
         margin-bottom: 2px;
         border-radius: 5px;
    }
    .chat-list .list-group-item.active {
        background-color: #0d6efd !important; /* Keep primary blue highlight */
        color: white !important;
    }
     .chat-list .list-group-item.active small {
        color: #e0e0e0 !important;
     }
     .chat-list .list-group-item:hover {
         background-color: #2c3248; /* Hover effect */
         color: #f0f0f0;
     }
     .chat-list .list-group-item img {
         width: 45px;
         height: 45px;
     }
     .chat-input-area {
        background-color: #2c3248; /* Darker input area */
        border-top: 1px solid #3a415a !important; /* Adjust border */
     }
     .chat-input-area .form-control {
         background-color: #1f2940;
         color: #e0e0e0;
         border-color: #3a415a;
     }
      .chat-input-area .form-control::placeholder {
         color: #adb5bd;
     }
     #selected-user-name {
         color: #e0e0e0; /* Light text for header */
     }
     #chat-placeholder {
          color: #adb5bd !important; /* Lighter placeholder text */
     }
</style>

<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" style="padding: 0.5rem;"> {{-- Reduced padding --}}
                    <div class="row">
                        <!-- User List -->
                        <div class="col-md-4 col-lg-3 border-end-lg pe-0 border-secondary"> {{-- Adjust border color --}}
                             <div class="px-3 pt-3">
                                 <h6 class="mb-3 text-light">Chat List</h6> {{-- Lighter heading --}}
                             </div>
                             <div class="chat-list">
                                <ul class="list-group list-group-flush" id="user-list"> {{-- Flush list group --}}
                                    {{-- Replaced static list with dynamic loading --}}
                                    <li class="list-group-item text-muted text-center" id="loading-users">Loading users...</li>
                                </ul>
                             </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="col-md-8 col-lg-9 ps-0"> {{-- Removed padding start --}}
                             <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0" id="selected-user-name">Select a User to Chat</h6>
                                <input type="hidden" id="selected-user-id" value="">
                                {{-- Maybe add user status or other info here --}}
                             </div>
                             <div class="chat-message-area" id="chat-message-area">
                                <div class="text-center text-muted p-5" id="chat-placeholder">Select a user from the list to view messages.</div>
                             </div>
                             <!-- Input Area -->
                             <div class="p-3 border-top chat-input-area">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Type your message here..." id="chat-message-input" disabled>
                                    <button class="btn btn-primary" type="button" id="send-message-button" disabled>Send</button>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Laravel Echo & Pusher JS --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

<script>
    // --- Updated JavaScript logic (consistent with Admin/User views) ---
    let selectedUserId = null;
    const currentUserId = {{ Auth::id() }}; // Use currentUserId consistently
    let selectedUserName = '';

    // Function to scroll chat area to bottom
    function scrollToBottom() {
        const chatArea = document.getElementById('chat-message-area');
        chatArea.scrollTop = chatArea.scrollHeight;
    }

    // Function to format message timestamp
    function formatTime(dateTimeString) {
        try {
             return new Date(dateTimeString).toLocaleString([], { hour: 'numeric', minute: '2-digit', month: 'short', day: 'numeric' });
        } catch (e) {
            console.error("Error formatting date:", e);
            return dateTimeString;
        }
    }

    // Function to append a single message
    function appendMessage(message, isNew = false) {
        console.log('[appendMessage] Called with message:', message, 'isNew:', isNew);
        const chatArea = $('#chat-message-area');
        const currentUserId = {{ Auth::id() }}; // Ensure currentUserId is accessible

        if (!message) {
            console.warn("[appendMessage] Skipping invalid message object:", message);
            return;
        }

        // Handle different message object structures
        // 1. Direct message object from database
        // 2. Message wrapped in event data
        // 3. Message with nested user object
        let msgObj = null;

        if (message.msg) {
            // Direct message object
            msgObj = message;
        } else if (message.message && message.message.msg) {
            // Message wrapped in event data (from Echo)
            msgObj = message.message;
        } else if (typeof message === 'object') {
            // Try to find message in any property of the event
            for (const key in message) {
                if (message[key] && typeof message[key] === 'object' && message[key].msg) {
                    msgObj = message[key];
                    break;
                }
            }
        }

        if (!msgObj || !msgObj.msg) {
            console.warn("[appendMessage] Message data is invalid:", message);
            return;
        }

        // Generate a unique ID for the message to prevent duplicates
        const messageId = msgObj.id ? `msg_${msgObj.id}` : `temp_${Date.now()}`;

        // Check if this message is already displayed
        if (document.getElementById(messageId)) {
            console.log(`[appendMessage] Message with ID ${messageId} already exists in the DOM, skipping.`);
            return;
        }

        // Determine sender name
        let senderName = 'User';
        if (msgObj.sender_id === currentUserId) {
            senderName = 'You';
        } else if (msgObj.user && msgObj.user.name) {
            senderName = msgObj.user.name;
        } else if (selectedUserName) {
            senderName = selectedUserName;
        }
        console.log('[appendMessage] Sender determined as:', senderName);

        const messageTime = formatTime(msgObj.created_at);
        let messageHtml = '';

        if (msgObj.sender_id === currentUserId) {
            messageHtml = `
                <div class="message-bubble message-sent" id="${messageId}">
                    <div>${msgObj.msg}</div>
                    <div class="message-meta text-start">You - ${messageTime}</div>
                </div>`;
        } else {
            messageHtml = `
                <div class="message-bubble message-received" id="${messageId}">
                    <div>${msgObj.msg}</div>
                    <div class="message-meta text-end">${senderName} - ${messageTime}</div>
                </div>`;
        }

        console.log('[appendMessage] Generated HTML:', messageHtml);

        try {
            chatArea.append(messageHtml); // Append the generated HTML
            console.log('[appendMessage] HTML appended successfully.');
            if (isNew) {
                scrollToBottom(); // Scroll only for new messages
                console.log('[appendMessage] Scrolled to bottom.');
            }
        } catch (error) {
             console.error('[appendMessage] Error appending HTML:', error);
        }
    }

    // Function to display messages from fetched history
    function displayMessages(messages, userName) {
        const chatArea = $('#chat-message-area');
        chatArea.empty();

        if (messages && messages.length > 0) {
            messages.forEach(function(message) {
                appendMessage(message, false);
            });
        } else {
             chatArea.append('<div class="text-center text-muted p-3">No messages yet. Start the conversation!</div>');
        }
        scrollToBottom();
    }

    // Function to send message
    function sendMessage() {
        const messageInput = $('#chat-message-input');
        const sendButton = $('#send-message-button');
        const message = messageInput.val().trim();
        const receiverId = $('#selected-user-id').val();

        if (message === '' || !receiverId) {
            return;
        }

        // Disable controls to prevent duplicate sends
        sendButton.prop('disabled', true);
        messageInput.prop('disabled', true);

        console.log(`Sending message: "${message}" to user ID: ${receiverId}`);

        // Create a temporary message ID
        const tempMsgId = 'temp_' + Date.now();

        // Add message to UI immediately
        $('#chat-message-area').append(`
            <div class="message-bubble message-sent" id="${tempMsgId}">
                <div class="message-content">${message}</div>
                <div class="message-meta text-start">
                    <span class="sender-name">You</span>
                    <span class="message-time">Just now</span>
                    <span class="sending-status">(Sending...)</span>
                </div>
            </div>
        `);

        // Scroll to bottom to show the new message
        scrollToBottom();

        // Clear input field
        messageInput.val('');

        $.ajax({
            url: '/send-message',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                receiver_id: receiverId,
                msg: message
            },
            dataType: 'json',
            success: function(data) {
                console.log("Message sent successfully:", data);

                // Update the temporary message status
                $(`#${tempMsgId} .sending-status`).html('').hide();

                // Re-enable controls
                sendButton.prop('disabled', false);
                messageInput.prop('disabled', false).focus();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error sending message:', textStatus, errorThrown, jqXHR.responseText);

                // Try to parse the error response
                let errorMessage = 'Failed to send message. Please try again.';
                try {
                    const response = JSON.parse(jqXHR.responseText);
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                    if (response && response.error) {
                        console.error('Server error details:', response.error);
                    }
                } catch (e) {
                    console.error('Could not parse error response:', e);
                }

                // Show error in the message with retry option
                $(`#${tempMsgId} .sending-status`).html(`(Failed to send - <a href="#" onclick="resendMessage('${tempMsgId}', '${message.replace(/'/g, "\\'")}', ${receiverId}); return false;">Retry</a>)`).addClass('text-danger');

                // Re-enable controls
                sendButton.prop('disabled', false);
                messageInput.prop('disabled', false).focus();
            }
        });
    }

    // Function to resend a failed message
    function resendMessage(msgId, message, receiverId) {
        const sendButton = $('#send-message-button');
        const messageInput = $('#chat-message-input');

        // Disable the send button during resend to prevent multiple attempts
        sendButton.prop('disabled', true);

        $(`#${msgId} .sending-status`).html('(Sending...)').removeClass('text-danger');

        $.ajax({
            url: '/send-message',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                receiver_id: receiverId,
                msg: message
            },
            dataType: 'json',
            success: function(data) {
                console.log("Message resent successfully:", data);

                // Clear the sending status
                $(`#${msgId} .sending-status`).html('').hide();

                // Re-enable the send button
                sendButton.prop('disabled', false);
                messageInput.prop('disabled', false).focus();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error resending message:', textStatus, errorThrown, jqXHR.responseText);
                $(`#${msgId} .sending-status`).html(`(Failed to send - <a href="#" onclick="resendMessage('${msgId}', '${message.replace(/'/g, "\\'")}', ${receiverId}); return false;">Retry</a>)`).addClass('text-danger');

                // Re-enable the send button
                sendButton.prop('disabled', false);
                messageInput.prop('disabled', false).focus();
            }
        });
    }

    // Function to select user and load messages
    function selectUser(userId, userName) {
        if (selectedUserId === userId) {
            return;
        }
        console.log('Selecting user:', userId, userName);

        // Validate parameters
        if (!userId || !userName) {
            console.error('Invalid parameters for selectUser:', { userId, userName });
            return;
        }

        // Clear any existing fallback polling
        if (fallbackPollingInterval) {
            clearInterval(fallbackPollingInterval);
            fallbackPollingInterval = null;
        }

        // Update global variables
        selectedUserId = userId;
        selectedUserName = userName;

        // Update UI elements
        $('#selected-user-id').val(userId);
        $('#selected-user-name').text('Chat with ' + userName);

        // Update active state in the sidebar
        $('.user-list-item').removeClass('active');
        const userElement = $('#user-' + userId);

        if (userElement.length) {
            userElement.addClass('active');
            console.log('User element found and activated:', userElement.attr('id'));
        } else {
            console.warn('User element not found for ID:', userId);
        }

        // Enable input controls
        $('#chat-message-input').prop('disabled', false).focus();
        $('#send-message-button').prop('disabled', false);

        // Show loading state
        $('#chat-placeholder').hide();
        $('#chat-message-area').empty().append(`
            <div class="text-center text-muted p-5">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <p>Loading messages...</p>
                <div class="mt-3 d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-primary" onclick="loadMessagesManually(${userId})">
                        <i class="fas fa-sync-alt me-1"></i> Try Alternative Load
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="loadMessagesDirectly(${userId})">
                        <i class="fas fa-external-link-alt me-1"></i> View API Response
                    </button>
                </div>
                <small class="d-block mt-2 text-muted">
                    If messages don't load automatically, try the alternative loading method.
                </small>
            </div>
        `);

        // Log the URL we're calling for debugging
        const messageUrl = '/user-message/' + userId;
        console.log('Fetching messages from URL:', messageUrl);

        // Add a fallback method to load messages if AJAX fails
        window.loadMessagesDirectly = function(userId) {
            // Open the API response in a new tab
            window.open('/user-message/' + userId, '_blank');
        };

        // Add a method to manually load messages from the API response
        window.loadMessagesManually = function(userId) {
            // Show loading indicator
            $('#chat-message-area').empty().append(`
                <div class="text-center text-muted p-5">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p>Manually loading messages...</p>
                </div>
            `);

            // Use the new direct HTML endpoint
            fetch('/chat-messages/' + userId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Manually loaded messages:", data);
                    if (data.success && data.html) {
                        // Directly insert the pre-rendered HTML
                        $('#chat-message-area').empty().html(data.html);
                        scrollToBottom();

                        // Update the user name in the header
                        if (data.user && data.user.name) {
                            $('#selected-user-name').text('Chat with ' + data.user.name);
                        }
                    } else if (data.user && data.messages) {
                        // Fall back to the old method if HTML is not provided
                        displayMessages(data.messages, data.user.name);
                    } else {
                        $('#chat-message-area').empty().append('<div class="text-center text-danger p-5">Error: Invalid data structure.</div>');
                    }
                })
                .catch(error => {
                    console.error('Error manually loading messages:', error);
                    $('#chat-message-area').empty().append(`
                        <div class="text-center p-5">
                            <div class="text-danger mb-3">
                                <i class="fas fa-exclamation-circle fa-3x"></i>
                            </div>
                            <h5 class="text-danger">Error loading messages</h5>
                            <p class="text-muted">${error.message}</p>
                            <div class="mt-3 d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-primary" onclick="tryLegacyLoad(${userId})">
                                    <i class="fas fa-sync-alt me-1"></i> Try Legacy Method
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="loadMessagesDirectly(${userId})">
                                    <i class="fas fa-external-link-alt me-1"></i> View API Response
                                </button>
                            </div>
                        </div>
                    `);
                });
        };

        // Add a legacy method as a last resort
        window.tryLegacyLoad = function(userId) {
            // Show loading indicator
            $('#chat-message-area').empty().append(`
                <div class="text-center text-muted p-5">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p>Trying legacy loading method...</p>
                </div>
            `);

            // Use the original endpoint
            fetch('/user-message/' + userId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Legacy loaded messages:", data);
                    if (data.user && data.messages) {
                        displayMessages(data.messages, data.user.name);
                    } else {
                        $('#chat-message-area').empty().append('<div class="text-center text-danger p-5">Error: Invalid data structure.</div>');
                    }
                })
                .catch(error => {
                    console.error('Error with legacy loading:', error);
                    $('#chat-message-area').empty().append(`
                        <div class="text-center p-5">
                            <div class="text-danger mb-3">
                                <i class="fas fa-exclamation-circle fa-3x"></i>
                            </div>
                            <h5 class="text-danger">All loading methods failed</h5>
                            <p class="text-muted">${error.message}</p>
                            <button class="btn btn-sm btn-outline-secondary mt-3" onclick="loadMessagesDirectly(${userId})">
                                <i class="fas fa-external-link-alt me-1"></i> View API Response
                            </button>
                        </div>
                    `);
                });
        };

        $.ajax({
            url: messageUrl,
            method: 'GET',
            dataType: 'json',
            cache: false, // Prevent caching
            beforeSend: function(xhr) {
                console.log('Starting AJAX request to load messages');
                // Add a custom header for debugging
                xhr.setRequestHeader('X-Debug-Info', 'Agent-Chat-Load-Messages');
            },
            success: function(data) {
                console.log("Raw response data:", data);

                if (!data) {
                    console.error("Empty response received");
                    $('#chat-message-area').empty().append('<div class="text-center text-danger p-5">Error: Empty response received.</div>');
                    return;
                }

                if (data.error) {
                    console.error("Error in response:", data.error);
                    $('#chat-message-area').empty().append(`<div class="text-center text-danger p-5">Error: ${data.error}</div>`);
                    return;
                }

                if (!data.user) {
                    console.error("No user data in response:", data);
                    $('#chat-message-area').empty().append('<div class="text-center text-danger p-5">Error: User data missing.</div>');
                    return;
                }

                console.log("Messages loaded successfully for user:", data.user.name);
                console.log("Number of messages:", data.messages ? data.messages.length : 0);

                if (data.user && data.messages) {
                    // Check if messages array is empty
                    if (data.messages.length === 0) {
                        $('#chat-message-area').empty().append('<div class="text-center text-muted p-5">No messages yet. Start the conversation!</div>');
                    } else {
                        displayMessages(data.messages, data.user.name);
                    }

                    // If Echo is not connected, set up fallback polling
                    if (!echoConnected) {
                        console.log('Echo not connected, setting up fallback polling');
                        setupFallbackPolling();
                    }
                } else {
                    console.error("Invalid data structure received:", data);
                    $('#chat-message-area').empty().append('<div class="text-center text-danger p-5">Error loading messages: Invalid data structure.</div>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching messages:', textStatus, errorThrown);
                console.error('Response text:', jqXHR.responseText);
                console.error('Status code:', jqXHR.status);

                let errorMessage = 'Error loading messages.';

                try {
                    // Try to parse the error response
                    const errorData = JSON.parse(jqXHR.responseText);
                    if (errorData && errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {
                    // If parsing fails, use the status text
                    errorMessage = textStatus + ': ' + errorThrown;
                }

                // Try the alternative loading method automatically
                console.log('Primary loading method failed, trying alternative method...');
                loadMessagesManually(userId);

                // Don't show error message since we're trying the alternative method
            }
        });
    }

    // Function to load user list
    function loadUserList() {
        console.log("Loading user list...");

        // Show loading indicator
        const userList = $('#user-list');
        userList.html('<li class="list-group-item text-muted text-center" id="loading-users">Loading users...</li>');

        // AJAX call with retry mechanism
        function fetchUsers(retryCount = 0) {
            $.ajax({
                url: '/user-all',
                method: 'GET',
                dataType: 'json',
                cache: false, // Prevent caching
                success: function(users) {
                    console.log("Users response:", users);

                    // Clear the loading indicator
                    userList.empty();

                    if(users && users.length > 0) {
                        console.log("Users loaded:", users.length, "users");

                        users.forEach(function(user) {
                            if (!user || !user.id || !user.name) {
                                console.warn("Skipping invalid user object:", user);
                                return;
                            }

                            // Determine if there are unread messages
                            const unreadBadge = user.unread_count && user.unread_count > 0
                                ? `<span class="badge bg-danger rounded-pill ms-2">${user.unread_count}</span>`
                                : '';

                            // Get last message preview if available
                            let lastMessagePreview = '';
                            if (user.last_message && user.last_message.msg) {
                                const msgText = user.last_message.msg.length > 30
                                    ? user.last_message.msg.substring(0, 30) + '...'
                                    : user.last_message.msg;
                                lastMessagePreview = `<small class="text-muted d-block text-truncate">${msgText}</small>`;
                            }

                            const userPhoto = user.photo ? `/upload/user_images/${user.photo}` : '/upload/no_image.jpg';

                            // Create a styled list item for each user with improved styling and explicit event handler
                            const listItem = `
                                <li class="list-group-item list-group-item-action user-list-item"
                                    data-user-id="${user.id}"
                                    data-user-name="${user.name.replace(/"/g, '&quot;')}"
                                    onclick="selectUser(${user.id}, '${user.name.replace(/'/g, "\\'")}')"
                                    id="user-${user.id}">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 position-relative">
                                            <img src="${userPhoto}" alt="${user.name}" class="rounded-circle"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            ${user.unread_count > 0 ? '<span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>' : ''}
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="fw-bold">${user.name}</div>
                                                ${unreadBadge}
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                ${lastMessagePreview}
                                                <small class="text-muted ms-2"><i class="fas fa-chevron-right"></i></small>
                                            </div>
                                        </div>
                                    </div>
                                </li>`;

                            userList.append(listItem);
                        });
                    } else {
                        userList.append('<li class="list-group-item text-muted text-center">No active chats found.</li>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching user list:', textStatus, errorThrown);

                    if (retryCount < 3) {
                        // Retry up to 3 times with exponential backoff
                        const delay = Math.pow(2, retryCount) * 1000;
                        console.log(`Retrying in ${delay}ms... (Attempt ${retryCount + 1}/3)`);

                        userList.html(`<li class="list-group-item text-muted text-center">Loading users... (Retry ${retryCount + 1}/3)</li>`);

                        setTimeout(() => {
                            fetchUsers(retryCount + 1);
                        }, delay);
                    } else {
                        // Show error message after all retries failed
                        userList.empty().append('<li class="list-group-item text-danger text-center">Error loading users. Please try refreshing the page.</li>');
                    }
                }
            });
        }

        // Start the initial fetch
        fetchUsers();
    }

    $(document).ready(function() {
        console.log("Document ready, initializing agent chat...");

        // Request notification permission
        if ("Notification" in window) {
            Notification.requestPermission();
        }

        // Load initial user list dynamically
        loadUserList();

        // Use event delegation for user list items with improved error handling
        $('#user-list').on('click', '.user-list-item', function(e) {
            e.preventDefault(); // Prevent any default action
            e.stopPropagation(); // Stop event bubbling

            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');

            console.log('User list item clicked:', this);
            console.log('User data attributes:', { userId, userName });

            if (userId && userName) {
                console.log('User clicked:', userId, userName);
                // Call selectUser with a slight delay to ensure the UI updates properly
                setTimeout(() => selectUser(userId, userName), 50);
            } else {
                console.error('Missing user data attributes:', $(this).data());
                // Try to get the ID from the element ID as a fallback
                const elementId = $(this).attr('id');
                if (elementId && elementId.startsWith('user-')) {
                    const idFromElement = elementId.replace('user-', '');
                    const nameFromElement = $(this).find('.fw-bold').text();
                    if (idFromElement && nameFromElement) {
                        console.log('Using fallback ID and name from element:', idFromElement, nameFromElement);
                        setTimeout(() => selectUser(idFromElement, nameFromElement), 50);
                    }
                }
            }

            return false; // Prevent any default behavior
        });

        // Send message on button click
        $('#send-message-button').on('click', function() {
            sendMessage();
        });

        // Send message on Enter key press in input field
        $('#chat-message-input').on('keypress', function(e) {
            if (e.which === 13) { // Enter key code
                e.preventDefault(); // Prevent default form submission (if any)
                sendMessage();
            }
        });

        // **** Laravel Echo Listener ****
        let echoConnected = false;
        let fallbackPollingInterval = null;

        try {
            if (typeof Echo !== 'undefined' && typeof currentUserId !== 'undefined') { // Check currentUserId too
                console.log(`Echo listening on private channel: chat.${currentUserId}`);

                // Set up connection status monitoring
                Echo.connector.pusher.connection.bind('connected', function() {
                    console.log('Pusher connected successfully');
                    echoConnected = true;

                    // Clear any fallback polling if it was active
                    if (fallbackPollingInterval) {
                        console.log('Clearing fallback polling interval');
                        clearInterval(fallbackPollingInterval);
                        fallbackPollingInterval = null;
                    }
                });

                Echo.connector.pusher.connection.bind('disconnected', function() {
                    console.log('Pusher disconnected');
                    echoConnected = false;
                    setupFallbackPolling();
                });

                Echo.connector.pusher.connection.bind('error', function(err) {
                    console.error('Pusher connection error:', err);
                    echoConnected = false;
                    setupFallbackPolling();
                });

                // Listen for messages
                Echo.private(`chat.${currentUserId}`) // Listen on the authenticated user's channel
                    .listen('.chat-message', (e) => { // Use broadcastAs name from event
                        console.log('Message received via Echo:', e);

                        try {
                            // Extract the message from the event data
                            let message = null;
                            if (e.message) {
                                message = e.message;
                            } else if (e.data && e.data.message) {
                                message = e.data.message;
                            } else {
                                // Try to find message in any property of the event
                                for (const key in e) {
                                    if (e[key] && typeof e[key] === 'object' && e[key].msg) {
                                        message = e[key];
                                        break;
                                    }
                                }

                                if (!message) {
                                    console.warn("Received Echo event with unknown structure:", e);
                                    return;
                                }
                            }

                            // Generate a unique ID for the message
                            const messageId = message.id ? `msg_${message.id}` : `temp_${Date.now()}`;

                            // Check if this message is already displayed
                            if (document.getElementById(messageId)) {
                                console.log(`Message with ID ${messageId} already exists in the DOM, skipping.`);
                                return;
                            }

                            // Play notification sound
                            const isFromOther = message.sender_id != currentUserId;
                            if (isFromOther) {
                                try {
                                    // Create a simple beep sound
                                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                                    const oscillator = audioContext.createOscillator();
                                    const gainNode = audioContext.createGain();

                                    oscillator.type = 'sine';
                                    oscillator.frequency.value = 800;
                                    gainNode.gain.value = 0.1;

                                    oscillator.connect(gainNode);
                                    gainNode.connect(audioContext.destination);

                                    oscillator.start();
                                    setTimeout(() => oscillator.stop(), 200);
                                } catch (soundError) {
                                    console.warn("Could not play notification sound:", soundError);
                                }
                            }

                            // Only append if the message is relevant to the currently selected chat
                            // Check if the message involves the agent (currentUserId) and the selected user
                            if (selectedUserId &&
                                ((message.sender_id == currentUserId && message.receiver_id == selectedUserId) ||
                                (message.sender_id == selectedUserId && message.receiver_id == currentUserId)) )
                            {
                                console.log('Appending received message to active chat');
                                appendMessage(message, true); // Append as new message
                                scrollToBottom();

                                // Mark message as read if it's from the other person
                                if (isFromOther && message.id) {
                                    $.ajax({
                                        url: '/mark-message-read',
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            message_id: message.id
                                        },
                                        error: function(err) {
                                            console.warn("Could not mark message as read:", err);
                                        }
                                    });
                                }
                            } else {
                                console.log('Received message for a different chat or no chat selected.');
                                // Reload the user list to show updated unread counts
                                loadUserList();

                                // Show a notification for the new message
                                if (isFromOther && "Notification" in window && Notification.permission === "granted") {
                                    let senderName = 'User';
                                    if (message.user && message.user.name) {
                                        senderName = message.user.name;
                                    }

                                    new Notification("New message from " + senderName, {
                                        body: message.msg.substring(0, 60) + (message.msg.length > 60 ? '...' : '')
                                    });
                                }
                            }
                        } catch (error) {
                            console.error("Error handling real-time message:", error, error.stack);
                            // Try to recover by refreshing the user list
                            loadUserList();
                        }
                    });
            } else {
                console.error('Laravel Echo or currentUserId is not defined. Make sure app.js is loaded and configured.');
                setupFallbackPolling();
            }
        } catch (error) {
            console.error('Error setting up Echo listener:', error);
            setupFallbackPolling();
        }

        // Function to set up fallback polling when WebSockets are not available
        function setupFallbackPolling() {
            // Clear any existing interval first
            if (fallbackPollingInterval) {
                clearInterval(fallbackPollingInterval);
                fallbackPollingInterval = null;
            }

            // Only set up polling if we have a selected user
            if (selectedUserId) {
                console.log('Setting up fallback polling for messages with user ID:', selectedUserId);

                // Poll for new messages every 5 seconds
                fallbackPollingInterval = setInterval(function() {
                    if (selectedUserId) {
                        console.log('Polling for new messages with user:', selectedUserId);

                        $.ajax({
                            url: '/user-message/' + selectedUserId,
                            method: 'GET',
                            dataType: 'json',
                            cache: false, // Prevent caching
                            success: function(data) {
                                if (data.messages && data.messages.length > 0) {
                                    // Get the current messages in the DOM
                                    const existingMessageIds = [];
                                    $('.message-bubble').each(function() {
                                        const id = $(this).attr('id');
                                        if (id && id.startsWith('msg_')) {
                                            existingMessageIds.push(parseInt(id.replace('msg_', '')));
                                        }
                                    });

                                    // Find new messages
                                    const newMessages = data.messages.filter(msg => {
                                        // Only include messages with valid IDs that aren't already displayed
                                        return msg.id && !existingMessageIds.includes(msg.id);
                                    });

                                    // Append new messages
                                    if (newMessages.length > 0) {
                                        console.log('Found new messages via polling:', newMessages.length);
                                        newMessages.forEach(msg => appendMessage(msg, true));
                                        scrollToBottom();

                                        // Mark messages as read if they're from the other person
                                        newMessages.forEach(msg => {
                                            if (msg.sender_id != currentUserId && msg.id) {
                                                $.ajax({
                                                    url: '/mark-message-read',
                                                    method: 'POST',
                                                    data: {
                                                        _token: '{{ csrf_token() }}',
                                                        message_id: msg.id
                                                    },
                                                    error: function(err) {
                                                        console.warn("Could not mark message as read:", err);
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('Error polling for messages:', textStatus, errorThrown);
                            }
                        });

                        // Also refresh the user list to show updated unread counts
                        loadUserList();
                    }
                }, 5000); // Poll every 5 seconds

                return true;
            } else {
                // If no user is selected, just refresh the user list periodically
                fallbackPollingInterval = setInterval(function() {
                    loadUserList();
                }, 10000); // Poll every 10 seconds
            }

            return false;
        }

    });
</script>

@endsection