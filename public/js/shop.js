/* Progressive enhancement: standard Blade POST forms still work without JavaScript. */
document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-ajax-cart]');
    if (!form || !window.fetch) return;
    event.preventDefault();

    const button = form.querySelector('button[type="submit"], button:not([type])');
    if (button?.disabled) return;
    if (button) button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await response.json();
        if (!response.ok) {
            const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
            throw new Error(firstError || data.message || 'Không thể thêm sản phẩm.');
        }
        document.querySelectorAll('[data-cart-count]').forEach((element) => {
            element.textContent = String(data.cart_count ?? 0);
        });
        showCartNotice(data.message || 'Đã thêm vào giỏ hàng.', 'success');
    } catch (error) {
        showCartNotice(error.message || 'Có lỗi kết nối. Vui lòng thử lại.', 'danger');
    } finally {
        if (button) button.disabled = false;
    }
});

function showCartNotice(message, type) {
    const old = document.querySelector('[data-cart-notice]');
    if (old) old.remove();
    const container = document.createElement('div');
    container.dataset.cartNotice = '';
    container.className = `position-fixed bottom-0 start-50 translate-middle-x p-3 alert alert-${type} shadow-lg`;
    container.style.cssText = 'z-index:1090;max-width:min(92vw,460px);min-width:260px';
    container.setAttribute('role', 'status');
    container.setAttribute('aria-live', 'polite');
    const span = document.createElement('span');
    span.textContent = message;
    container.append(span);
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'btn-close ms-3 float-end';
    close.setAttribute('aria-label', 'Đóng thông báo');
    close.addEventListener('click', () => container.remove());
    container.append(close);
    document.body.append(container);
}
