/**
 * NovaTech Gear - Frontend Application JavaScript
 * Handles live toast notifications, AJAX cart interactions,
 * real-time badge updates, image previews, and UI enhancements.
 */

document.addEventListener('DOMContentLoaded', () => {
    initToast();
    initAjaxCart();
    initImagePreview();
    initPasswordToggles();
});

/**
 * Toast Notification Dispatcher
 */
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('liveToast');
    if (!toastEl) return;

    const toastMessage = document.getElementById('toastMessage');
    const bgClass = type === 'success' ? 'bg-success' : (type === 'danger' || type === 'error' ? 'bg-danger' : 'bg-primary');
    
    // Reset background classes
    toastEl.className = `toast align-items-center text-white border-0 shadow ${bgClass}`;

    const icon = type === 'success' ? 'bi-check-circle-fill' : (type === 'danger' || type === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');
    toastMessage.innerHTML = `<i class="bi ${icon} fs-5"></i><span>${message}</span>`;

    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
    toast.show();
}

/**
 * Update all navbar cart badges
 */
function updateCartBadge(count) {
    document.querySelectorAll('.cart-count-badge').forEach(badge => {
        badge.textContent = count;
        badge.classList.remove('d-none');
        // Visual bounce animation
        badge.animate([
            { transform: 'scale(1)' },
            { transform: 'scale(1.35)' },
            { transform: 'scale(1)' }
        ], { duration: 300 });
    });
}

/**
 * AJAX Shopping Cart Handler
 */
function initAjaxCart() {
    // Intercept Add to Cart forms and buttons
    document.body.addEventListener('submit', async (e) => {
        const form = e.target.closest('form.ajax-add-cart');
        if (!form) return;

        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHtml = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';
        }

        try {
            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                if (data.cart_count !== undefined) {
                    updateCartBadge(data.cart_count);
                }
            } else {
                showToast(data.message || 'Could not add to cart.', 'danger');
            }
        } catch (err) {
            console.error('Cart error:', err);
            showToast('An unexpected error occurred.', 'danger');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    });
}

/**
 * Image Upload Live Preview
 */
function initImagePreview() {
    const input = document.getElementById('productImageInput');
    const preview = document.getElementById('imagePreviewContainer');
    if (!input || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.innerHTML = `
                    <div class="position-relative d-inline-block">
                        <img src="${e.target.result}" alt="Preview" class="img-thumbnail rounded shadow-sm" style="max-height: 180px;">
                        <span class="badge bg-success position-absolute top-0 end-0 m-1">New Image</span>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });
}

/**
 * Password Visibility Toggles
 */
function initPasswordToggles() {
    document.querySelectorAll('.password-toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            if (!targetInput) return;

            const icon = btn.querySelector('i');
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            } else {
                targetInput.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });
}

function initToast() {
    // If there is an existing flash alert, auto-dismiss after 6s
    const alerts = document.querySelectorAll('.alerts-container .alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {}
        }, 6000);
    });
}
