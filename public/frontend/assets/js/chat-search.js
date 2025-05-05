/**
 * Chat Search and Message History Functionality
 */

class ChatMessageSearch {
    constructor() {
        this.searchBtn = document.getElementById('search-messages-btn');
        this.searchContainer = document.getElementById('message-search-container');
        this.searchInput = document.getElementById('message-search-input');
        this.searchResults = document.getElementById('message-search-results');
        this.closeSearchBtn = document.getElementById('close-search-btn');
        this.chatMessageArea = document.getElementById('chat-message-area');

        this.messages = [];
        this.selectedUserId = null;
        this.searchTimeout = null;

        this.initialize();
    }

    initialize() {
        // Set up event listeners
        if (this.searchBtn) {
            this.searchBtn.addEventListener('click', () => this.toggleSearch());
        }

        if (this.closeSearchBtn) {
            this.closeSearchBtn.addEventListener('click', () => this.closeSearch());
        }

        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => this.performSearch(), 300);
            });

            this.searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeSearch();
                }
            });
        }
    }

    /**
     * Set the current conversation messages for searching
     * @param {Array} messages - Array of message objects
     * @param {number} userId - ID of the user in the conversation
     */
    setMessages(messages, userId) {
        this.messages = messages || [];
        this.selectedUserId = userId;

        // Enable/disable search button based on message count
        if (this.searchBtn) {
            this.searchBtn.disabled = this.messages.length === 0;
        }
    }

    /**
     * Toggle search container visibility
     */
    toggleSearch() {
        if (this.searchContainer.classList.contains('active')) {
            this.closeSearch();
        } else {
            this.openSearch();
        }
    }

    /**
     * Open search container and focus input
     */
    openSearch() {
        this.searchContainer.classList.add('active');
        this.searchInput.focus();
        this.searchInput.value = '';
        this.searchResults.innerHTML = '';
    }

    /**
     * Close search container
     */
    closeSearch() {
        this.searchContainer.classList.remove('active');
        this.searchResults.innerHTML = '';
        this.searchInput.value = '';

        // Remove any highlights from the chat
        this.removeHighlights();
    }

    /**
     * Perform search on messages
     */
    performSearch() {
        const query = this.searchInput.value.trim().toLowerCase();
        this.searchResults.innerHTML = '';

        if (!query || query.length < 2) {
            return;
        }

        // Remove previous highlights
        this.removeHighlights();

        // Show loading indicator
        this.searchResults.innerHTML = '<div class="p-3 text-center">Searching messages...</div>';

        // If we have the messages locally, search them
        if (this.messages && this.messages.length > 0) {
            this.searchLocalMessages(query);
        } else {
            // Otherwise, search on the server
            this.searchServerMessages(query);
        }
    }

    /**
     * Search messages locally
     * @param {string} query - Search query
     */
    searchLocalMessages(query) {
        // Find messages matching the query
        const matches = this.messages.filter(msg =>
            msg.msg && msg.msg.toLowerCase().includes(query)
        );

        if (matches.length === 0) {
            this.searchResults.innerHTML = '<div class="p-3 text-center text-muted">No messages found</div>';
            return;
        }

        this.displaySearchResults(matches, query);
    }

    /**
     * Search messages on the server
     * @param {string} query - Search query
     */
    searchServerMessages(query) {
        if (!this.selectedUserId) {
            this.searchResults.innerHTML = '<div class="p-3 text-center text-muted">Please select a conversation first</div>';
            return;
        }

        // Make AJAX request to search messages
        fetch('/search-messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                user_id: this.selectedUserId,
                query: query
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.results) {
                if (data.results.length === 0) {
                    this.searchResults.innerHTML = '<div class="p-3 text-center text-muted">No messages found</div>';
                    return;
                }

                this.displaySearchResults(data.results, query);
            } else {
                this.searchResults.innerHTML = '<div class="p-3 text-center text-danger">Error searching messages</div>';
            }
        })
        .catch(error => {
            console.error('Error searching messages:', error);
            this.searchResults.innerHTML = '<div class="p-3 text-center text-danger">Error searching messages</div>';
        });
    }

    /**
     * Display search results
     * @param {Array} matches - Matching messages
     * @param {string} query - Search query
     */
    displaySearchResults(matches, query) {
        this.searchResults.innerHTML = '';

        // Display search results
        matches.forEach(message => {
            const messageText = message.msg;
            const messageDate = new Date(message.created_at);
            const formattedDate = messageDate.toLocaleString();

            // Highlight the matching text
            const highlightedText = this.highlightText(messageText, query);

            const resultItem = document.createElement('div');
            resultItem.className = 'search-result-item';
            resultItem.innerHTML = `
                <div class="search-result-text">${highlightedText}</div>
                <div class="search-result-date">${formattedDate}</div>
            `;

            // Add click event to scroll to the message
            resultItem.addEventListener('click', () => {
                this.scrollToMessage(message.id);
            });

            this.searchResults.appendChild(resultItem);
        });
    }

    /**
     * Highlight search text in message
     * @param {string} text - Original message text
     * @param {string} query - Search query
     * @returns {string} HTML with highlighted text
     */
    highlightText(text, query) {
        if (!text) return '';

        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    }

    /**
     * Scroll to a specific message and highlight it
     * @param {number} messageId - ID of the message to scroll to
     */
    scrollToMessage(messageId) {
        const messageElement = document.getElementById(`msg_${messageId}`);

        if (messageElement) {
            // Remove previous highlights
            this.removeHighlights();

            // Add highlight class to the message
            messageElement.classList.add('highlighted-message');

            // Scroll to the message
            messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Remove highlight after a delay
            setTimeout(() => {
                messageElement.classList.remove('highlighted-message');
            }, 3000);
        }
    }

    /**
     * Remove all highlights from messages
     */
    removeHighlights() {
        const highlightedMessages = document.querySelectorAll('.highlighted-message');
        highlightedMessages.forEach(el => el.classList.remove('highlighted-message'));
    }
}

// Initialize the search functionality when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.chatSearch = new ChatMessageSearch();
});
