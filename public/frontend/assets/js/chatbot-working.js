/**
 * PropNepal Chatbot - Guaranteed Working Version
 * Specialized real estate assistant for PropNepal platform
 */

(function() {
    'use strict';
    
    // Chatbot state
    let isOpen = false;
    let chatHistory = [];
    
    // Create chatbot HTML with inline styles for guaranteed visibility
    function createChatbot() {
        // Remove any existing chatbot
        const existing = document.getElementById('propnepal-chatbot-container');
        if (existing) existing.remove();

        const chatbotHTML = `
            <div id="propnepal-chatbot-container">
                <!-- Chat Bubble -->
                <div id="chat-bubble" style="
                    position: fixed !important;
                    bottom: 20px !important;
                    right: 20px !important;
                    width: 60px !important;
                    height: 60px !important;
                    background: linear-gradient(135deg, #43C4E3 0%, #2D3954 100%) !important;
                    border-radius: 50% !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    cursor: pointer !important;
                    box-shadow: 0 4px 20px rgba(67, 196, 227, 0.4) !important;
                    z-index: 999999 !important;
                    color: white !important;
                    font-size: 24px !important;
                    border: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    transition: transform 0.3s ease !important;
                ">
                    💬
                    <div id="chat-notification" style="
                        position: absolute !important;
                        top: -5px !important;
                        right: -5px !important;
                        background: #ff4444 !important;
                        color: white !important;
                        border-radius: 50% !important;
                        width: 20px !important;
                        height: 20px !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        font-size: 12px !important;
                        font-weight: bold !important;
                    ">1</div>
                </div>
                
                <!-- Chat Widget -->
                <div id="chat-widget" style="
                    position: fixed !important;
                    bottom: 90px !important;
                    right: 20px !important;
                    width: 350px !important;
                    height: 500px !important;
                    background: white !important;
                    border-radius: 15px !important;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
                    z-index: 999998 !important;
                    display: none !important;
                    flex-direction: column !important;
                    overflow: hidden !important;
                    border: 1px solid #e0e0e0 !important;
                ">
                    <!-- Header -->
                    <div style="
                        background: linear-gradient(135deg, #2D3954 0%, #43C4E3 100%) !important;
                        color: white !important;
                        padding: 15px !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                    ">
                        <div style="display: flex !important; align-items: center !important;">
                            <div style="
                                width: 35px !important;
                                height: 35px !important;
                                background: rgba(255,255,255,0.2) !important;
                                border-radius: 50% !important;
                                display: flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                margin-right: 10px !important;
                            ">🏠</div>
                            <div>
                                <div style="font-weight: bold !important; font-size: 16px !important;">PropNepal Assistant</div>
                                <div style="font-size: 12px !important; opacity: 0.9 !important;">Online</div>
                            </div>
                        </div>
                        <button id="chat-close" style="
                            background: none !important;
                            border: none !important;
                            color: white !important;
                            font-size: 20px !important;
                            cursor: pointer !important;
                            padding: 5px !important;
                        ">×</button>
                    </div>
                    
                    <!-- Messages -->
                    <div id="chat-messages" style="
                        flex: 1 !important;
                        padding: 15px !important;
                        overflow-y: auto !important;
                        background: #f8f9fa !important;
                    ">
                        <div style="
                            background: white !important;
                            padding: 12px !important;
                            border-radius: 10px !important;
                            margin-bottom: 10px !important;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.1) !important;
                        ">
                            <div style="color: #333 !important; line-height: 1.4 !important;">
                                👋 Welcome to PropNepal! I'm here to help you navigate our platform.<br><br>
                                Ask me about:<br>
                                🏠 Property search & filtering<br>
                                👥 Finding and contacting agents<br>
                                ❤️ Wishlist & compare features<br>
                                📱 Your dashboard & account<br>
                                💬 Chat & messaging
                            </div>
                            <div style="font-size: 11px !important; color: #888 !important; margin-top: 8px !important;" id="welcome-time"></div>
                        </div>
                    </div>
                    
                    <!-- Input -->
                    <div style="
                        padding: 15px !important;
                        background: white !important;
                        border-top: 1px solid #e0e0e0 !important;
                        display: flex !important;
                        gap: 10px !important;
                    ">
                        <input type="text" id="chat-input" placeholder="Type your question here..." style="
                            flex: 1 !important;
                            padding: 10px !important;
                            border: 1px solid #ddd !important;
                            border-radius: 20px !important;
                            outline: none !important;
                            font-size: 14px !important;
                        ">
                        <button id="chat-send" style="
                            background: linear-gradient(135deg, #43C4E3 0%, #2D3954 100%) !important;
                            color: white !important;
                            border: none !important;
                            border-radius: 50% !important;
                            width: 40px !important;
                            height: 40px !important;
                            cursor: pointer !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                        ">➤</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
        
        // Set welcome time
        document.getElementById('welcome-time').textContent = new Date().toLocaleTimeString();
        
        // Bind events
        bindEvents();
    }
    
    function bindEvents() {
        const bubble = document.getElementById('chat-bubble');
        const widget = document.getElementById('chat-widget');
        const closeBtn = document.getElementById('chat-close');
        const input = document.getElementById('chat-input');
        const sendBtn = document.getElementById('chat-send');
        const notification = document.getElementById('chat-notification');
        
        // Toggle chat
        bubble.addEventListener('click', function() {
            isOpen = !isOpen;
            widget.style.display = isOpen ? 'flex' : 'none';
            bubble.style.transform = isOpen ? 'scale(0.9)' : 'scale(1)';
            
            if (isOpen) {
                notification.style.display = 'none';
                input.focus();
            }
        });
        
        // Close chat
        closeBtn.addEventListener('click', function() {
            isOpen = false;
            widget.style.display = 'none';
            bubble.style.transform = 'scale(1)';
        });
        
        // Send message
        function sendMessage() {
            const message = input.value.trim();
            if (!message) return;
            
            addMessage(message, 'user');
            input.value = '';
            
            // Process response
            setTimeout(() => {
                const response = getResponse(message);
                addMessage(response, 'bot');
            }, 500);
        }
        
        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
    
    function addMessage(text, sender) {
        const messagesContainer = document.getElementById('chat-messages');
        const isUser = sender === 'user';
        
        const messageHTML = `
            <div style="
                background: ${isUser ? 'linear-gradient(135deg, #43C4E3 0%, #2D3954 100%)' : 'white'} !important;
                color: ${isUser ? 'white' : '#333'} !important;
                padding: 12px !important;
                border-radius: 10px !important;
                margin-bottom: 10px !important;
                margin-left: ${isUser ? '50px' : '0'} !important;
                margin-right: ${isUser ? '0' : '50px'} !important;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1) !important;
            ">
                <div style="line-height: 1.4 !important;">${text}</div>
                <div style="font-size: 11px !important; opacity: 0.7 !important; margin-top: 5px !important;">
                    ${new Date().toLocaleTimeString()}
                </div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    function getResponse(message) {
        const msg = message.toLowerCase();

        // Enhanced keyword matching with intent detection
        const viewingKeywords = ['view', 'see', 'browse', 'look', 'check', 'show', 'display', 'watch'];
        const propertyKeywords = ['property', 'properties', 'home', 'homes', 'house', 'houses', 'listing', 'listings'];
        const searchKeywords = ['search', 'find', 'filter', 'locate'];
        const detailKeywords = ['detail', 'details', 'information', 'info', 'description'];
        const galleryKeywords = ['photo', 'photos', 'image', 'images', 'gallery', 'picture', 'pictures'];

        // Check for property viewing intent
        const hasViewingIntent = viewingKeywords.some(keyword => msg.includes(keyword));
        const hasPropertyContext = propertyKeywords.some(keyword => msg.includes(keyword));
        const hasSearchIntent = searchKeywords.some(keyword => msg.includes(keyword));
        const hasDetailIntent = detailKeywords.some(keyword => msg.includes(keyword));
        const hasGalleryIntent = galleryKeywords.some(keyword => msg.includes(keyword));

        // Specific property viewing responses
        if (hasViewingIntent && hasPropertyContext) {
            if (hasGalleryIntent) {
                return "📸 <strong>Viewing Property Photos & Gallery:</strong><br><br>" +
                       "1. <strong>Browse Properties:</strong> Click on any property card on the homepage<br>" +
                       "2. <strong>Property Details Page:</strong> Scroll to the image gallery section<br>" +
                       "3. <strong>Gallery Navigation:</strong> Click on thumbnail images or use arrow buttons<br>" +
                       "4. <strong>Full-Screen View:</strong> Click the main image to open in full-screen mode<br>" +
                       "5. <strong>Zoom Feature:</strong> Use mouse wheel or zoom controls for detailed viewing<br><br>" +
                       "💡 <em>Tip: Most properties have 10+ high-quality photos showing interior, exterior, and amenities!</em>";
            }

            if (hasDetailIntent) {
                return "📋 <strong>Viewing Property Details:</strong><br><br>" +
                       "1. <strong>Find a Property:</strong> Use search filters or browse homepage listings<br>" +
                       "2. <strong>Click Property Card:</strong> Click anywhere on the property card<br>" +
                       "3. <strong>Property Details Page:</strong> View comprehensive information including:<br>" +
                       "   • Price, size, bedrooms, bathrooms<br>" +
                       "   • Property description and features<br>" +
                       "   • Location map and neighborhood info<br>" +
                       "   • Agent contact information<br>" +
                       "   • Similar properties nearby<br><br>" +
                       "💡 <em>Tip: Scroll down to see all details, amenities, and contact options!</em>";
            }

            return "🏠 <strong>How to View Properties on PropNepal:</strong><br><br>" +
                   "1. <strong>Homepage Browsing:</strong> Scroll down to see featured properties<br>" +
                   "2. <strong>Search Method:</strong> Use the search bar with location and filters<br>" +
                   "3. <strong>Category Browsing:</strong> Click 'Property' in the menu → 'All Categories'<br>" +
                   "4. <strong>Filter Options:</strong> Select location, property type, and price range<br>" +
                   "5. <strong>View Details:</strong> Click any property card to see full details<br><br>" +
                   "🔍 <strong>Quick Tip:</strong> Use the BUY/RENT toggle on homepage to filter by transaction type!";
        }

        // Enhanced search responses
        if ((hasSearchIntent && hasPropertyContext) || (msg.includes('search') && (msg.includes('property') || msg.includes('home')))) {
            return "🔍 <strong>Property Search Guide:</strong><br><br>" +
                   "1. <strong>Homepage Search:</strong> Use the main search bar at the top<br>" +
                   "2. <strong>Location Filter:</strong> Select or type your preferred location<br>" +
                   "3. <strong>Property Type:</strong> Choose from dropdown (House, Apartment, Villa, etc.)<br>" +
                   "4. <strong>Price Range:</strong> Set minimum and maximum price limits<br>" +
                   "5. <strong>Advanced Filters:</strong> Bedrooms, bathrooms, size, amenities<br>" +
                   "6. <strong>Search Results:</strong> Browse filtered properties with photos and details<br><br>" +
                   "💡 <strong>Pro Tips:</strong><br>" +
                   "• Use 'BUY' or 'RENT' buttons to filter transaction type<br>" +
                   "• Save favorite properties to your wishlist ❤️<br>" +
                   "• Compare multiple properties side-by-side ⚖️";
        }

        // Agent responses
        if (msg.includes('agent') || msg.includes('contact') || (msg.includes('call') && msg.includes('agent'))) {
            return "👥 <strong>Contacting Agents:</strong><br><br>" +
                   "1. <strong>Find Agents:</strong> Click 'Agents' in the main menu<br>" +
                   "2. <strong>Agent Profiles:</strong> View agent photos, experience, and listings<br>" +
                   "3. <strong>Contact Methods:</strong><br>" +
                   "   • Phone: Direct call from property details<br>" +
                   "   • Email: Send message through contact form<br>" +
                   "   • Chat: Use our messaging system<br>" +
                   "4. <strong>Property-Specific:</strong> Contact agent directly from any property listing<br><br>" +
                   "📞 <strong>Quick Contact:</strong> Every property shows the agent's phone and email for immediate contact!";
        }

        // Buy/Rent responses with specific steps
        if (msg.includes('buy') || msg.includes('purchase') || msg.includes('buying')) {
            return "🏡 <strong>Buying Property Guide:</strong><br><br>" +
                   "1. <strong>Browse Properties:</strong> Use search filters to find homes for sale<br>" +
                   "2. <strong>Set Budget:</strong> Use price filters to match your budget<br>" +
                   "3. <strong>Property Details:</strong> Review all information, photos, and location<br>" +
                   "4. <strong>Contact Agent:</strong> Call or message the listing agent<br>" +
                   "5. <strong>Schedule Viewing:</strong> Arrange property visit with agent<br>" +
                   "6. <strong>Make Offer:</strong> Work with agent to submit your offer<br><br>" +
                   "💰 <strong>Tip:</strong> Save multiple properties to your wishlist to compare options before deciding!";
        }

        if (msg.includes('rent') || msg.includes('rental') || msg.includes('lease')) {
            return "🏠 <strong>Renting Property Guide:</strong><br><br>" +
                   "1. <strong>Filter for Rentals:</strong> Click 'RENT' button on homepage<br>" +
                   "2. <strong>Set Preferences:</strong> Choose location, budget, and property type<br>" +
                   "3. <strong>Check Availability:</strong> View current rental listings<br>" +
                   "4. <strong>Property Details:</strong> Review rent amount, deposit, and lease terms<br>" +
                   "5. <strong>Contact Owner/Agent:</strong> Use provided contact information<br>" +
                   "6. <strong>Schedule Viewing:</strong> Arrange property inspection<br>" +
                   "7. <strong>Apply:</strong> Submit rental application with required documents<br><br>" +
                   "📋 <strong>Tip:</strong> Check property descriptions for lease duration and included amenities!";
        }

        // Enhanced wishlist responses
        if (msg.includes('wishlist') || msg.includes('favorite') || msg.includes('save') || (msg.includes('heart') && msg.includes('property'))) {
            return "❤️ <strong>Using Your Wishlist:</strong><br><br>" +
                   "1. <strong>Add to Wishlist:</strong> Click the heart ❤️ icon on any property card<br>" +
                   "2. <strong>Access Wishlist:</strong> Go to your Dashboard → 'My Wishlist'<br>" +
                   "3. <strong>Manage Favorites:</strong> View all saved properties in one place<br>" +
                   "4. <strong>Remove Items:</strong> Click the heart again or use remove button<br>" +
                   "5. <strong>Quick Actions:</strong> Contact agents directly from wishlist<br>" +
                   "6. <strong>Compare Saved:</strong> Add wishlist items to comparison tool<br><br>" +
                   "💡 <strong>Tip:</strong> Your wishlist is saved to your account and accessible from any device!";
        }

        // Enhanced compare responses
        if (msg.includes('compare') || msg.includes('comparison') || (msg.includes('side') && msg.includes('side'))) {
            return "⚖️ <strong>Property Comparison Tool:</strong><br><br>" +
                   "1. <strong>Add to Compare:</strong> Click the compare icon on property cards<br>" +
                   "2. <strong>Access Comparison:</strong> Go to Dashboard → 'Compare Properties'<br>" +
                   "3. <strong>Side-by-Side View:</strong> See detailed comparison table<br>" +
                   "4. <strong>Compare Features:</strong><br>" +
                   "   • Price and size comparison<br>" +
                   "   • Location and amenities<br>" +
                   "   • Photos and descriptions<br>" +
                   "   • Agent contact information<br>" +
                   "5. <strong>Make Decision:</strong> Use comparison to choose the best property<br><br>" +
                   "📊 <strong>Tip:</strong> You can compare up to 4 properties at once for detailed analysis!";
        }

        // Enhanced account/dashboard responses
        if (msg.includes('account') || msg.includes('dashboard') || msg.includes('profile') || msg.includes('login') || msg.includes('register')) {
            return "📱 <strong>Your PropNepal Dashboard:</strong><br><br>" +
                   "1. <strong>Access Dashboard:</strong> Click 'Dashboard' in top-right menu<br>" +
                   "2. <strong>Account Features:</strong><br>" +
                   "   • My Wishlist: View saved properties<br>" +
                   "   • Compare Properties: Side-by-side comparison<br>" +
                   "   • Profile Settings: Update personal information<br>" +
                   "   • Search History: Review past searches<br>" +
                   "   • Messages: Chat with agents<br>" +
                   "3. <strong>Profile Management:</strong> Update contact info and preferences<br>" +
                   "4. <strong>Property Tracking:</strong> Monitor saved and compared properties<br><br>" +
                   "🔐 <strong>Account Access:</strong> Login/Register using the top-right menu buttons!";
        }

        // Navigation and general help
        if (msg.includes('navigate') || msg.includes('navigation') || msg.includes('menu') || msg.includes('where')) {
            return "🧭 <strong>Site Navigation Guide:</strong><br><br>" +
                   "1. <strong>Main Menu:</strong> Home | Property | Agents | Blog<br>" +
                   "2. <strong>Property Section:</strong> All Categories, Property Types<br>" +
                   "3. <strong>Search Tools:</strong> Homepage search bar with filters<br>" +
                   "4. <strong>User Area:</strong> Dashboard, Login/Register (top-right)<br>" +
                   "5. <strong>Quick Actions:</strong><br>" +
                   "   • BUY/RENT toggle on homepage<br>" +
                   "   • Heart icons for wishlist<br>" +
                   "   • Compare icons for comparison<br>" +
                   "   • Agent contact on property pages<br><br>" +
                   "🎯 <strong>Quick Tip:</strong> Use the search bar on any page to find properties instantly!";
        }

        // Price and budget related queries
        if (msg.includes('price') || msg.includes('cost') || msg.includes('budget') || msg.includes('expensive') || msg.includes('cheap')) {
            return "💰 <strong>Property Pricing Information:</strong><br><br>" +
                   "1. <strong>Price Display:</strong> All prices shown in NPR (Nepalese Rupee)<br>" +
                   "2. <strong>Price Filters:</strong> Set minimum and maximum budget in search<br>" +
                   "3. <strong>Price Comparison:</strong> Use compare tool to analyze pricing<br>" +
                   "4. <strong>Budget Planning:</strong><br>" +
                   "   • Filter by your budget range<br>" +
                   "   • Compare similar properties<br>" +
                   "   • Contact agents for negotiation<br>" +
                   "5. <strong>Additional Costs:</strong> Check property details for maintenance fees<br><br>" +
                   "📈 <strong>Tip:</strong> Save multiple properties in different price ranges to compare value!";
        }

        // Location-based queries
        if (msg.includes('location') || msg.includes('area') || msg.includes('neighborhood') || msg.includes('where') || msg.includes('map')) {
            return "📍 <strong>Location & Area Information:</strong><br><br>" +
                   "1. <strong>Location Search:</strong> Use location dropdown in search filters<br>" +
                   "2. <strong>Area Browsing:</strong> Browse properties by specific neighborhoods<br>" +
                   "3. <strong>Map View:</strong> See property locations on interactive map<br>" +
                   "4. <strong>Neighborhood Info:</strong> Each property shows area details<br>" +
                   "5. <strong>Nearby Properties:</strong> View similar properties in same area<br>" +
                   "6. <strong>Location Benefits:</strong> Check proximity to schools, hospitals, markets<br><br>" +
                   "🗺️ <strong>Tip:</strong> Click on property details to see exact location and nearby amenities!";
        }

        // Specific help requests
        if (msg.includes('help') || msg.includes('how') || msg.includes('guide') || msg.includes('tutorial') || msg.includes('instructions')) {
            return "🆘 <strong>PropNepal Help Center:</strong><br><br>" +
                   "I can provide specific guidance on:<br><br>" +
                   "🏠 <strong>Property Viewing:</strong> 'How to view properties?'<br>" +
                   "🔍 <strong>Search & Filters:</strong> 'How to search properties?'<br>" +
                   "👥 <strong>Agent Contact:</strong> 'How to contact agents?'<br>" +
                   "❤️ <strong>Wishlist:</strong> 'How to save favorites?'<br>" +
                   "⚖️ <strong>Comparison:</strong> 'How to compare properties?'<br>" +
                   "💰 <strong>Buying/Renting:</strong> 'How to buy/rent?'<br>" +
                   "📱 <strong>Dashboard:</strong> 'How to use dashboard?'<br><br>" +
                   "💡 <strong>Ask me specific questions</strong> like 'How to view property photos?' for detailed step-by-step instructions!";
        }

        // Intelligent fallback response for non-PropNepal related queries
        return "🏠 I'm sorry, I'm a specialized assistant for the PropNepal real estate platform only. I can help you with:<br><br>" +
               "• Property viewing and search<br>" +
               "• Buying and renting guidance<br>" +
               "• Agent contact information<br>" +
               "• Wishlist and comparison features<br>" +
               "• Dashboard and account management<br>" +
               "• Site navigation and pricing<br><br>" +
               "Please ask me anything related to PropNepal's real estate services, and I'll provide detailed step-by-step assistance!";
    }
    
    // Initialize when DOM is ready
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', createChatbot);
        } else {
            createChatbot();
        }
    }
    
    // Start initialization
    init();
    
})();
