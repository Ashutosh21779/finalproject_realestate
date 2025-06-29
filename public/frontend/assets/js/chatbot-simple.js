/**
 * Simple RealShed Chatbot Test
 */

console.log('Simple Chatbot: Script loaded!');

// Add test indicator
document.body.insertAdjacentHTML('beforeend', '<div style="position:fixed;top:10px;right:10px;background:blue;color:white;padding:10px;z-index:9999;border-radius:5px;">Simple Chatbot Test</div>');

// Simple chatbot bubble
function createSimpleChatbot() {
    console.log('Creating simple chatbot...');
    
    const chatHTML = `
        <div id="simple-chat-bubble" style="
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #43C4E3 0%, #2D3954 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(67, 196, 227, 0.4);
            z-index: 1000;
            color: white;
            font-size: 24px;
        ">
            💬
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', chatHTML);
    
    // Add click event
    const bubble = document.getElementById('simple-chat-bubble');
    if (bubble) {
        bubble.addEventListener('click', function() {
            alert('Simple chatbot clicked! The chatbot is working.');
        });
        console.log('Simple chatbot created and event bound');
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', createSimpleChatbot);
} else {
    createSimpleChatbot();
}
