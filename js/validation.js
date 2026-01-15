/**
 * ACT Test Prep - Form Validation
 */

const Validation = {
    /**
     * Validate email
     */
    email(value) {
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(value);
    },

    /**
     * Validate password
     */
    password(value, minLength = 8) {
        return value && value.length >= minLength;
    },

    /**
     * Validate password strength
     */
    passwordStrength(value) {
        let strength = 0;
        
        if (value.length >= 8) strength++;
        if (value.length >= 12) strength++;
        if (/[a-z]/.test(value)) strength++;
        if (/[A-Z]/.test(value)) strength++;
        if (/[0-9]/.test(value)) strength++;
        if (/[^a-zA-Z0-9]/.test(value)) strength++;

        if (strength <= 2) return { level: 'weak', score: strength };
        if (strength <= 4) return { level: 'medium', score: strength };
        return { level: 'strong', score: strength };
    },

    /**
     * Validate required field
     */
    required(value) {
        return value !== null && value !== undefined && value.toString().trim() !== '';
    },

    /**
     * Validate min length
     */
    minLength(value, min) {
        return value && value.length >= min;
    },

    /**
     * Validate max length
     */
    maxLength(value, max) {
        return !value || value.length <= max;
    },

    /**
     * Validate numeric
     */
    numeric(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    },

    /**
     * Validate integer
     */
    integer(value) {
        return Number.isInteger(Number(value));
    },

    /**
     * Validate range
     */
    range(value, min, max) {
        const num = parseFloat(value);
        return !isNaN(num) && num >= min && num <= max;
    },

    /**
     * Validate pattern (regex)
     */
    pattern(value, regex) {
        return regex.test(value);
    },

    /**
     * Validate match (two values equal)
     */
    match(value1, value2) {
        return value1 === value2;
    },

    /**
     * Validate URL
     */
    url(value) {
        try {
            new URL(value);
            return true;
        } catch {
            return false;
        }
    },

    /**
     * Validate date
     */
    date(value) {
        const date = new Date(value);
        return date instanceof Date && !isNaN(date);
    },

    /**
     * Validate date in future
     */
    futureDate(value) {
        const date = new Date(value);
        return this.date(value) && date > new Date();
    },

    /**
     * Validate date in past
     */
    pastDate(value) {
        const date = new Date(value);
        return this.date(value) && date < new Date();
    },

    /**
     * Validate form
     */
    validateForm(formData, rules) {
        const errors = {};
        let isValid = true;

        for (const [field, fieldRules] of Object.entries(rules)) {
            const value = formData[field];
            const fieldErrors = [];

            for (const rule of fieldRules) {
                let valid = true;
                let message = '';

                if (typeof rule === 'string') {
                    // Simple rule name
                    switch (rule) {
                        case 'required':
                            valid = this.required(value);
                            message = 'This field is required';
                            break;
                        case 'email':
                            valid = !value || this.email(value);
                            message = 'Please enter a valid email address';
                            break;
                        case 'url':
                            valid = !value || this.url(value);
                            message = 'Please enter a valid URL';
                            break;
                        case 'numeric':
                            valid = !value || this.numeric(value);
                            message = 'Please enter a number';
                            break;
                        case 'integer':
                            valid = !value || this.integer(value);
                            message = 'Please enter a whole number';
                            break;
                    }
                } else if (typeof rule === 'object') {
                    // Rule with parameters
                    const { type, value: param, message: customMessage } = rule;

                    switch (type) {
                        case 'required':
                            valid = this.required(value);
                            message = customMessage || 'This field is required';
                            break;
                        case 'minLength':
                            valid = !value || this.minLength(value, param);
                            message = customMessage || `Must be at least ${param} characters`;
                            break;
                        case 'maxLength':
                            valid = this.maxLength(value, param);
                            message = customMessage || `Must be no more than ${param} characters`;
                            break;
                        case 'min':
                            valid = !value || parseFloat(value) >= param;
                            message = customMessage || `Must be at least ${param}`;
                            break;
                        case 'max':
                            valid = !value || parseFloat(value) <= param;
                            message = customMessage || `Must be no more than ${param}`;
                            break;
                        case 'range':
                            valid = !value || this.range(value, param.min, param.max);
                            message = customMessage || `Must be between ${param.min} and ${param.max}`;
                            break;
                        case 'pattern':
                            valid = !value || this.pattern(value, param);
                            message = customMessage || 'Invalid format';
                            break;
                        case 'match':
                            valid = this.match(value, formData[param]);
                            message = customMessage || 'Fields do not match';
                            break;
                        case 'custom':
                            valid = param(value, formData);
                            message = customMessage || 'Invalid value';
                            break;
                    }
                }

                if (!valid) {
                    fieldErrors.push(message);
                }
            }

            if (fieldErrors.length > 0) {
                errors[field] = fieldErrors;
                isValid = false;
            }
        }

        return { isValid, errors };
    },

    /**
     * Show validation errors on form
     */
    showErrors(form, errors) {
        // Clear previous errors
        this.clearErrors(form);

        for (const [field, messages] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                // Add error class
                input.classList.add('border-red-500', 'focus:border-red-500');
                
                // Add error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'form-error text-red-500 text-sm mt-1';
                errorDiv.textContent = messages[0];
                input.parentNode.appendChild(errorDiv);
            }
        }
    },

    /**
     * Clear validation errors from form
     */
    clearErrors(form) {
        // Remove error classes
        form.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'focus:border-red-500');
        });

        // Remove error messages
        form.querySelectorAll('.form-error').forEach(el => el.remove());
    },

    /**
     * Setup real-time validation
     */
    setupRealTimeValidation(form, rules) {
        for (const field of Object.keys(rules)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.addEventListener('blur', () => {
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData.entries());
                    const fieldRules = { [field]: rules[field] };
                    const result = this.validateForm(data, fieldRules);
                    
                    // Clear previous error for this field
                    input.classList.remove('border-red-500', 'focus:border-red-500');
                    const existingError = input.parentNode.querySelector('.form-error');
                    if (existingError) existingError.remove();

                    // Show error if any
                    if (result.errors[field]) {
                        input.classList.add('border-red-500', 'focus:border-red-500');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'form-error text-red-500 text-sm mt-1';
                        errorDiv.textContent = result.errors[field][0];
                        input.parentNode.appendChild(errorDiv);
                    }
                });
            }
        }
    }
};

// Make available globally
window.Validation = Validation;
