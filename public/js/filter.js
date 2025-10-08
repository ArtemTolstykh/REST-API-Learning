document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('sortSelect');
    const grid   = document.getElementById('productsGrid');

    async function reload(sortValue) {
        grid.setAttribute('aria-busy', 'true');
        try {
            const res  = await fetch('/ajax/sort-products.php?sort=' + encodeURIComponent(sortValue), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            const html = await res.text();
            grid.innerHTML = html;
            history.replaceState(null, '', `?sort=${encodeURIComponent(sortValue)}`);
        } finally {
            grid.removeAttribute('aria-busy');
        }
    }

    select.addEventListener('change', () => reload(select.value));
});