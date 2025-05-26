/**
 * Vafa Chat Widget CORS and API Access Fix
 * This script patches the fetch API to ensure CORS requests work properly
 * and adds the assistantId parameter to the widget initialization
 */

(function() {
    // Wait for the VafaChatWidget to be available
    function initVafaChatWithCorsSupport() {
        if (typeof VafaChatWidget === 'undefined' || !window.vafaChatSettings) {
            console.log('Waiting for VafaChatWidget to load...');
            setTimeout(initVafaChatWithCorsSupport, 100);
            return;
        }

        console.log('Initializing Vafa Chat Widget with CORS support...');

        // Patch the fetch API to handle CORS properly
        const originalFetch = window.fetch;
        window.fetch = function(url, options) {
            // Add CORS mode to all fetch requests
            if (options === undefined) {
                options = {};
            }
            options.mode = "cors";
            options.credentials = "omit";
            
            // Add necessary headers
            if (!options.headers) {
                options.headers = {};
            }
            
            console.log('Patched fetch request to:', url);
            
            // Call the original fetch with our modified options
            return originalFetch(url, options);
        };

        // Check if the container already exists
        let chatContainer = document.getElementById("vafa-chat-container");
        
        // Create container if it doesn't exist
        if (!chatContainer) {
            chatContainer = document.createElement("div");
            chatContainer.id = "vafa-chat-container";
            document.body.appendChild(chatContainer);
        }
        
        // Initialize widget with settings
        VafaChatWidget.init("#vafa-chat-container", {
            // Basic settings
            token: vafaChatSettings.token,
            assistantId: vafaChatSettings.token, // Use token as assistantId
            welcomeTitle: vafaChatSettings.welcomeTitle,
            initialMessage: vafaChatSettings.initialMessage,
            defaultQuestion: vafaChatSettings.defaultQuestion,
            suggestedQuestions: vafaChatSettings.suggestedQuestions,
            
            // API Configuration
            apiBaseUrl: vafaChatSettings.apiBaseUrl,
            apiTimeout: vafaChatSettings.apiTimeout,
            apiRetryAttempts: vafaChatSettings.apiRetryAttempts,
            
            // User data
            userData: vafaChatSettings.userData,
        });
    }

    // Start initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVafaChatWithCorsSupport);
    } else {
        initVafaChatWithCorsSupport();
    }
})();
