/**
 * RealShed Chatbot - Keyword-based FAQ System
 * Provides intelligent responses to user queries about RealShed features
 */

// Add immediate visual indicator that script is loading
document.body.insertAdjacentHTML('beforeend', '<div id="chatbot-loading" style="position:fixed;top:10px;right:10px;background:#43C4E3;color:white;padding:5px;z-index:9999;border-radius:3px;">Chatbot Loading...</div>');

class RealShedChatbot {
    constructor() {
        this.isOpen = false;
        this.chatHistory = [];
        this.fallbackResponses = [
            "I'm here to help you navigate RealShed! Try asking about property search, agents, or your account.",
            "I can help you with questions about buying, renting, wishlist, or contacting agents. What would you like to know?",
            "Feel free to ask me about property listings, agent details, or how to use your dashboard features!"
        ];

        this.init();
    }

    init() {
        this.createChatWidget();
        this.bindEvents();
        this.loadChatHistory();
    }

    createChatWidget() {
        // Remove loading indicator
        const loadingEl = document.getElementById('chatbot-loading');
        if (loadingEl) loadingEl.remove();

        const chatHTML = `
            <!-- Chat Bubble -->
            <div id="chat-bubble" class="chat-bubble">
                <i class="fas fa-comments"></i>
                <span class="chat-notification" id="chat-notification">1</span>
            </div>

            <!-- Chat Widget -->
            <div id="chat-widget" class="chat-widget">
                <div class="chat-header">
                    <div class="chat-header-info">
                        <img src="/frontend/assets/images/favicon.ico" alt="RealShed" class="chat-logo">
                        <div>
                            <h4>RealShed Assistant</h4>
                            <span class="chat-status">Online</span>
                        </div>
                    </div>
                    <button id="chat-close" class="chat-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="chat-messages" id="chat-messages">
                    <div class="message bot-message">
                        <div class="message-content">
                            <p>👋 Welcome to RealShed! I'm here to help you navigate our platform.</p>
                            <p>Ask me about:</p>
                            <ul>
                                <li>🏠 Property search & filtering</li>
                                <li>👥 Finding and contacting agents</li>
                                <li>❤️ Wishlist & compare features</li>
                                <li>📱 Your dashboard & account</li>
                                <li>💬 Chat & messaging</li>
                            </ul>
                        </div>
                        <span class="message-time" id="welcome-time"></span>
                    </div>
                </div>
                
                <div class="chat-input-container">
                    <input type="text" id="chat-input" placeholder="Type your question here..." maxlength="500">
                    <button id="chat-send" class="chat-send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', chatHTML);

        // Set the welcome message time after HTML is inserted
        const welcomeTimeElement = document.getElementById('welcome-time');
        if (welcomeTimeElement) {
            welcomeTimeElement.textContent = this.getCurrentTime();
        }
    }

    bindEvents() {
        const chatBubble = document.getElementById('chat-bubble');
        const chatWidget = document.getElementById('chat-widget');
        const chatClose = document.getElementById('chat-close');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');

        if (!chatBubble || !chatWidget || !chatClose || !chatInput || !chatSend) {
            console.error('RealShed Chatbot: One or more chat elements not found in DOM');
            return;
        }

        // Toggle chat widget
        chatBubble.addEventListener('click', () => this.toggleChat());
        chatClose.addEventListener('click', () => this.closeChat());

        // Send message
        chatSend.addEventListener('click', () => this.sendMessage());
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });

        // Close chat when clicking outside
        document.addEventListener('click', (e) => {
            if (!chatWidget.contains(e.target) && !chatBubble.contains(e.target) && this.isOpen) {
                // Don't auto-close to improve user experience
            }
        });
    }

    toggleChat() {
        const chatWidget = document.getElementById('chat-widget');
        const chatBubble = document.getElementById('chat-bubble');
        const notification = document.getElementById('chat-notification');
        
        if (this.isOpen) {
            this.closeChat();
        } else {
            chatWidget.classList.add('chat-widget-open');
            chatBubble.style.display = 'none';
            notification.style.display = 'none';
            this.isOpen = true;
            
            // Focus input
            setTimeout(() => {
                document.getElementById('chat-input').focus();
            }, 300);
        }
    }

    closeChat() {
        const chatWidget = document.getElementById('chat-widget');
        const chatBubble = document.getElementById('chat-bubble');
        
        chatWidget.classList.remove('chat-widget-open');
        chatBubble.style.display = 'flex';
        this.isOpen = false;
    }

    sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        input.value = '';

        // Process and respond
        setTimeout(() => {
            const response = this.processMessage(message);
            this.addMessage(response, 'bot');
        }, 500);
    }

    addMessage(content, sender) {
        const messagesContainer = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        messageDiv.innerHTML = `
            <div class="message-content">
                ${typeof content === 'string' ? `<p>${content}</p>` : content}
            </div>
            <span class="message-time">${this.getCurrentTime()}</span>
        `;

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Save to history
        this.chatHistory.push({
            content: content,
            sender: sender,
            timestamp: new Date().toISOString()
        });
        this.saveChatHistory();
    }

    getCurrentTime() {
        return new Date().toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
    }

    // Main message processing function
    processMessage(message) {
        const lowerMessage = message.toLowerCase();

        // Check for greetings first
        if (this.matchKeywords(lowerMessage, ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'])) {
            return this.getGreetingResponse();
        }

        // Check for thanks
        if (this.matchKeywords(lowerMessage, ['thank', 'thanks', 'appreciate'])) {
            return "You're welcome! I'm always here to help you with RealShed. Is there anything else you'd like to know?";
        }

        // Property search related queries
        if (this.matchKeywords(lowerMessage, ['search', 'find', 'look', 'property', 'house', 'home', 'apartment'])) {
            return this.getPropertySearchResponse(lowerMessage);
        }

        // Agent related queries
        if (this.matchKeywords(lowerMessage, ['agent', 'contact', 'talk', 'speak', 'call', 'phone', 'email'])) {
            return this.getAgentResponse(lowerMessage);
        }

        // Buy/Rent/Sell queries
        if (this.matchKeywords(lowerMessage, ['buy', 'purchase', 'buying'])) {
            return this.getBuyResponse();
        }
        if (this.matchKeywords(lowerMessage, ['rent', 'rental', 'renting', 'lease'])) {
            return this.getRentResponse();
        }
        if (this.matchKeywords(lowerMessage, ['sell', 'selling', 'list'])) {
            return this.getSellResponse();
        }

        // Account and dashboard queries
        if (this.matchKeywords(lowerMessage, ['account', 'profile', 'dashboard', 'login', 'register', 'sign'])) {
            return this.getAccountResponse(lowerMessage);
        }

        // Wishlist queries
        if (this.matchKeywords(lowerMessage, ['wishlist', 'wish list', 'favorite', 'save', 'bookmark'])) {
            return this.getWishlistResponse();
        }

        // Compare queries
        if (this.matchKeywords(lowerMessage, ['compare', 'comparison', 'difference'])) {
            return this.getCompareResponse();
        }

        // Chat and messaging queries
        if (this.matchKeywords(lowerMessage, ['chat', 'message', 'messaging', 'talk', 'communicate'])) {
            return this.getChatResponse();
        }

        // Blog queries
        if (this.matchKeywords(lowerMessage, ['blog', 'news', 'article', 'read'])) {
            return this.getBlogResponse();
        }

        // Navigation and help queries
        if (this.matchKeywords(lowerMessage, ['help', 'how', 'navigate', 'use', 'guide', 'tutorial'])) {
            return this.getHelpResponse(lowerMessage);
        }

        // Filter and advanced search
        if (this.matchKeywords(lowerMessage, ['filter', 'advanced', 'criteria', 'bedroom', 'bathroom', 'price', 'location'])) {
            return this.getFilterResponse();
        }

        // Recommendation system
        if (this.matchKeywords(lowerMessage, ['recommend', 'suggestion', 'similar', 'like'])) {
            return this.getRecommendationResponse();
        }

        // Default fallback
        return this.getFallbackResponse();
    }

    matchKeywords(message, keywords) {
        return keywords.some(keyword => message.includes(keyword));
    }

    getGreetingResponse() {
        const greetings = [
            "Hello! Welcome to RealShed! 🏠 I'm here to help you find your perfect property or answer any questions about our platform.",
            "Hi there! 👋 I'm your RealShed assistant. Whether you're looking to buy, rent, or just explore, I'm here to help!",
            "Hey! Great to see you on RealShed! I can help you with property searches, agent contacts, or navigating your account."
        ];
        return greetings[Math.floor(Math.random() * greetings.length)];
    }

    getPropertySearchResponse(message) {
        if (this.matchKeywords(message, ['how', 'where'])) {
            return `🔍 <strong>How to Search Properties:</strong><br><br>
                    1. <strong>Quick Search:</strong> Use the search bar on the home page<br>
                    2. <strong>Browse by Type:</strong> Go to Property → Rent Property or Buy Property<br>
                    3. <strong>View All:</strong> Visit our complete property listings<br>
                    4. <strong>Filter Results:</strong> Use filters for price, bedrooms, bathrooms, and location<br><br>
                    <em>💡 Tip: You can also browse by property categories to see different types of properties!</em>`;
        }

        return `🏠 <strong>Property Search Options:</strong><br><br>
                • <strong>Rent Properties:</strong> Browse available rental properties<br>
                • <strong>Buy Properties:</strong> Explore properties for sale<br>
                • <strong>All Properties:</strong> View our complete listings<br>
                • <strong>Property Types:</strong> Filter by apartment, house, villa, etc.<br><br>
                Use our advanced filters to narrow down by price range, bedrooms, bathrooms, and location!`;
    }

    // Chat history management
    saveChatHistory() {
        try {
            sessionStorage.setItem('realshed_chat_history', JSON.stringify(this.chatHistory));
        } catch (e) {
            console.warn('Could not save chat history:', e);
        }
    }

    loadChatHistory() {
        try {
            const saved = sessionStorage.getItem('realshed_chat_history');
            if (saved) {
                this.chatHistory = JSON.parse(saved);
            }
        } catch (e) {
            console.warn('Could not load chat history:', e);
            this.chatHistory = [];
        }
    }
}

    getAgentResponse(message) {
        if (this.matchKeywords(message, ['how', 'find', 'contact'])) {
            return `👥 <strong>How to Contact Agents:</strong><br><br>
                    1. <strong>Browse Agents:</strong> Visit the "Agents" section from the main menu<br>
                    2. <strong>Property Page:</strong> Click "Chat With Agent" on any property details page<br>
                    3. <strong>Agent Details:</strong> View agent profiles and send direct messages<br>
                    4. <strong>Live Chat:</strong> Use the live chat feature from your dashboard<br><br>
                    <em>📝 Note: You need to be logged in to chat with agents!</em>`;
        }

        return `🏢 <strong>Agent Services:</strong><br><br>
                • <strong>Find Agents:</strong> Browse our qualified real estate agents<br>
                • <strong>Direct Contact:</strong> Send messages directly to property agents<br>
                • <strong>Live Chat:</strong> Real-time communication with agents<br>
                • <strong>Property Inquiries:</strong> Ask questions about specific properties<br><br>
                All our agents are verified professionals ready to help you!`;
    }

    getBuyResponse() {
        return `🏠 <strong>Buying Properties:</strong><br><br>
                1. <strong>Browse:</strong> Go to Property → Buy Property<br>
                2. <strong>Filter:</strong> Use price range, bedrooms, location filters<br>
                3. <strong>View Details:</strong> Click on properties to see full information<br>
                4. <strong>Contact Agent:</strong> Chat with the property agent<br>
                5. <strong>Schedule Visit:</strong> Request property viewing appointments<br><br>
                <em>💰 All prices are displayed in NPR (Nepalese Rupee)</em>`;
    }

    getRentResponse() {
        return `🏡 <strong>Renting Properties:</strong><br><br>
                1. <strong>Browse Rentals:</strong> Go to Property → Rent Property<br>
                2. <strong>Search & Filter:</strong> Find properties by location, price, size<br>
                3. <strong>Property Details:</strong> View photos, amenities, and specifications<br>
                4. <strong>Contact Owner/Agent:</strong> Use the chat feature to inquire<br>
                5. <strong>Schedule Viewing:</strong> Arrange property visits<br><br>
                <em>🔑 Perfect for finding your next rental home!</em>`;
    }

    getSellResponse() {
        return `📋 <strong>Selling/Listing Properties:</strong><br><br>
                To list your property on RealShed:<br><br>
                1. <strong>Agent Account:</strong> You need an agent account to list properties<br>
                2. <strong>Property Details:</strong> Add comprehensive property information<br>
                3. <strong>Photos & Media:</strong> Upload high-quality property images<br>
                4. <strong>Pricing:</strong> Set competitive market prices<br><br>
                <em>💼 Contact our team for agent registration and listing assistance!</em>`;
    }

    getAccountResponse(message) {
        if (this.matchKeywords(message, ['login', 'sign in'])) {
            return `🔐 <strong>Login to RealShed:</strong><br><br>
                    • Click "Sign In" in the top-right corner<br>
                    • Enter your email and password<br>
                    • Access your personalized dashboard<br><br>
                    <em>🆕 New user? You can register on the same page!</em>`;
        }

        if (this.matchKeywords(message, ['register', 'sign up', 'create'])) {
            return `📝 <strong>Create Your Account:</strong><br><br>
                    1. Click "Sign In" then switch to "Register" tab<br>
                    2. Fill in your name, email, and password<br>
                    3. Verify your account<br>
                    4. Start exploring properties!<br><br>
                    <em>✨ Registration is free and gives you access to wishlist, chat, and more!</em>`;
        }

        return `👤 <strong>Your Dashboard Features:</strong><br><br>
                • <strong>Profile Settings:</strong> Update your personal information<br>
                • <strong>Schedule Requests:</strong> Manage property viewing appointments<br>
                • <strong>Wishlist:</strong> Save your favorite properties<br>
                • <strong>Compare:</strong> Compare different properties side-by-side<br>
                • <strong>Live Chat:</strong> Communicate with agents<br>
                • <strong>Security:</strong> Change password and account settings<br><br>
                Access your dashboard after logging in!`;
    }

    getWishlistResponse() {
        return `❤️ <strong>Wishlist Feature:</strong><br><br>
                1. <strong>Add Properties:</strong> Click the heart icon on any property<br>
                2. <strong>View Wishlist:</strong> Access from your dashboard sidebar<br>
                3. <strong>Get Recommendations:</strong> Our system suggests similar properties<br>
                4. <strong>Easy Management:</strong> Remove properties you're no longer interested in<br><br>
                <em>🎯 Pro tip: Add at least 3 properties to get personalized recommendations!</em>`;
    }

    getCompareResponse() {
        return `⚖️ <strong>Property Comparison:</strong><br><br>
                1. <strong>Add to Compare:</strong> Click the compare icon on properties<br>
                2. <strong>View Comparison:</strong> Go to Compare section in your dashboard<br>
                3. <strong>Side-by-Side:</strong> See detailed comparisons of features<br>
                4. <strong>Make Decisions:</strong> Compare prices, rooms, amenities, and locations<br><br>
                <em>📊 Perfect for making informed property decisions!</em>`;
    }

    getChatResponse() {
        return `💬 <strong>Chat & Messaging:</strong><br><br>
                • <strong>Agent Chat:</strong> Direct messaging with property agents<br>
                • <strong>Property Inquiries:</strong> Ask questions about specific properties<br>
                • <strong>Live Chat:</strong> Real-time communication from your dashboard<br>
                • <strong>Message History:</strong> Keep track of all your conversations<br><br>
                <em>🔒 Login required for all messaging features!</em>`;
    }

    getBlogResponse() {
        return `📰 <strong>RealShed Blog:</strong><br><br>
                • <strong>Market Insights:</strong> Latest real estate trends and news<br>
                • <strong>Buying Guides:</strong> Tips for property buyers<br>
                • <strong>Investment Advice:</strong> Real estate investment strategies<br>
                • <strong>Local Market:</strong> Nepal property market updates<br><br>
                <em>📖 Visit our Blog section from the main menu to stay informed!</em>`;
    }

    getHelpResponse(message) {
        if (this.matchKeywords(message, ['navigate', 'use', 'guide'])) {
            return `🧭 <strong>Navigation Guide:</strong><br><br>
                    • <strong>Home:</strong> Main page with search and featured properties<br>
                    • <strong>Property:</strong> Browse rent and buy properties<br>
                    • <strong>Agents:</strong> Find and contact real estate agents<br>
                    • <strong>Blog:</strong> Read market insights and guides<br>
                    • <strong>Dashboard:</strong> Your personal account area (after login)<br><br>
                    <em>🎯 Use the search bar for quick property searches!</em>`;
        }

        return `❓ <strong>Need Help?</strong><br><br>
                I can help you with:<br>
                • Property search and filtering<br>
                • Agent contact and communication<br>
                • Account management and dashboard<br>
                • Wishlist and compare features<br>
                • Navigation and general questions<br><br>
                <em>Just ask me anything about RealShed!</em>`;
    }

    getFilterResponse() {
        return `🔍 <strong>Advanced Property Filters:</strong><br><br>
                Available filters:<br>
                • <strong>Property Type:</strong> House, apartment, villa, etc.<br>
                • <strong>Price Range:</strong> Set minimum and maximum price<br>
                • <strong>Bedrooms:</strong> Number of bedrooms<br>
                • <strong>Bathrooms:</strong> Number of bathrooms<br>
                • <strong>Location/State:</strong> Filter by area or state<br>
                • <strong>Property Status:</strong> Buy, rent, or both<br><br>
                <em>🎯 Use multiple filters to find exactly what you're looking for!</em>`;
    }

    getRecommendationResponse() {
        return `🎯 <strong>Property Recommendations:</strong><br><br>
                Our smart recommendation system:<br>
                • <strong>Based on Wishlist:</strong> Suggests similar properties to your saved ones<br>
                • <strong>User Preferences:</strong> Learns from your browsing behavior<br>
                • <strong>Hybrid Algorithm:</strong> Combines multiple recommendation techniques<br>
                • <strong>Personalized Results:</strong> Tailored to your specific needs<br><br>
                <em>💡 Add properties to your wishlist to get better recommendations!</em>`;
    }

    getFallbackResponse() {
        return this.fallbackResponses[Math.floor(Math.random() * this.fallbackResponses.length)];
    }
}

// Initialize chatbot when DOM is ready
function initChatbot() {
    try {
        // Add status indicator
        document.body.insertAdjacentHTML('beforeend', '<div id="chatbot-init" style="position:fixed;top:40px;right:10px;background:green;color:white;padding:5px;z-index:9999;border-radius:3px;">Initializing...</div>');

        window.realShedChatbot = new RealShedChatbot();

        // Update status
        const initEl = document.getElementById('chatbot-init');
        if (initEl) {
            initEl.textContent = 'Chatbot Ready!';
            setTimeout(() => initEl.remove(), 2000);
        }
    } catch (error) {
        console.error('RealShed Chatbot: Error during initialization:', error);
        document.body.insertAdjacentHTML('beforeend', '<div style="position:fixed;top:40px;right:10px;background:red;color:white;padding:5px;z-index:9999;border-radius:3px;">Error: ' + error.message + '</div>');
    }
}

// Try multiple initialization methods
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChatbot);
} else {
    initChatbot();
}

// Fallback
setTimeout(initChatbot, 1000);
