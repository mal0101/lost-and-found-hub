// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Handle flash messages fade out
    const flashMessages = document.querySelectorAll('.alert-message');
    
    flashMessages.forEach(function(message) {
        // Add animation class
        message.classList.add('alert-animation');
        
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            message.style.transition = 'opacity 1s';
            message.style.opacity = '0';
            
            // Remove the element after fade out
            setTimeout(function() {
                message.remove();
            }, 1000);
        }, 5000);
    });
    
    // Add hover effect to item cards
    const itemCards = document.querySelectorAll('.item-card');
    
    itemCards.forEach(function(card) {
        card.classList.add('card-hover');
    });
    
    // Image preview functionality for file uploads
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.addEventListener('load', function() {
                    imagePreview.src = reader.result;
                    imagePreview.style.display = 'block';
                });
                
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            let hasError = false;
            const requiredInputs = form.querySelectorAll('[required]');
            
            requiredInputs.forEach(function(input) {
                if (!input.value.trim()) {
                    hasError = true;
                    input.classList.add('border-red-500');
                    
                    // Create error message if it doesn't exist
                    let errorMessage = input.nextElementSibling;
                    
                    if (!errorMessage || !errorMessage.classList.contains('error-message')) {
                        errorMessage = document.createElement('p');
                        errorMessage.classList.add('error-message', 'text-red-500', 'text-xs', 'mt-1');
                        errorMessage.textContent = 'This field is required';
                        input.parentNode.insertBefore(errorMessage, input.nextSibling);
                    }
                } else {
                    input.classList.remove('border-red-500');
                    
                    // Remove error message if it exists
                    const errorMessage = input.nextElementSibling;
                    
                    if (errorMessage && errorMessage.classList.contains('error-message')) {
                        errorMessage.remove();
                    }
                }
            });
            
            if (hasError) {
                event.preventDefault();
            }
        });
    });
});