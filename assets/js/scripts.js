// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Handle flash messages fade out
    const flashMessages = document.querySelectorAll('.alert-message');
    
    flashMessages.forEach(function(message) {
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
    
    // Image preview functionality for file uploads
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.addEventListener('load', function() {
                    // Create image element if it doesn't exist
                    let previewImg = imagePreview.querySelector('img');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.className = 'max-w-xs rounded';
                        imagePreview.appendChild(previewImg);
                    }
                    
                    // Set the image source and show the preview
                    previewImg.src = reader.result;
                    imagePreview.classList.remove('hidden');
                });
                
                reader.readAsDataURL(file);
            } else {
                // Hide the preview if no file is selected
                imagePreview.classList.add('hidden');
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
                    input.classList.add('is-invalid');
                    
                    // Create error message if it doesn't exist
                    let errorMessage = input.nextElementSibling;
                    
                    if (!errorMessage || !errorMessage.classList.contains('invalid-feedback')) {
                        errorMessage = document.createElement('p');
                        errorMessage.classList.add('invalid-feedback');
                        errorMessage.textContent = 'This field is required';
                        input.parentNode.insertBefore(errorMessage, input.nextSibling);
                    }
                } else {
                    input.classList.remove('is-invalid');
                    
                    // Remove error message if it exists
                    const errorMessage = input.nextElementSibling;
                    
                    if (errorMessage && errorMessage.classList.contains('invalid-feedback')) {
                        errorMessage.remove();
                    }
                }
            });
            
            if (hasError) {
                event.preventDefault();
            }
        });
    });
    
    // Add hover effect to item cards
    const itemCards = document.querySelectorAll('.item-card');
    
    itemCards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.classList.add('shadow-lg');
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('shadow-lg');
        });
    });
    
    // Email field validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    
    emailInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            const email = this.value.trim();
            
            if (email && !isValidEmail(email)) {
                this.classList.add('is-invalid');
                
                // Create error message if it doesn't exist
                let errorMessage = this.nextElementSibling;
                
                if (!errorMessage || !errorMessage.classList.contains('invalid-feedback')) {
                    errorMessage = document.createElement('p');
                    errorMessage.classList.add('invalid-feedback');
                    errorMessage.textContent = 'Please enter a valid email address';
                    this.parentNode.insertBefore(errorMessage, this.nextSibling);
                }
            } else if (email) {
                this.classList.remove('is-invalid');
                
                // Remove error message if it exists
                const errorMessage = this.nextElementSibling;
                
                if (errorMessage && errorMessage.classList.contains('invalid-feedback')) {
                    errorMessage.remove();
                }
            }
        });
    });
    
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            
            // Get or create strength indicator
            let strengthIndicator = document.getElementById('password-strength');
            
            if (!strengthIndicator) {
                strengthIndicator = document.createElement('div');
                strengthIndicator.id = 'password-strength';
                strengthIndicator.className = 'mt-1';
                this.parentNode.insertBefore(strengthIndicator, this.nextSibling);
            }
            
            // Set strength indicator content based on password strength
            if (password.length === 0) {
                strengthIndicator.innerHTML = '';
            } else if (strength < 3) {
                strengthIndicator.innerHTML = '<span class="text-red-500 text-sm">Weak password</span>';
            } else if (strength < 5) {
                strengthIndicator.innerHTML = '<span class="text-yellow-500 text-sm">Moderate password</span>';
            } else {
                strengthIndicator.innerHTML = '<span class="text-green-500 text-sm">Strong password</span>';
            }
        });
    }
    
    // Helper function to validate email
    function isValidEmail(email) {
        const re = /^(([^<>()$$$$\\.,;:\s@"]+(\.[^<>()$$$$\\.,;:\s@"]+)*)|(".+"))@(($$[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$$)|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Helper function to check password strength
    function getPasswordStrength(password) {
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 1;
        
        // Contains lowercase letters
        if (/[a-z]/.test(password)) strength += 1;
        
        // Contains uppercase letters
        if (/[A-Z]/.test(password)) strength += 1;
        
        // Contains numbers
        if (/[0-9]/.test(password)) strength += 1;
        
        // Contains special characters
        if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
        
        return strength;
    }
});