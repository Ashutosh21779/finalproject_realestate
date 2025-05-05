/**
 * Chat Notification System
 * Provides desktop notifications and sound alerts for new messages
 */

class ChatNotificationSystem {
    constructor() {
        this.hasPermission = false;
        this.notificationSound = new Audio('/frontend/assets/sounds/notification.mp3');
        this.initialize();
    }

    initialize() {
        // Request notification permission if not already granted
        if ("Notification" in window) {
            if (Notification.permission === "granted") {
                this.hasPermission = true;
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    this.hasPermission = permission === "granted";
                });
            }
        }

        // Preload notification sound
        this.notificationSound.load();
    }

    /**
     * Show a desktop notification for a new message
     * @param {Object} message - The message object
     * @param {Object} sender - The sender object
     */
    showNotification(message, sender) {
        // Play notification sound regardless of desktop notification permission
        this.playSound();

        // Show desktop notification if permission granted
        if (this.hasPermission) {
            const notification = new Notification("New message from " + sender.name, {
                body: this.truncateMessage(message.msg, 60),
                icon: '/frontend/assets/images/chat-icon.png',
                tag: 'chat-notification-' + sender.id,
                renotify: true
            });

            // Close notification after 5 seconds
            setTimeout(() => notification.close(), 5000);

            // Handle notification click
            notification.onclick = function() {
                window.focus();
                notification.close();
            };
        }
    }

    /**
     * Play notification sound
     */
    playSound() {
        try {
            // Reset sound to beginning and play
            this.notificationSound.currentTime = 0;
            this.notificationSound.play().catch(e => {
                console.warn('Could not play notification sound:', e);
            });
        } catch (e) {
            console.warn('Error playing notification sound:', e);
        }
    }

    /**
     * Truncate message to specified length
     * @param {string} message - The message to truncate
     * @param {number} length - Maximum length
     * @returns {string} Truncated message
     */
    truncateMessage(message, length) {
        return message.length > length 
            ? message.substring(0, length) + '...' 
            : message;
    }
}

// Create global instance
window.chatNotifications = new ChatNotificationSystem();
