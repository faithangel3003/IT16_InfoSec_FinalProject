/**
 * TriadCo Input Validation JavaScript
 * Task 6: Implement input field validation with red placeholder for empty fields
 * and restrict integer fields to numbers only.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all input validations
    initInputValidation();
});

/**
 * Initialize input field validation
 */
function initInputValidation() {
    // Get all forms in the page
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Add validation on form submit
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Find first invalid field and focus
                const firstInvalid = form.querySelector('.input-error');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
        
        // Add real-time validation on input
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            // Remove error state when user starts typing
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    clearFieldError(this);
                }
            });
            
            // Validate on blur
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required')) {
                    validateField(this);
                }
            });
        });
    });
    
    // Restrict integer fields to numbers only
    restrictNumericInputs();
}

/**
 * Validate a single field
 * @param {HTMLElement} field - The input field to validate
 * @returns {boolean} - Whether the field is valid
 */
function validateField(field) {
    const value = field.value.trim();
    
    // Check if field is empty and required
    if (field.hasAttribute('required') && value === '') {
        setFieldError(field);
        return false;
    }
    
    // Clear error if validation passes
    clearFieldError(field);
    return true;
}

/**
 * Set error state on a field
 * @param {HTMLElement} field - The input field
 */
function setFieldError(field) {
    field.classList.add('input-error');
    
    // Store original placeholder
    if (!field.dataset.originalPlaceholder) {
        field.dataset.originalPlaceholder = field.placeholder || '';
    }
    
    // Set error placeholder
    const fieldName = getFieldName(field);
    field.placeholder = fieldName + ' is required';
}

/**
 * Clear error state from a field
 * @param {HTMLElement} field - The input field
 */
function clearFieldError(field) {
    field.classList.remove('input-error');
    
    // Restore original placeholder
    if (field.dataset.originalPlaceholder !== undefined) {
        field.placeholder = field.dataset.originalPlaceholder;
    }
}

/**
 * Get human-readable field name
 * @param {HTMLElement} field - The input field
 * @returns {string} - The field name
 */
function getFieldName(field) {
    // Try to get label text
    const label = document.querySelector(`label[for="${field.id}"]`);
    if (label) {
        return label.textContent.replace('*', '').trim();
    }
    
    // Try to get name attribute and format it
    if (field.name) {
        return field.name
            .replace(/_/g, ' ')
            .replace(/([A-Z])/g, ' $1')
            .replace(/\[|\]/g, '')
            .trim()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    }
    
    // Fallback
    return 'This field';
}

/**
 * Restrict numeric inputs to numbers only
 */
function restrictNumericInputs() {
    // Select all inputs that should only accept numbers
    const numericSelectors = [
        'input[type="number"]',
        'input[data-type="number"]',
        'input[data-numeric="true"]',
        'input.numeric-only',
        // Common numeric field names
        'input[name*="quantity"]',
        'input[name*="price"]',
        'input[name*="cost"]',
        'input[name*="amount"]',
        'input[name*="stock"]',
        'input[name*="phone"]',
        'input[name*="age"]',
        'input[name*="year"]',
        'input[name*="number"]',
        'input[name*="count"]',
        'input[name*="total"]',
        'input[name*="rate"]',
        'input[name*="capacity"]',
        'input[name*="floor"]',
        'input[name*="room_number"]',
        'input[name*="unit_price"]',
        'input[name*="sale_price"]',
        'input[name*="purchase_price"]'
    ];
    
    const numericInputs = document.querySelectorAll(numericSelectors.join(', '));
    
    numericInputs.forEach(input => {
        // Prevent non-numeric key presses
        input.addEventListener('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter, decimal point
            if ([8, 9, 27, 13, 110, 190].includes(e.keyCode) ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey) ||
                (e.keyCode === 67 && e.ctrlKey) ||
                (e.keyCode === 86 && e.ctrlKey) ||
                (e.keyCode === 88 && e.ctrlKey) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            
            // Check if it's a number
            const char = String.fromCharCode(e.which);
            if (!/^[0-9.]$/.test(char)) {
                e.preventDefault();
                showNumericOnlyWarning(input);
            }
        });
        
        // Also handle paste event to filter non-numeric content
        input.addEventListener('paste', function(e) {
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (!/^[\d.]+$/.test(pastedText)) {
                e.preventDefault();
                showNumericOnlyWarning(input);
            }
        });
        
        // Clean up on input (remove any letters that might have been pasted)
        input.addEventListener('input', function() {
            const originalValue = this.value;
            // Allow decimal point only once
            let cleanValue = this.value.replace(/[^0-9.]/g, '');
            
            // Handle multiple decimal points
            const parts = cleanValue.split('.');
            if (parts.length > 2) {
                cleanValue = parts[0] + '.' + parts.slice(1).join('');
            }
            
            if (cleanValue !== originalValue) {
                this.value = cleanValue;
            }
        });
    });
}

/**
 * Show a brief warning that only numbers are allowed
 * @param {HTMLElement} input - The input field
 */
function showNumericOnlyWarning(input) {
    // Don't show multiple warnings
    if (input.dataset.warningActive) return;
    
    input.dataset.warningActive = 'true';
    input.classList.add('numeric-warning');
    
    // Create a tooltip warning
    const existingWarning = input.parentNode.querySelector('.numeric-warning-tip');
    if (!existingWarning) {
        const warning = document.createElement('span');
        warning.className = 'numeric-warning-tip';
        warning.textContent = 'Numbers only';
        warning.style.cssText = `
            position: absolute;
            top: -24px;
            left: 0;
            background: #ef4444;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            animation: fadeOut 1.5s forwards;
        `;
        
        // Ensure parent has relative positioning
        if (getComputedStyle(input.parentNode).position === 'static') {
            input.parentNode.style.position = 'relative';
        }
        
        input.parentNode.appendChild(warning);
        
        setTimeout(() => {
            warning.remove();
            input.classList.remove('numeric-warning');
            delete input.dataset.warningActive;
        }, 1500);
    }
}

// Add CSS for warnings dynamically
const validationStyleEl = document.createElement('style');
validationStyleEl.textContent = `
    .input-error {
        border: 2px solid #ef4444 !important;
        background-color: #fef2f2 !important;
    }
    
    .input-error::placeholder {
        color: #ef4444 !important;
        opacity: 1 !important;
    }
    
    .numeric-warning {
        border-color: #ef4444 !important;
        animation: shake 0.3s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    @keyframes fadeOut {
        0% { opacity: 1; }
        70% { opacity: 1; }
        100% { opacity: 0; }
    }
`;
document.head.appendChild(validationStyleEl);
