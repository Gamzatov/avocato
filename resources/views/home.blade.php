<!DOCTYPE html>
<html lang="uk">

<head>
  @php
    $siteName = 'AvoCato Sushi';
    $siteTitle = 'AvoCato Sushi — доставка суші';
    $siteDescription = 'Свіжі роли, сети, wok, мідії та інші страви AvoCato Sushi у Переяславі та Березані.';
    $siteUrl = url('/');
    $socialImage = asset('images/logo.png');
  @endphp
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#090909" />
  <meta name="description" content="{{ $siteDescription }}" />
  <meta property="og:type" content="website" />
  <meta property="og:locale" content="uk_UA" />
  <meta property="og:site_name" content="{{ $siteName }}" />
  <meta property="og:title" content="{{ $siteTitle }}" />
  <meta property="og:description" content="{{ $siteDescription }}" />
  <meta property="og:url" content="{{ $siteUrl }}" />
  <meta property="og:image" content="{{ $socialImage }}" />
  <meta property="og:image:secure_url" content="{{ $socialImage }}" />
  <meta property="og:image:type" content="image/png" />
  <meta property="og:image:width" content="1254" />
  <meta property="og:image:height" content="1254" />
  <meta property="og:image:alt" content="{{ $siteName }}" />
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:title" content="{{ $siteTitle }}" />
  <meta name="twitter:description" content="{{ $siteDescription }}" />
  <meta name="twitter:image" content="{{ $socialImage }}" />
  <link rel="canonical" href="{{ $siteUrl }}" />
  <title>{{ $siteTitle }}</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/avocato.css', 'resources/js/avocato.js'])
</head>

<body>
  <div class="city-modal" id="cityModal" aria-hidden="false">
    <div class="city-modal__backdrop"></div>
    <div class="city-modal__panel">
      <div class="brand-mark">
       
        <div class="logo-wrapper">
        
        </div>
      </div>

      <p class="eyebrow">Оберіть ваше місто</p>
      <h1>Де будемо замовляти?</h1>
      <p class="modal-copy">Меню, телефони та акції можуть відрізнятися залежно від міста.</p>

      <div class="city-options">
        <button class="city-card" data-city="pereiaslav">
          <span class="city-card__pin">●</span>
          <span>
            <strong>Переяслав</strong>
            <small>Відкрити меню</small>
          </span>
          <span class="city-card__arrow">→</span>
        </button>

        <button class="city-card" data-city="berezan">
          <span class="city-card__pin">●</span>
          <span>
            <strong>Березань</strong>
            <small>Відкрити меню</small>
          </span>
          <span class="city-card__arrow">→</span>
        </button>
      </div>
    </div>
  </div>

  <header class="site-header">
    <div class="container header-row">
      <a class="logo" href="#top" aria-label="AvoCato Sushi">
        <!-- <span class="logo-icon">🥑</span>
        <span class="logo-text">Avo<span>Cato</span><small>SUSHI</small></span> -->
        <div class="logo-wrapper">
          
        </div>
      </a>

      <button class="city-switch" id="citySwitch" type="button">
        <span>Місто:</span>
        <strong id="selectedCity">Оберіть місто</strong>
        <span class="chev">⌄</span>
      </button>

      <div class="header-actions">
        <div class="phone-links" id="headerPhones">
          <a class="phone-link" href="#">—</a>
        </div>
        <button class="cart-button" id="cartButton" type="button" aria-label="Відкрити кошик">
          <span class="cart-button__icon">🛒</span>
          <span class="cart-button__label">Кошик</span>
          <span class="cart-count" id="cartCount">0</span>
        </button>
        <a class="btn btn-green btn-small" href="#menu">Меню</a>
      </div>
    </div>
  </header>

  <main id="top">
    <section class="hero">
      <div class="hero-image" aria-hidden="true"></div>
      <div class="hero-overlay"></div>

      <div class="container hero-content">
        <div class="hero-copy">
          <p class="eyebrow">AvoCato Sushi</p>
          <h1>Суші, які хочеться <span>замовити ще до першого шматочка.</span></h1>
          <p class="hero-text">
            Роли, сети, wok, мідії та багато іншого — готуємо свіжо, красиво й по-нашому.
          </p>

          <div class="hero-cta">
            <a class="btn btn-green" href="#menu">Дивитися меню</a>
            <a class="btn btn-outline" id="heroPhone" href="#">Зателефонувати</a>
          </div>

          <div class="hero-meta">
            <div><strong id="heroHours">9:00–21:00</strong><span>щодня</span></div>
            <div><strong id="heroCity">—</strong><span>ваше місто</span></div>
          </div>
        </div>
      </div>
    </section>

    <section class="section categories-section" id="menu">
      <div class="container">
        <div class="section-head">
          <div>
            <p class="eyebrow">Меню</p>
            <h2>Що будемо сьогодні?</h2>
          </div>
          <p class="section-note">Оберіть категорію — нижче автоматично покажуться позиції.</p>
        </div>

        <div class="category-grid" id="categoryGrid"></div>
      </div>
    </section>

    <section class="section products-section">
      <div class="container">
        <div class="products-toolbar">
          <div>
            <p class="eyebrow">Категорія</p>
            <h2 id="categoryTitle">Роли</h2>
          </div>
          <div class="products-tools">
            <label class="product-search" for="productSearch">
              <span class="product-search__icon">⌕</span>
              <input id="productSearch" type="search" placeholder="Пошук по меню" autocomplete="off">
            </label>
            <div class="result-count" id="resultCount"></div>
          </div>
        </div>

        <div class="products-grid" id="productsGrid"></div>
        <div class="product-pagination" id="productPagination"></div>
      </div>
    </section>

    <section class="promo-section">
      <div class="container promo-card">
        <div>
          <p class="eyebrow">AvoCato Sushi</p>
          <h2>Замовляй так, як зручно тобі</h2>
          <p>Обирай страви на сайті або телефонуй — ми підкажемо та приймемо замовлення.</p>
        </div>
        <div class="promo-actions">
          <a class="btn btn-green" href="#menu">Обрати страви</a>
          <a class="btn btn-outline" id="promoPhone" href="#">Подзвонити</a>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container footer-row">
      <div class="logo footer-logo">
      </div>
      <p>© 2026 AvoCato Sushi. Смачно. Свіжо. По-нашому.</p>
      <button class="footer-city" id="footerCityBtn" type="button">Змінити місто</button>
    </div>
  </footer>

  <div class="cart-drawer" id="cartDrawer" aria-hidden="true">
    <div class="cart-drawer__backdrop" id="cartBackdrop"></div>

    <aside class="cart-panel" aria-label="Кошик">
      <div class="cart-panel__head">
        <div>
          <p class="eyebrow">Ваше замовлення</p>
          <h2>Кошик</h2>
        </div>
        <button class="cart-close" id="cartClose" type="button" aria-label="Закрити кошик">×</button>
      </div>

      <div class="cart-city">
        <span>Місто</span>
        <strong id="cartCity">—</strong>
      </div>

      <div class="cart-items" id="cartItems"></div>

      <div class="cart-empty" id="cartEmpty">
        <div class="cart-empty__icon">🍣</div>
        <h3>Кошик поки порожній</h3>
        <p>Додайте роли, сети або інші страви з меню.</p>
        <button class="btn btn-green" id="cartGoMenu" type="button">Перейти до меню</button>
      </div>

      <div class="cart-summary" id="cartSummary">
        <div class="cart-summary__row">
          <span>Кількість</span>
          <strong id="cartItemsCount">0</strong>
        </div>
        <div class="cart-summary__row cart-summary__total">
          <span>Разом</span>
          <strong id="cartTotal">0 ₴</strong>
        </div>

        <button class="btn btn-green cart-checkout" id="checkoutButton" type="button">
          Оформити замовлення
        </button>

        <form class="checkout-form hidden" id="checkoutForm">
          <div class="checkout-field">
            <label for="checkoutName">Ім’я *</label>
            <input id="checkoutName" name="name" autocomplete="name" required>
          </div>

          <div class="checkout-field">
            <label for="checkoutPhone">Телефон *</label>
            <input id="checkoutPhone"
                   name="phone"
                   type="tel"
                   inputmode="tel"
                   autocomplete="tel"
                   placeholder="+38 (0__) ___-__-__"
                   maxlength="19"
                   required>
          </div>

          <button class="btn btn-green cart-checkout" type="submit">
            Створити замовлення
          </button>
        </form>

        <p class="cart-summary__note">
          Після створення замовлення зателефонуйте нам для підтвердження.
        </p>
      </div>

      <div class="checkout-success hidden" id="checkoutSuccess">
        <h3 id="checkoutSuccessTitle">Замовлення створено</h3>
        <p>Щоб ми взяли його в роботу, зателефонуйте нам і підтвердіть замовлення.</p>
        <div class="checkout-call-buttons" id="checkoutCallButtons"></div>
      </div>
    </aside>
  </div>

  <div class="toast" id="toast"></div>

</body>

</html>
