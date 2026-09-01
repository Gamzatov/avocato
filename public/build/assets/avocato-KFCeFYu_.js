var e=localStorage.getItem(`avocatoCity`),t=null,n=`all`,r=``,i=null,a=JSON.parse(localStorage.getItem(`avocatoCart`)||`[]`),o=e=>document.getElementById(e),s=o(`cityModal`),c=o(`selectedCity`),ee=o(`heroCity`),l=o(`headerPhones`),te=o(`heroPhone`),u=o(`promoPhone`),d=o(`categoryGrid`),f=o(`productsGrid`),p=o(`categoryTitle`),m=o(`resultCount`),h=o(`productSearch`),g=o(`toast`),_=o(`cartDrawer`),v=o(`cartItems`),y=o(`cartEmpty`),b=o(`cartSummary`),x=o(`cartCount`),S=o(`cartItemsCount`),C=o(`cartTotal`),ne=o(`cartCity`),w=o(`checkoutButton`),T=o(`checkoutForm`),E=o(`checkoutSuccess`),D=o(`checkoutSuccessTitle`),O=o(`checkoutCallButtons`);function k(e){g.textContent=e,g.classList.add(`show`),setTimeout(()=>g.classList.remove(`show`),2200)}function A(e){return`${Number(e||0).toLocaleString(`uk-UA`)} ₴`}function j(e){return`tel:${String(e||``).replace(/[^\d+]/g,``)}`}function M(){let e=Array.isArray(t?.city?.phones)?t.city.phones:[];return e.length?e:[t?.city?.phone].filter(Boolean)}function N(){l.replaceChildren();let e=M();if(!e.length){let e=document.createElement(`a`);e.className=`phone-link`,e.href=`#`,e.textContent=`—`,l.append(e);return}e.forEach(e=>{let t=document.createElement(`a`);t.className=`phone-link`,t.href=j(e),t.textContent=e,l.append(t)})}function P(){return n===`all`}function F(){s.setAttribute(`aria-hidden`,`false`)}function I(){s.setAttribute(`aria-hidden`,`true`)}async function L(r,i={}){let o=await fetch(`/api/menu/${r}`);if(!o.ok)throw Error(`Не вдалося завантажити меню`);t=await o.json(),e=r,localStorage.setItem(`avocatoCity`,r),c.textContent=t.city.name,ee.textContent=t.city.name,ne.textContent=t.city.name;let s=M()[0];N(),[te,u].forEach(e=>e.href=j(s)),!P()&&!t.categories.some(e=>e.id===n)&&(n=t.categories[0]?.id??null),R(),B(),i.clearCartBeforeRender&&(a=[],W()),Q()}function R(){t&&(d.innerHTML=[{id:`all`,name:`Все`,icon:`🍽️`,products:V()},...t.categories].map(e=>`
    <button class="category-card ${e.id===n?`active`:``}" data-category-id="${e.id}">
      <span class="category-icon">${e.icon||`🍣`}</span>
      <span>
        <span class="category-name">${e.name}</span>
        <small>${e.products.length} позицій</small>
      </span>
    </button>
  `).join(``),d.querySelectorAll(`[data-category-id]`).forEach(e=>{e.addEventListener(`click`,()=>{n=e.dataset.categoryId===`all`?`all`:Number(e.dataset.categoryId),R(),B(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})}))}function z(){return P()?{name:`Все меню`,products:V()}:t?.categories.find(e=>e.id===n)}function B(){let e=z(),t=H(e?.products||[]);if(p.textContent=e?.name||`Меню`,m.textContent=r?`${t.length} знайдено`:`${t.length} позицій`,!t.length){f.innerHTML=`
      <div class="products-empty">
        <h3>Нічого не знайшли</h3>
        <p>Спробуйте змінити запит або оберіть іншу категорію.</p>
      </div>
    `;return}f.innerHTML=t.map(e=>`
    <article class="product-card">
      <div class="product-photo">
        ${e.image?`<img src="${e.image}" alt="${e.name}" style="width:100%;height:100%;object-fit:cover">`:`<span style="font-size:96px">🍣</span>`}
      </div>
      <div class="product-body">
        <div class="product-top">
          <h3>${e.name}</h3>
          <div class="price">${A(e.price)}</div>
        </div>
        <p class="ingredients">${e.description||``}</p>
        <div class="product-meta">
          ${e.weight?`<span class="weight"><span aria-hidden="true">⚖</span> Вага: ${e.weight}</span>`:``}
        </div>
        <div class="product-actions">
          <button class="btn btn-green order-btn" data-product-id="${e.id}">У кошик</button>
          <a class="btn btn-outline" href="${j(M()[0])}">Зателефонувати</a>
        </div>
      </div>
    </article>
  `).join(``),f.querySelectorAll(`.order-btn`).forEach(e=>{e.addEventListener(`click`,()=>G(Number(e.dataset.productId)))})}function V(){return t?t.categories.flatMap(e=>e.products):[]}function H(e){let t=r.trim().toLowerCase();return t?e.filter(e=>[e.name,e.description,e.weight].some(e=>String(e||``).toLowerCase().includes(t))):e}function U(e){return V().find(t=>t.id===Number(e))}function W(){localStorage.setItem(`avocatoCart`,JSON.stringify(a))}function G(e){let t=U(e);if(!t)return;X();let n=a.find(t=>t.productId===e);n?n.qty++:a.push({productId:e,qty:1}),W(),Q(),k(`${t.name} додано до кошика`)}function K(e,t){let n=a.find(t=>t.productId===e);n&&(X(),n.qty+=t,n.qty<=0&&(a=a.filter(t=>t.productId!==e)),W(),Q())}function q(e){X(),a=a.filter(t=>t.productId!==e),W(),Q()}function J(e=!0){T.classList.add(`hidden`),w.classList.toggle(`hidden`,!e)}function Y(){if(!a.length){k(`Додайте товари в кошик перед оформленням.`);return}T.classList.remove(`hidden`),w.classList.add(`hidden`),T.scrollIntoView({behavior:`smooth`,block:`nearest`})}function X(){i=null,E.classList.add(`hidden`),O.replaceChildren(),a.length&&w.classList.remove(`hidden`)}function re(e){i=e,D.textContent=`Замовлення #${e} створено`,Z(),w.classList.add(`hidden`),E.classList.remove(`hidden`)}function Z(){O.replaceChildren(),M().forEach(e=>{let t=document.createElement(`a`);t.className=`btn btn-green cart-checkout`,t.href=j(e),t.textContent=`Подзвонити ${e}`,t.addEventListener(`click`,ie),O.append(t)})}function ie(){a=[],W(),X(),Q()}function Q(){if(!t){x.textContent=a.reduce((e,t)=>e+t.qty,0);return}a=a.filter(e=>U(e.productId)),W();let e=a.reduce((e,t)=>e+t.qty,0),n=a.reduce((e,t)=>e+Number(U(t.productId).price)*t.qty,0);if(x.textContent=e,S.textContent=e,C.textContent=A(n),!a.length){v.innerHTML=``,y.classList.toggle(`show`,!i),b.classList.add(`hidden`),J();return}y.classList.remove(`show`),b.classList.remove(`hidden`),v.innerHTML=a.map(e=>{let t=U(e.productId);return`
      <div class="cart-item">
        <div class="cart-item__photo">${t.image?`<img src="${t.image}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:14px">`:`🍣`}</div>
        <div>
          <div class="cart-item__name">${t.name}</div>
          <div class="cart-item__price">${A(t.price)} / шт.</div>
          <div class="cart-item__controls">
            <button data-action="minus" data-id="${t.id}">−</button>
            <span>${e.qty}</span>
            <button data-action="plus" data-id="${t.id}">+</button>
          </div>
          <button class="cart-remove" data-action="remove" data-id="${t.id}">Видалити</button>
        </div>
        <div class="cart-item__sum">${A(Number(t.price)*e.qty)}</div>
      </div>`}).join(``),v.querySelectorAll(`[data-action]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=Number(e.dataset.id);e.dataset.action===`plus`&&K(t,1),e.dataset.action===`minus`&&K(t,-1),e.dataset.action===`remove`&&q(t)})})}async function ae(n){if(n.preventDefault(),!e||!t){k(`Спочатку оберіть місто.`),F();return}if(!a.length){k(`Кошик порожній.`);return}let r=new FormData(T),i=T.querySelector(`button[type="submit"]`);i.disabled=!0;try{let t=await fetch(`/api/orders`,{method:`POST`,headers:{Accept:`application/json`,"Content-Type":`application/json`},body:JSON.stringify({city_slug:e,customer:{name:r.get(`name`),phone:r.get(`phone`),address:r.get(`address`),comment:r.get(`comment`)},items:a.map(e=>({product_id:e.productId,qty:e.qty}))})}),n=await t.json();if(!t.ok)throw Error(n.message||`Не вдалося створити замовлення`);T.reset(),J(!1),re(n.order.id),Q(),k(`Замовлення #${n.order.id} створено. Зателефонуйте для підтвердження.`)}catch(e){k(e.message)}finally{i.disabled=!1}}async function oe(n){let r=e,i=r&&r!==n&&a.length>0;try{await L(n,{clearCartBeforeRender:i}),r&&r!==n&&(X(),Q()),I(),k(i?`Місто змінено. Кошик очищено.`:`Обрано місто: ${t.city.name}`)}catch(e){k(e.message)}}function se(){Q(),_.setAttribute(`aria-hidden`,`false`),document.body.classList.add(`cart-open`)}function $(){_.setAttribute(`aria-hidden`,`true`),document.body.classList.remove(`cart-open`)}document.querySelectorAll(`[data-city]`).forEach(e=>e.addEventListener(`click`,()=>oe(e.dataset.city))),o(`citySwitch`).addEventListener(`click`,F),o(`footerCityBtn`).addEventListener(`click`,F),o(`cartButton`).addEventListener(`click`,se),o(`cartClose`).addEventListener(`click`,$),o(`cartBackdrop`).addEventListener(`click`,$),o(`cartGoMenu`).addEventListener(`click`,()=>{$(),o(`menu`).scrollIntoView({behavior:`smooth`})}),w.addEventListener(`click`,Y),T.addEventListener(`submit`,ae),h.addEventListener(`input`,()=>{r=h.value,B()}),document.addEventListener(`keydown`,e=>{e.key===`Escape`&&$()}),(async()=>{if(e)try{await L(e),I();return}catch{localStorage.removeItem(`avocatoCity`)}F(),Q()})();