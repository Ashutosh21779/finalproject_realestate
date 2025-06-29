/**
 * Minimal RealShed Chatbot for Testing
 */

console.log('Minimal Chatbot: Script loaded');

// Create minimal chatbot immediately
function createMinimalChatbot() {
    console.log('Creating minimal chatbot...');
    
    // Remove any existing chatbot
    const existing = document.getElementById('minimal-chat-bubble');
    if (existing) existing.remove();
    
    // Create simple bubble with inline styles
    const bubble = document.createElement('div');
    bubble.id = 'minimal-chat-bubble';
    bubble.innerHTML = '💬';
    bubble.style.cssText = `
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
    `;
    
    // Add click handler
    bubble.addEventListener('click', function() {
        alert('🎉 Minimal chatbot is working!\n\nThis confirms that:\n✅ JavaScript is loading\n✅ DOM manipulation works\n✅ Event handlers work\n✅ CSS positioning works');
    });
    
    // Add to page
    document.body.appendChild(bubble);
    
    console.log('Minimal chatbot created successfully');
    
    // Add success indicator
    const indicator = document.createElement('div');
    indicator.style.cssText = `
        position: fixed !important;
        top: 10px !important;
        right: 10px !important;
        background: green !important;
        color: white !important;
        padding: 10px !important;
        border-radius: 5px !important;
        z-index: 999999 !important;
        font-family: Arial, sans-serif !important;
        font-size: 14px !important;
    `;
    indicator.textContent = '✅ Minimal Chatbot Active';
    document.body.appendChild(indicator);
    
    // Remove indicator after 3 seconds
    setTimeout(() => {
        if (indicator.parentNode) {
            indicator.parentNode.removeChild(indicator);
        }
    }, 3000);
}

// Initialize immediately and with multiple fallbacks
createMinimalChatbot();

// DOM ready fallback
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', createMinimalChatbot);
} else {
    setTimeout(createMinimalChatbot, 100);
}

// Window load fallback
window.addEventListener('load', createMinimalChatbot);

// Final fallback
setTimeout(createMinimalChatbot, 2000);

console.log('Minimal Chatbot: All initialization methods set up');
