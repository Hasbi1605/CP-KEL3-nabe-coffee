import './bootstrap';

const categoryTabs = document.querySelectorAll('[data-category-filter]');
const menuCards = document.querySelectorAll('[data-menu-card]');
const searchInput = document.querySelector('#menu-search');
const priceFilter = document.querySelector('#menu-price-filter');
const emptyState = document.querySelector('#menu-empty-state');
const detailModal = document.querySelector('#menu-detail-modal');

let activeCategory = 'Semua';

function filterMenus() {
    const searchTerm = searchInput?.value.toLowerCase().trim() ?? '';
    const maximumPrice = priceFilter?.value ?? 'all';
    let visibleMenus = 0;

    menuCards.forEach((card) => {
        const matchesCategory = activeCategory === 'Semua' || card.dataset.category === activeCategory;
        const matchesSearch = card.textContent.toLowerCase().includes(searchTerm);
        const matchesPrice = maximumPrice === 'all' || Number(card.dataset.price) <= Number(maximumPrice);
        const isVisible = matchesCategory && matchesSearch && matchesPrice;

        card.classList.toggle('hidden', !isVisible);
        visibleMenus += Number(isVisible);
    });

    emptyState?.classList.toggle('hidden', visibleMenus !== 0);
}

categoryTabs.forEach((tab) => {
    tab.addEventListener('click', () => {
        activeCategory = tab.dataset.categoryFilter;

        categoryTabs.forEach((item) => {
            const isActive = item === tab;
            item.classList.toggle('is-active', isActive);
            item.setAttribute('aria-pressed', String(isActive));
        });

        filterMenus();
    });
});

searchInput?.addEventListener('input', filterMenus);
priceFilter?.addEventListener('change', filterMenus);

document.querySelectorAll('[data-menu-detail]').forEach((button) => {
    button.addEventListener('click', () => {
        if (!(button instanceof HTMLElement) || !detailModal) return;

        document.querySelector('#menu-modal-image').src = button.dataset.image;
        document.querySelector('#menu-modal-image').alt = button.dataset.name;
        document.querySelector('#menu-modal-category').textContent = button.dataset.category;
        document.querySelector('#menu-modal-name').textContent = button.dataset.name;
        document.querySelector('#menu-modal-description').textContent = button.dataset.description;
        document.querySelector('#menu-modal-price').textContent = button.dataset.price;

        const badges = document.querySelector('#menu-modal-badges');
        badges.replaceChildren();

        if (button.dataset.bestSeller) {
            badges.insertAdjacentHTML('beforeend', '<span class="rounded bg-gold px-2.5 py-1 text-[10px] font-bold tracking-wider text-gold-ink">Terlaris</span>');
        }

        if (button.dataset.promo) {
            badges.insertAdjacentHTML('beforeend', '<span class="rounded bg-blush px-2.5 py-1 text-[10px] font-bold tracking-wider text-blush-ink">Promo</span>');
        }

        detailModal.showModal();
    });
});

document.querySelector('[data-modal-close]')?.addEventListener('click', () => detailModal?.close());

detailModal?.addEventListener('click', (event) => {
    if (event.target === detailModal) detailModal.close();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') detailModal?.close();
});
