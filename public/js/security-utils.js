/**
 * Security Utilities for Data Masking and Credential Verification
 * TriadCo Hotel Management System
 */

const SecurityUtils = {
    /**
     * Current verification token (stored after successful verification)
     */
    verificationToken: null,
    tokenExpiry: null,

    /**
     * Show credential verification modal
     * @param {string} action - The action being performed
     * @param {function} onSuccess - Callback when verification succeeds
     */
    verifyCredentials: function(action, onSuccess) {
        // Create and show modal
        const modal = this.createVerificationModal(action, onSuccess);
        document.body.appendChild(modal);
        modal.style.display = 'flex';
        modal.querySelector('input[type="password"]').focus();
    },

    /**
     * Create verification modal HTML
     */
    createVerificationModal: function(action, onSuccess) {
        const modal = document.createElement('div');
        modal.id = 'credentialVerificationModal';
        modal.innerHTML = `
            <div class="security-modal-backdrop" onclick="SecurityUtils.closeVerificationModal()"></div>
            <div class="security-modal-content">
                <div class="security-modal-header">
                    <i class="bi bi-shield-lock" style="font-size: 48px; color: #c8a858;"></i>
                    <h3>Verify Your Identity</h3>
                    <p>Please enter your password to continue</p>
                </div>
                <form id="credentialVerificationForm" onsubmit="return SecurityUtils.submitVerification(event, '${action}')">
                    <div class="security-modal-body">
                        <div class="form-group">
                            <label for="verificationPassword">Password</label>
                            <input type="password" id="verificationPassword" name="password" required 
                                   placeholder="Enter your password" autocomplete="current-password">
                        </div>
                        <div id="verificationError" class="verification-error" style="display: none;"></div>
                    </div>
                    <div class="security-modal-footer">
                        <button type="button" class="btn-secondary" onclick="SecurityUtils.closeVerificationModal()">Cancel</button>
                        <button type="submit" class="btn-primary" id="verifyBtn">
                            <span class="btn-text">Verify</span>
                            <span class="btn-loading" style="display: none;">
                                <i class="bi bi-arrow-repeat spin"></i> Verifying...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        `;
        modal.className = 'security-modal';
        modal.dataset.onSuccess = onSuccess ? onSuccess.toString() : '';
        modal._onSuccess = onSuccess;
        return modal;
    },

    /**
     * Submit verification request
     */
    submitVerification: async function(event, action) {
        event.preventDefault();
        
        const modal = document.getElementById('credentialVerificationModal');
        const password = document.getElementById('verificationPassword').value;
        const errorDiv = document.getElementById('verificationError');
        const verifyBtn = document.getElementById('verifyBtn');
        
        // Show loading state
        verifyBtn.querySelector('.btn-text').style.display = 'none';
        verifyBtn.querySelector('.btn-loading').style.display = 'inline';
        verifyBtn.disabled = true;
        errorDiv.style.display = 'none';

        try {
            const response = await fetch('/security/verify-credentials', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    password: password,
                    action: action
                })
            });

            const data = await response.json();

            if (data.success) {
                this.verificationToken = data.verification_token;
                this.tokenExpiry = Date.now() + (data.expires_in * 1000);
                
                // Call success callback
                if (modal._onSuccess) {
                    modal._onSuccess(data.verification_token);
                }
                
                this.closeVerificationModal();
            } else {
                errorDiv.textContent = data.message || 'Verification failed. Please try again.';
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.style.display = 'block';
        } finally {
            verifyBtn.querySelector('.btn-text').style.display = 'inline';
            verifyBtn.querySelector('.btn-loading').style.display = 'none';
            verifyBtn.disabled = false;
        }

        return false;
    },

    /**
     * Close verification modal
     */
    closeVerificationModal: function() {
        const modal = document.getElementById('credentialVerificationModal');
        if (modal) {
            modal.remove();
        }
    },

    /**
     * Check if we have a valid verification token
     */
    hasValidToken: function() {
        return this.verificationToken && this.tokenExpiry && Date.now() < this.tokenExpiry;
    },

    /**
     * Unmask sensitive data
     * @param {string} recordType - 'employee', 'supplier', or 'user'
     * @param {number} recordId - The record ID
     * @param {string} dataType - Type of data: 'phone', 'email', 'sss', 'address', etc.
     * @param {HTMLElement} element - The element to update with unmasked data
     */
    unmaskData: async function(recordType, recordId, dataType, element) {
        const doUnmask = async (token) => {
            try {
                element.innerHTML = '<i class="bi bi-arrow-repeat spin"></i>';
                
                const response = await fetch('/security/unmask-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        verification_token: token,
                        record_type: recordType,
                        record_id: recordId,
                        data_type: dataType
                    })
                });

                const data = await response.json();

                if (data.success) {
                    element.textContent = data.data;
                    element.classList.add('unmasked');
                    element.classList.remove('masked');
                } else {
                    alert(data.message || 'Failed to unmask data');
                    element.textContent = element.dataset.maskedValue || '***';
                }
            } catch (error) {
                alert('An error occurred while unmasking data');
                element.textContent = element.dataset.maskedValue || '***';
            }
        };

        if (this.hasValidToken()) {
            await doUnmask(this.verificationToken);
        } else {
            this.verifyCredentials('unmask_' + dataType, async (token) => {
                await doUnmask(token);
            });
        }
    }
};

// Add CSS for modals and animations
const securityStyles = document.createElement('style');
securityStyles.textContent = `
    .security-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
    }
    
    .security-modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }
    
    .security-modal-content {
        position: relative;
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        width: 90%;
        max-width: 400px;
        animation: modalSlideIn 0.3s ease;
    }
    
    @keyframes modalSlideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .security-modal-header {
        text-align: center;
        padding: 30px 30px 20px;
        border-bottom: 1px solid #eee;
    }
    
    .security-modal-header h3 {
        margin: 15px 0 5px;
        color: #1e2a47;
        font-size: 1.4rem;
    }
    
    .security-modal-header p {
        margin: 0;
        color: #666;
        font-size: 0.9rem;
    }
    
    .security-modal-body {
        padding: 25px 30px;
    }
    
    .security-modal-body .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    
    .security-modal-body .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.2s;
    }
    
    .security-modal-body .form-group input:focus {
        outline: none;
        border-color: #c8a858;
    }
    
    .verification-error {
        margin-top: 15px;
        padding: 10px 15px;
        background: #f8d7da;
        color: #721c24;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    
    .security-modal-footer {
        padding: 20px 30px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #eee;
    }
    
    .security-modal-footer .btn-secondary {
        padding: 10px 20px;
        background: #e0e0e0;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        transition: background 0.2s;
    }
    
    .security-modal-footer .btn-secondary:hover {
        background: #d0d0d0;
    }
    
    .security-modal-footer .btn-primary {
        padding: 10px 25px;
        background: linear-gradient(135deg, #c8a858 0%, #d4b86a 100%);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 600;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .security-modal-footer .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(200, 168, 88, 0.4);
    }
    
    .security-modal-footer .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Masked data styling */
    .masked-data {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .masked-data .unmask-btn {
        padding: 4px 8px;
        background: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        color: #666;
        transition: all 0.2s;
    }
    
    .masked-data .unmask-btn:hover {
        background: #c8a858;
        color: white;
        border-color: #c8a858;
    }
    
    .masked-value {
        font-family: monospace;
        color: #666;
    }
    
    .masked-value.unmasked {
        color: #333;
    }
`;
document.head.appendChild(securityStyles);
