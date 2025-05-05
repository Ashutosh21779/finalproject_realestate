@extends('admin.admin_dashboard')
@section('admin')
{{-- Include jQuery if not already globally available --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

{{-- Replicated Styles from Agent Chat --}}
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
    .message-sent {
        background-color: #0d6efd; /* Use primary blue for sent */
        color: #ffffff; /* White text for sent */
        align-self: flex-end;
        margin-left: auto;
    }
    .message-received {
        background-color: #2c3248; /* Slightly lighter dark bg */
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
                <div class="card-body" style="padding: 0.5rem;">
                    <div class="row">
                        <!-- User List -->
                        <div class="col-md-4 col-lg-3 border-end-lg pe-0 border-secondary">
                             <div class="px-3 pt-3">
                                 <h6 class="mb-3 text-light">Chat List</h6>
                             </div>
                             <div class="chat-list">
                                <ul class="list-group list-group-flush" id="user-list">
                                    {{-- User list will be loaded via JS --}}
                                    <li class="list-group-item text-muted text-center" id="loading-users">Loading users...</li>
                                </ul>
                             </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="col-md-8 col-lg-9 ps-0">
                             <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0" id="selected-user-name">Select a User to Chat</h6>
                                <input type="hidden" id="selected-user-id" value="">
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
    let selectedUserId = null;
    const currentUserId = {{ Auth::id() }}; // Get current user ID
    let selectedUserName = ''; // Store selected user's name

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
                appendMessage(message, false); // Append historical messages
            });
        } else {
             chatArea.append('<div class="text-center text-muted p-3">No messages yet. Start the conversation!</div>');
        }
        scrollToBottom(); // Scroll after loading all historical messages
    }

    // Function to send message
    function sendMessage() {
        const messageInput = $('#chat-message-input');
        const message = messageInput.val().trim();
        const receiverId = $('#selected-user-id').val();

        if (message === '' || !receiverId) {
            return;
        }

        console.log(`Sending message: "${message}" to user ID: ${receiverId}`);

        $.ajax({
            url: '/send-message',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Add CSRF token
                receiver_id: receiverId,
                msg: message
            },
            dataType: 'json',
            success: function(data) {
                console.log("Message sent successfully:", data.message);
                messageInput.val(''); // Clear input field
                // The message will be displayed via the Echo listener
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

                alert(errorMessage);

                // Re-enable the input field and button
                $('#chat-message-input').prop('disabled', false);
                $('#send-message-button').prop('disabled', false);
            }
        });
    }

    // Function to select user and load messages
    function selectUser(userId, userName) {
        if (selectedUserId === userId) {
            return;
        }
        console.log('Selecting user:', userId, userName);

        // Clear any existing fallback polling
        if (fallbackPollingInterval) {
            clearInterval(fallbackPollingInterval);
            fallbackPollingInterval = null;
        }

        selectedUserId = userId;
        selectedUserName = userName;
        $('#selected-user-id').val(userId);
        $('#selected-user-name').text('Chat with ' + userName);

        $('.user-list-item').removeClass('active');
        $('#user-' + userId).addClass('active');

        $('#chat-message-input').prop('disabled', false).focus();
        $('#send-message-button').prop('disabled', false);
        $('#chat-placeholder').hide();
        $('#chat-message-area').empty().append('<div class="text-center text-muted p-5">Loading messages...</div>');

        $.ajax({
            url: '/user-message/' + userId,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log("Messages loaded successfully for user:", data.user.name, data);
                if(data.user && data.messages) {
                    displayMessages(data.messages, data.user.name);

                    // If Echo is not connected, set up fallback polling
                    if (!echoConnected) {
                        console.log('Echo not connected, setting up fallback polling');
                        setupFallbackPolling();
                    }
                } else {
                     console.error("Invalid data structure received:", data);
                     $('#chat-message-area').empty().append('<div class="text-center text-danger p-5">Error loading messages.</div>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                 console.error('Error fetching messages:', textStatus, errorThrown, jqXHR.responseText);
                  $('#chat-message-area').empty().append('<div class="text-center text-danger p-5">Error loading messages.</div>');
            }
        });
    }

    // Function to load user list
    function loadUserList() {
        $.ajax({
            url: '/user-all', // Endpoint to get users the current user has chatted with
            method: 'GET',
            dataType: 'json',
            success: function(users) {
                const userList = $('#user-list');
                userList.empty(); // Clear loading/previous list
                if(users && users.length > 0) {
                     console.log("Loading users:", users);
                    users.forEach(function(user) {
                         if (!user || !user.id || !user.name) {
                             console.warn("Skipping invalid user object:", user);
                             return; // Skip if user data is incomplete
                         }
                        const userPhoto = user.photo ? `/upload/user_images/${user.photo}` : '/upload/no_image.jpg';
                        const listItem = `
                            <li class="list-group-item list-group-item-action d-flex align-items-center user-list-item"
                                style="cursor: pointer;"
                                onclick="selectUser(${user.id}, '${user.name}')"
                                id="user-${user.id}">
                                <img src="${userPhoto}" alt="user" class="rounded-circle me-3">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${user.name}</div>
                                    {{-- <small class="text-muted">Role: ${user.role || 'N/A'}</small> --}}
                                </div>
                                {{-- <span class="badge bg-danger rounded-pill ms-auto">?</span> --}}
                            </li>`;
                        userList.append(listItem);
                    });
                } else {
                    userList.append('<li class="list-group-item text-muted text-center">No active chats found.</li>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching user list:', textStatus, errorThrown);
                 $('#user-list').empty().append('<li class="list-group-item text-danger text-center">Error loading users.</li>');
            }
        });
    }

    $(document).ready(function() {
        // Load initial user list
        loadUserList();

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
            if (typeof Echo !== 'undefined') {
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
                    .listen('.chat-message', (e) => { // Use broadcastAs name
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
                console.error('Laravel Echo is not defined. Make sure app.js is loaded and configured.');
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