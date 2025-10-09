document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('sortSelect');
    const refresh = document.getElementById('refreshBtn');
    const target   = document.querySelector('[data-list]');

    if (!target) return;

    const isAdmin = (target.dataset.list || '').startsWith('admin');
    const view = isAdmin ? 'admin' : 'cards';

    const params = new URLSearchParams(location.search);
    if (params.has('sort')) {
        const v = params.get('sort');
        if (['title', 'price', '-price'].includes(v)) select.value = v; //TODO проверить
    }
    const currentSort = () => (select?.value || 'title');

    async function load(sort) {
        target.setAttribute('aria-busy', 'true');
        try {
            const res  = await fetch(`/ajax/sort-products.php?sort=${encodeURIComponent(sort)}&view=${view}`,{
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Ошибка сети');
            target.innerHTML = await res.text();
            history.replaceState(null, '', `?sort=${encodeURIComponent(sort)}`);
        } catch (e) {
            console.error(e);
            target.innerHTML = isAdmin
                ? '<tr><td colspan="6">Ошибка загрузки</td></tr>'
                : '<div class="error">Ошибка загрузки</div>';
        } finally {
            target.removeAttribute('aria-busy');
        }
    }

    async function refreshData() {
        const sort = currentSort();
        target.setAttribute('aria-busy', 'true');
        refresh && (refresh.disabled = true);
        try {
            const res = await fetch(`/ajax/refresh-products.php?sort=${encodeURIComponent(sort)}&view=${view}`, {
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('refresh err');
            target.innerHTML = await res.text();
        } catch (e) {
            console.error(e);
            target.innerHTML = isAdmin
                ? '<tr><td colspan="6">Ошибка загрузки</td></tr>'
                : '<div class="error">Ошибка загрузки</div>';
        } finally {
            target.removeAttribute('aria-busy');
            refresh && (refresh.disabled = false);
        }
    }

    select?.addEventListener('change', () => load(currentSort()));
    refresh?.addEventListener('click', refreshData);

    if (!target.innerHTML.trim()) {
        load(currentSort());
    }
});