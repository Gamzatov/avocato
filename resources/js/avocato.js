let currentCitySlug = localStorage.getItem('avocatoCity');
let menuData = null;
let activeCategoryId = 'all';
let productSearchTerm = '';
let currentProductsPage = 1;
let createdOrderId = null;
let cart = JSON.parse(localStorage.getItem('avocatoCart') || '[]');
let selectedProductOptions = {};
const productsPerPage = 9;
const cityHours = {
  pereiaslav: '10:00–22:00',
  berezan: '9:00–21:00',
};
const productBadgeLabels = {
  new: 'Новинка',
  hit: 'Хіт',
  sale: 'Акція',
  out_of_stock: 'Немає в наявності',
};

const $ = id => document.getElementById(id);
const cityModal = $('cityModal');
const selectedCity = $('selectedCity');
const heroCity = $('heroCity');
const heroHours = $('heroHours');
const headerPhones = $('headerPhones');
const headerPhone = $('headerPhone');
const heroPhone = $('heroPhone');
const promoPhone = $('promoPhone');
const categoryGrid = $('categoryGrid');
const productsGrid = $('productsGrid');
const productPagination = $('productPagination');
const categoryTitle = $('categoryTitle');
const resultCount = $('resultCount');
const productSearch = $('productSearch');
const toast = $('toast');
const cartDrawer = $('cartDrawer');
const cartItems = $('cartItems');
const cartEmpty = $('cartEmpty');
const cartSummary = $('cartSummary');
const cartCount = $('cartCount');
const cartItemsCount = $('cartItemsCount');
const cartTotal = $('cartTotal');
const cartCity = $('cartCity');
const checkoutButton = $('checkoutButton');
const checkoutForm = $('checkoutForm');
const checkoutPhone = $('checkoutPhone');
const checkoutSuccess = $('checkoutSuccess');
const checkoutCallButtons = $('checkoutCallButtons');

function notify(text) {
  toast.textContent = text;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 2200);
}

function money(value) {
  return `${Number(value || 0).toLocaleString('uk-UA')} ₴`;
}

function tel(phone) {
  return `tel:${String(phone || '').replace(/[^\d+]/g, '')}`;
}

function phoneDigits(value) {
  let digits = String(value || '').replace(/\D/g, '');

  if (digits.startsWith('380')) {
    digits = digits.slice(3);
  } else if (digits.startsWith('38')) {
    digits = digits.slice(2);
  }

  if (digits.startsWith('0')) {
    digits = digits.slice(1);
  }

  return digits.slice(0, 9);
}

function formatUkrainianPhone(value) {
  const digits = phoneDigits(value);
  const operator = digits.slice(0, 2);
  const first = digits.slice(2, 5);
  const second = digits.slice(5, 7);
  const third = digits.slice(7, 9);

  let phone = '+38';

  if (operator) {
    phone += ` (0${operator}`;
  }

  if (operator.length === 2) {
    phone += ')';
  }

  if (first) {
    phone += ` ${first}`;
  }

  if (second) {
    phone += `-${second}`;
  }

  if (third) {
    phone += `-${third}`;
  }

  return phone;
}

function cityPhones() {
  const phones = Array.isArray(menuData?.city?.phones) ? menuData.city.phones : [];
  return phones.length ? phones : [menuData?.city?.phone].filter(Boolean);
}

function renderHeaderPhones() {
  if (!headerPhones) {
    const primaryPhone = cityPhones()[0];

    if (headerPhone) {
      headerPhone.textContent = primaryPhone || '—';
      headerPhone.href = tel(primaryPhone);
    }

    return;
  }

  headerPhones.replaceChildren();

  const phones = cityPhones();

  if (!phones.length) {
    const emptyPhone = document.createElement('a');
    emptyPhone.className = 'phone-link';
    emptyPhone.href = '#';
    emptyPhone.textContent = '—';
    headerPhones.append(emptyPhone);
    return;
  }

  phones.forEach(phone => {
    const link = document.createElement('a');
    link.className = 'phone-link';
    link.href = tel(phone);
    link.textContent = phone;

    headerPhones.append(link);
  });
}

function isAllCategoryActive() {
  return activeCategoryId === 'all';
}

function openCityModal() {
  cityModal.setAttribute('aria-hidden', 'false');
}

function closeCityModal() {
  cityModal.setAttribute('aria-hidden', 'true');
}

async function loadMenu(citySlug, options = {}) {
  const response = await fetch(`/api/menu/${citySlug}`);
  if (!response.ok) throw new Error('Не вдалося завантажити меню');

  menuData = await response.json();
  currentCitySlug = citySlug;
  localStorage.setItem('avocatoCity', citySlug);

  selectedCity.textContent = menuData.city.name;
  heroCity.textContent = menuData.city.name;
  if (heroHours) {
    heroHours.textContent = cityHours[citySlug] || '9:00–21:00';
  }
  cartCity.textContent = menuData.city.name;
  const primaryPhone = cityPhones()[0];
  renderHeaderPhones();
  [heroPhone, promoPhone].forEach(link => link.href = tel(primaryPhone));

  if (!isAllCategoryActive() && !menuData.categories.some(c => c.id === activeCategoryId)) {
    activeCategoryId = menuData.categories[0]?.id ?? null;
  }

  renderCategories();
  currentProductsPage = 1;
  renderProducts();

  if (options.clearCartBeforeRender) {
    cart = [];
    saveCart();
  }

  renderCart();
}

function renderCategories() {
  if (!menuData) return;

  const categories = [
    {
      id: 'all',
      name: 'Все',
      icon: '🍽️',
      image: menuData.all_category_image || '/images/all.jpg',
      products: allProducts(),
    },
    ...menuData.categories,
  ];

  categoryGrid.innerHTML = categories.map(category => `
    <button class="category-card ${category.id === activeCategoryId ? 'active' : ''}" data-category-id="${category.id}" data-category-image="${category.image || ''}">
      <span class="category-card__content">
        <span class="category-name">${category.name}</span>
        <small>${category.products.length} позицій</small>
      </span>
    </button>
  `).join('');

  categoryGrid.querySelectorAll('[data-category-id]').forEach(button => {
    if (button.dataset.categoryImage) {
      button.style.backgroundImage = `url("${button.dataset.categoryImage}")`;
    }

    button.addEventListener('click', () => {
      activeCategoryId = button.dataset.categoryId === 'all'
        ? 'all'
        : Number(button.dataset.categoryId);
      currentProductsPage = 1;
      renderCategories();
      renderProducts();
      document.querySelector('.products-section').scrollIntoView({behavior:'smooth'});
    });
  });
}

function getCategory() {
  if (isAllCategoryActive()) {
    return {
      name: 'Все меню',
      products: allProducts(),
    };
  }

  return menuData?.categories.find(c => c.id === activeCategoryId);
}

function renderProducts() {
  const category = getCategory();
  const products = filterProducts(category?.products || []);
  const totalPages = Math.max(1, Math.ceil(products.length / productsPerPage));
  currentProductsPage = Math.min(currentProductsPage, totalPages);
  const pageStart = (currentProductsPage - 1) * productsPerPage;
  const visibleProducts = products.slice(pageStart, pageStart + productsPerPage);

  categoryTitle.textContent = category?.name || 'Меню';
  resultCount.textContent = productSearchTerm
    ? `${products.length} знайдено`
    : `${products.length} позицій`;

  if (!products.length) {
    productsGrid.innerHTML = `
      <div class="products-empty">
        <h3>Нічого не знайшли</h3>
        <p>Спробуйте змінити запит або оберіть іншу категорію.</p>
      </div>
    `;
    renderProductPagination(products.length);
    return;
  }

  productsGrid.innerHTML = visibleProducts.map(product => {
    const options = productOptions(product);
    const option = selectedOption(product);
    const weight = productWeight(product, option?.id);
    const badgeLabel = product.badge_label || productBadgeLabels[product.badge] || '';
    const isAvailable = product.is_available !== false && product.badge !== 'out_of_stock';

    return `
      <article class="product-card ${isAvailable ? '' : 'is-unavailable'}">
        <div class="product-photo">
          ${product.image
            ? `<img src="${product.image}" alt="${product.name}">`
            : '<span style="font-size:96px">🍣</span>'}
          ${badgeLabel ? `<span class="product-badge product-badge--${product.badge || 'default'}">${badgeLabel}</span>` : ''}
        </div>
        <div class="product-body">
          <div class="product-top">
            <h3>${product.name}</h3>
            <div class="price">${money(productUnitPrice(product, option?.id))}</div>
          </div>
          <p class="ingredients">${product.description || ''}</p>
          ${options.length
            ? `<label class="product-option">
                <span>Варіант</span>
                <select class="product-option-select" data-product-id="${product.id}">
                  ${options.map(item => `
                    <option value="${item.id}" ${Number(item.id) === Number(option?.id) ? 'selected' : ''}>
                      ${item.name} - ${money(item.price)}
                    </option>
                  `).join('')}
                </select>
              </label>`
            : ''}
          <div class="product-card__footer">
            <div class="product-meta">
              ${weight
                ? `<span class="weight"><span aria-hidden="true">⚖</span> Вага: ${weight}</span>`
                : ''}
            </div>
            <div class="product-actions">
              <button class="btn btn-green order-btn" data-product-id="${product.id}" data-option-id="${option?.id || ''}" ${isAvailable ? '' : 'disabled'}>
                ${isAvailable ? 'У кошик' : 'Немає'}
              </button>
              <a class="btn btn-outline" href="${tel(cityPhones()[0])}">Зателефонувати</a>
            </div>
          </div>
        </div>
      </article>
    `;
  }).join('');

  productsGrid.querySelectorAll('.product-option-select').forEach(select => {
    select.addEventListener('change', () => {
      selectedProductOptions[Number(select.dataset.productId)] = Number(select.value);
      renderProducts();
    });
  });

  productsGrid.querySelectorAll('.order-btn').forEach(button => {
    button.addEventListener('click', () => addToCart(Number(button.dataset.productId), Number(button.dataset.optionId) || null));
  });

  renderProductPagination(products.length);
}

function renderProductPagination(productsCount) {
  if (!productPagination) {
    return;
  }

  const totalPages = Math.ceil(productsCount / productsPerPage);

  if (totalPages <= 1) {
    productPagination.replaceChildren();
    return;
  }

  productPagination.innerHTML = `
    <button class="pagination-btn" type="button" data-page-action="prev" ${currentProductsPage === 1 ? 'disabled' : ''}>Назад</button>
    <span class="pagination-status">${currentProductsPage} / ${totalPages}</span>
    <button class="pagination-btn" type="button" data-page-action="next" ${currentProductsPage === totalPages ? 'disabled' : ''}>Далі</button>
  `;

  productPagination.querySelectorAll('[data-page-action]').forEach(button => {
    button.addEventListener('click', () => {
      currentProductsPage += button.dataset.pageAction === 'next' ? 1 : -1;
      renderProducts();
      document.querySelector('.products-section').scrollIntoView({behavior: 'smooth'});
    });
  });
}

function allProducts() {
  return menuData ? menuData.categories.flatMap(c => c.products) : [];
}

function filterProducts(products) {
  const query = productSearchTerm.trim().toLowerCase();

  if (!query) {
    return products;
  }

  return products.filter(product => [
    product.name,
    product.description,
    product.weight,
  ].some(value => String(value || '').toLowerCase().includes(query)));
}

function getProduct(id) {
  return allProducts().find(p => p.id === Number(id));
}

function productOptions(product) {
  return Array.isArray(product.options) ? product.options : [];
}

function selectedOption(product) {
  const options = productOptions(product);
  if (!options.length) return null;

  const selectedOptionId = selectedProductOptions[product.id] || options[0].id;
  return options.find(option => Number(option.id) === Number(selectedOptionId)) || options[0];
}

function productUnitPrice(product, optionId = null) {
  const option = optionId
    ? productOptions(product).find(item => Number(item.id) === Number(optionId))
    : selectedOption(product);

  return Number(option?.price ?? product.price);
}

function productWeight(product, optionId = null) {
  const option = optionId
    ? productOptions(product).find(item => Number(item.id) === Number(optionId))
    : selectedOption(product);

  return option?.weight || product.weight;
}

function saveCart() {
  localStorage.setItem('avocatoCart', JSON.stringify(cart));
}

function addToCart(productId, optionId = null) {
  const product = getProduct(productId);
  if (!product) return;
  if (product.is_available === false || product.badge === 'out_of_stock') {
    notify('Цієї позиції зараз немає в наявності.');
    return;
  }
  hideCheckoutSuccess();
  const option = optionId
    ? productOptions(product).find(item => Number(item.id) === Number(optionId))
    : null;
  const resolvedOptionId = option?.id || null;
  const item = cart.find(i => i.productId === productId && (i.optionId || null) === resolvedOptionId);
  item ? item.qty++ : cart.push({productId, optionId: resolvedOptionId, qty:1});
  saveCart(); renderCart(); notify(`${product.name} додано до кошика`);
}

function changeQty(productId, optionId, delta) {
  const item = cart.find(i => i.productId === productId && (i.optionId || null) === (optionId || null));
  if (!item) return;
  hideCheckoutSuccess();
  item.qty += delta;
  if (item.qty <= 0) cart = cart.filter(i => !(i.productId === productId && (i.optionId || null) === (optionId || null)));
  saveCart(); renderCart();
}

function removeItem(productId, optionId) {
  hideCheckoutSuccess();
  cart = cart.filter(i => !(i.productId === productId && (i.optionId || null) === (optionId || null)));
  saveCart(); renderCart();
}

function hideCheckoutForm(showButton = true) {
  checkoutForm.classList.add('hidden');
  checkoutButton.classList.toggle('hidden', !showButton);
}

function showCheckoutForm() {
  if (!cart.length) {
    notify('Додайте товари в кошик перед оформленням.');
    return;
  }

  checkoutForm.classList.remove('hidden');
  checkoutButton.classList.add('hidden');
  checkoutForm.scrollIntoView({behavior: 'smooth', block: 'nearest'});
}

function hideCheckoutSuccess() {
  createdOrderId = null;
  checkoutSuccess.classList.add('hidden');
  checkoutCallButtons.replaceChildren();
  if (cart.length) checkoutButton.classList.remove('hidden');
}

function showCheckoutSuccess(orderId) {
  createdOrderId = orderId;
  renderCheckoutCallButtons();
  checkoutButton.classList.add('hidden');
  checkoutSuccess.classList.remove('hidden');
}

function renderCheckoutCallButtons() {
  checkoutCallButtons.replaceChildren();

  cityPhones().forEach(phone => {
    const link = document.createElement('a');
    link.className = 'btn btn-green cart-checkout';
    link.href = tel(phone);
    link.textContent = `Подзвонити ${phone}`;
    link.addEventListener('click', confirmOrderAndCall);

    checkoutCallButtons.append(link);
  });
}

function confirmOrderAndCall() {
  cart = [];
  saveCart();
  hideCheckoutSuccess();
  renderCart();
}

function renderCart() {
  if (!menuData) {
    cartCount.textContent = cart.reduce((s,i) => s + i.qty, 0);
    return;
  }

  cart = cart.filter(item => {
    const product = getProduct(item.productId);
    if (!product) return false;
    const options = productOptions(product);

    if (!item.optionId && options.length) {
      item.optionId = options[0].id;
      return true;
    }

    if (!item.optionId) return true;

    return options.some(option => Number(option.id) === Number(item.optionId));
  });
  saveCart();

  const qty = cart.reduce((s,i) => s + i.qty, 0);
  const total = cart.reduce((s,i) => s + productUnitPrice(getProduct(i.productId), i.optionId) * i.qty, 0);
  cartCount.textContent = qty;
  cartItemsCount.textContent = qty;
  cartTotal.textContent = money(total);

  if (!cart.length) {
    cartItems.innerHTML = '';
    cartEmpty.classList.toggle('show', !createdOrderId);
    cartSummary.classList.add('hidden');
    hideCheckoutForm();
    return;
  }

  cartEmpty.classList.remove('show');
  cartSummary.classList.remove('hidden');
  cartItems.innerHTML = cart.map(item => {
    const product = getProduct(item.productId);
    const option = item.optionId
      ? productOptions(product).find(productOption => Number(productOption.id) === Number(item.optionId))
      : null;
    const unitPrice = productUnitPrice(product, item.optionId);
    return `
      <div class="cart-item">
        <div class="cart-item__photo">${product.image ? `<img src="${product.image}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:14px">` : '🍣'}</div>
        <div>
          <div class="cart-item__name">${product.name}</div>
          ${option ? `<div class="cart-item__option">${option.name}</div>` : ''}
          <div class="cart-item__price">${money(unitPrice)} / шт.</div>
          <div class="cart-item__controls">
            <button data-action="minus" data-id="${product.id}" data-option-id="${item.optionId || ''}">−</button>
            <span>${item.qty}</span>
            <button data-action="plus" data-id="${product.id}" data-option-id="${item.optionId || ''}">+</button>
          </div>
          <button class="cart-remove" data-action="remove" data-id="${product.id}" data-option-id="${item.optionId || ''}">Видалити</button>
        </div>
        <div class="cart-item__sum">${money(unitPrice * item.qty)}</div>
      </div>`;
  }).join('');

  cartItems.querySelectorAll('[data-action]').forEach(button => {
    button.addEventListener('click', () => {
      const id = Number(button.dataset.id);
      const optionId = Number(button.dataset.optionId) || null;
      if (button.dataset.action === 'plus') changeQty(id, optionId, 1);
      if (button.dataset.action === 'minus') changeQty(id, optionId, -1);
      if (button.dataset.action === 'remove') removeItem(id, optionId);
    });
  });
}

async function submitOrder(event) {
  event.preventDefault();

  if (!currentCitySlug || !menuData) {
    notify('Спочатку оберіть місто.');
    openCityModal();
    return;
  }

  if (!cart.length) {
    notify('Кошик порожній.');
    return;
  }

  if (phoneDigits(checkoutPhone.value).length !== 9) {
    notify('Введіть повний український номер телефону.');
    checkoutPhone.focus();
    return;
  }

  const formData = new FormData(checkoutForm);
  const submitButton = checkoutForm.querySelector('button[type="submit"]');
  submitButton.disabled = true;

  try {
    const response = await fetch('/api/orders', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        city_slug: currentCitySlug,
        customer: {
          name: formData.get('name'),
          phone: formData.get('phone'),
        },
        items: cart.map(item => ({
          product_id: item.productId,
          product_option_id: item.optionId,
          qty: item.qty,
        })),
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Не вдалося створити замовлення');
    }

    checkoutForm.reset();
    hideCheckoutForm(false);
    showCheckoutSuccess(data.order.id);
    renderCart();
    notify(`Замовлення #${data.order.id} створено. Зателефонуйте для підтвердження.`);
  } catch (error) {
    notify(error.message);
  } finally {
    submitButton.disabled = false;
  }
}

async function chooseCity(slug) {
  const oldCity = currentCitySlug;
  const shouldClearCart = oldCity && oldCity !== slug && cart.length > 0;

  closeCityModal();

  try {
    await loadMenu(slug, {clearCartBeforeRender: shouldClearCart});
    if (oldCity && oldCity !== slug) {
      hideCheckoutSuccess();
      renderCart();
    }

    if (shouldClearCart) {
      notify('Місто змінено. Кошик очищено.');
    } else {
      notify(`Обрано місто: ${menuData.city.name}`);
    }
  } catch (e) {
    if (!menuData) {
      openCityModal();
    }

    notify(e.message);
  }
}

function openCart() { renderCart(); cartDrawer.setAttribute('aria-hidden','false'); document.body.classList.add('cart-open'); }
function closeCart() { cartDrawer.setAttribute('aria-hidden','true'); document.body.classList.remove('cart-open'); }

document.querySelectorAll('[data-city]').forEach(btn => btn.addEventListener('click', () => chooseCity(btn.dataset.city)));
$('citySwitch').addEventListener('click', openCityModal);
$('footerCityBtn').addEventListener('click', openCityModal);
$('cartButton').addEventListener('click', openCart);
$('cartClose').addEventListener('click', closeCart);
$('cartBackdrop').addEventListener('click', closeCart);
$('cartGoMenu').addEventListener('click', () => { closeCart(); $('menu').scrollIntoView({behavior:'smooth'}); });
checkoutButton.addEventListener('click', showCheckoutForm);
checkoutForm.addEventListener('submit', submitOrder);
checkoutPhone.addEventListener('input', () => {
  checkoutPhone.value = formatUkrainianPhone(checkoutPhone.value);
});
productSearch.addEventListener('input', () => {
  productSearchTerm = productSearch.value;
  currentProductsPage = 1;
  renderProducts();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCart(); });

(async () => {
  if (currentCitySlug) {
    try {
      await loadMenu(currentCitySlug);
      closeCityModal();
      return;
    } catch (_) {
      localStorage.removeItem('avocatoCity');
    }
  }
  openCityModal();
  renderCart();
})();
