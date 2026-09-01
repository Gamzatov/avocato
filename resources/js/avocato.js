let currentCitySlug = localStorage.getItem('avocatoCity');
let menuData = null;
let activeCategoryId = 'all';
let productSearchTerm = '';
let createdOrderId = null;
let cart = JSON.parse(localStorage.getItem('avocatoCart') || '[]');

const $ = id => document.getElementById(id);
const cityModal = $('cityModal');
const selectedCity = $('selectedCity');
const heroCity = $('heroCity');
const headerPhones = $('headerPhones');
const heroPhone = $('heroPhone');
const promoPhone = $('promoPhone');
const categoryGrid = $('categoryGrid');
const productsGrid = $('productsGrid');
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
const checkoutSuccess = $('checkoutSuccess');
const checkoutSuccessTitle = $('checkoutSuccessTitle');
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

function cityPhones() {
  const phones = Array.isArray(menuData?.city?.phones) ? menuData.city.phones : [];
  return phones.length ? phones : [menuData?.city?.phone].filter(Boolean);
}

function renderHeaderPhones() {
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
  cartCity.textContent = menuData.city.name;
  const primaryPhone = cityPhones()[0];
  renderHeaderPhones();
  [heroPhone, promoPhone].forEach(link => link.href = tel(primaryPhone));

  if (!isAllCategoryActive() && !menuData.categories.some(c => c.id === activeCategoryId)) {
    activeCategoryId = menuData.categories[0]?.id ?? null;
  }

  renderCategories();
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
      products: allProducts(),
    },
    ...menuData.categories,
  ];

  categoryGrid.innerHTML = categories.map(category => `
    <button class="category-card ${category.id === activeCategoryId ? 'active' : ''}" data-category-id="${category.id}">
      <span class="category-icon">${category.icon || '🍣'}</span>
      <span>
        <span class="category-name">${category.name}</span>
        <small>${category.products.length} позицій</small>
      </span>
    </button>
  `).join('');

  categoryGrid.querySelectorAll('[data-category-id]').forEach(button => {
    button.addEventListener('click', () => {
      activeCategoryId = button.dataset.categoryId === 'all'
        ? 'all'
        : Number(button.dataset.categoryId);
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
    return;
  }

  productsGrid.innerHTML = products.map(product => `
    <article class="product-card">
      <div class="product-photo">
        ${product.image
          ? `<img src="${product.image}" alt="${product.name}" style="width:100%;height:100%;object-fit:cover">`
          : '<span style="font-size:96px">🍣</span>'}
      </div>
      <div class="product-body">
        <div class="product-top">
          <h3>${product.name}</h3>
          <div class="price">${money(product.price)}</div>
        </div>
        <p class="ingredients">${product.description || ''}</p>
        <div class="product-meta"><span class="weight">${product.weight || ''}</span></div>
        <div class="product-actions">
          <button class="btn btn-green order-btn" data-product-id="${product.id}">У кошик</button>
          <a class="btn btn-outline" href="${tel(cityPhones()[0])}">Зателефонувати</a>
        </div>
      </div>
    </article>
  `).join('');

  productsGrid.querySelectorAll('.order-btn').forEach(button => {
    button.addEventListener('click', () => addToCart(Number(button.dataset.productId)));
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

function saveCart() {
  localStorage.setItem('avocatoCart', JSON.stringify(cart));
}

function addToCart(productId) {
  const product = getProduct(productId);
  if (!product) return;
  hideCheckoutSuccess();
  const item = cart.find(i => i.productId === productId);
  item ? item.qty++ : cart.push({productId, qty:1});
  saveCart(); renderCart(); notify(`${product.name} додано до кошика`);
}

function changeQty(productId, delta) {
  const item = cart.find(i => i.productId === productId);
  if (!item) return;
  hideCheckoutSuccess();
  item.qty += delta;
  if (item.qty <= 0) cart = cart.filter(i => i.productId !== productId);
  saveCart(); renderCart();
}

function removeItem(productId) {
  hideCheckoutSuccess();
  cart = cart.filter(i => i.productId !== productId);
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
  checkoutSuccessTitle.textContent = `Замовлення #${orderId} створено`;
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

  cart = cart.filter(item => getProduct(item.productId));
  saveCart();

  const qty = cart.reduce((s,i) => s + i.qty, 0);
  const total = cart.reduce((s,i) => s + Number(getProduct(i.productId).price) * i.qty, 0);
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
    return `
      <div class="cart-item">
        <div class="cart-item__photo">${product.image ? `<img src="${product.image}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:14px">` : '🍣'}</div>
        <div>
          <div class="cart-item__name">${product.name}</div>
          <div class="cart-item__price">${money(product.price)} / шт.</div>
          <div class="cart-item__controls">
            <button data-action="minus" data-id="${product.id}">−</button>
            <span>${item.qty}</span>
            <button data-action="plus" data-id="${product.id}">+</button>
          </div>
          <button class="cart-remove" data-action="remove" data-id="${product.id}">Видалити</button>
        </div>
        <div class="cart-item__sum">${money(Number(product.price) * item.qty)}</div>
      </div>`;
  }).join('');

  cartItems.querySelectorAll('[data-action]').forEach(button => {
    button.addEventListener('click', () => {
      const id = Number(button.dataset.id);
      if (button.dataset.action === 'plus') changeQty(id, 1);
      if (button.dataset.action === 'minus') changeQty(id, -1);
      if (button.dataset.action === 'remove') removeItem(id);
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
          address: formData.get('address'),
          comment: formData.get('comment'),
        },
        items: cart.map(item => ({
          product_id: item.productId,
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

  try {
    await loadMenu(slug, {clearCartBeforeRender: shouldClearCart});
    if (oldCity && oldCity !== slug) {
      hideCheckoutSuccess();
      renderCart();
    }
    closeCityModal();

    if (shouldClearCart) {
      notify('Місто змінено. Кошик очищено.');
    } else {
      notify(`Обрано місто: ${menuData.city.name}`);
    }
  } catch (e) { notify(e.message); }
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
productSearch.addEventListener('input', () => {
  productSearchTerm = productSearch.value;
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
