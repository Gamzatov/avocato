var e=localStorage.getItem(`avocatoCity`),t=null,n=`all`,r=``,i=1,a=null,o=JSON.parse(localStorage.getItem(`avocatoCart`)||`[]`),s=10,c=e=>document.getElementById(e),l=c(`cityModal`),ee=c(`selectedCity`),u=c(`heroCity`),d=c(`headerPhones`),f=c(`headerPhone`),te=c(`heroPhone`),p=c(`promoPhone`),m=c(`categoryGrid`),h=c(`productsGrid`),g=c(`productPagination`),_=c(`categoryTitle`),ne=c(`resultCount`),v=c(`productSearch`),y=c(`toast`),b=c(`cartDrawer`),x=c(`cartItems`),S=c(`cartEmpty`),C=c(`cartSummary`),w=c(`cartCount`),re=c(`cartItemsCount`),ie=c(`cartTotal`),ae=c(`cartCity`),T=c(`checkoutButton`),E=c(`checkoutForm`),D=c(`checkoutPhone`),O=c(`checkoutSuccess`),oe=c(`checkoutSuccessTitle`),k=c(`checkoutCallButtons`);function A(e){y.textContent=e,y.classList.add(`show`),setTimeout(()=>y.classList.remove(`show`),2200)}function j(e){return`${Number(e||0).toLocaleString(`uk-UA`)} ₴`}function M(e){return`tel:${String(e||``).replace(/[^\d+]/g,``)}`}function N(e){let t=String(e||``).replace(/\D/g,``);return t.startsWith(`380`)?t=t.slice(3):t.startsWith(`38`)&&(t=t.slice(2)),t.startsWith(`0`)&&(t=t.slice(1)),t.slice(0,9)}function P(e){let t=N(e),n=t.slice(0,2),r=t.slice(2,5),i=t.slice(5,7),a=t.slice(7,9),o=`+38`;return n&&(o+=` (0${n}`),n.length===2&&(o+=`)`),r&&(o+=` ${r}`),i&&(o+=`-${i}`),a&&(o+=`-${a}`),o}function F(){let e=Array.isArray(t?.city?.phones)?t.city.phones:[];return e.length?e:[t?.city?.phone].filter(Boolean)}function se(){if(!d){let e=F()[0];f&&(f.textContent=e||`—`,f.href=M(e));return}d.replaceChildren();let e=F();if(!e.length){let e=document.createElement(`a`);e.className=`phone-link`,e.href=`#`,e.textContent=`—`,d.append(e);return}e.forEach(e=>{let t=document.createElement(`a`);t.className=`phone-link`,t.href=M(e),t.textContent=e,d.append(t)})}function I(){return n===`all`}function L(){l.setAttribute(`aria-hidden`,`false`)}function R(){l.setAttribute(`aria-hidden`,`true`)}async function z(r,a={}){let s=await fetch(`/api/menu/${r}`);if(!s.ok)throw Error(`Не вдалося завантажити меню`);t=await s.json(),e=r,localStorage.setItem(`avocatoCity`,r),ee.textContent=t.city.name,u.textContent=t.city.name,ae.textContent=t.city.name;let c=F()[0];se(),[te,p].forEach(e=>e.href=M(c)),!I()&&!t.categories.some(e=>e.id===n)&&(n=t.categories[0]?.id??null),B(),i=1,H(),a.clearCartBeforeRender&&(o=[],q()),Q()}function B(){t&&(m.innerHTML=[{id:`all`,name:`Все`,icon:`🍽️`,image:null,products:W()},...t.categories].map(e=>`
    <button class="category-card ${e.id===n?`active`:``}" data-category-id="${e.id}">
      <span class="category-icon">
        ${e.image?`<img src="${e.image}" alt="">`:e.icon||`🍣`}
      </span>
      <span>
        <span class="category-name">${e.name}</span>
        <small>${e.products.length} позицій</small>
      </span>
    </button>
  `).join(``),m.querySelectorAll(`[data-category-id]`).forEach(e=>{e.addEventListener(`click`,()=>{n=e.dataset.categoryId===`all`?`all`:Number(e.dataset.categoryId),i=1,B(),H(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})}))}function V(){return I()?{name:`Все меню`,products:W()}:t?.categories.find(e=>e.id===n)}function H(){let e=V(),t=G(e?.products||[]),n=Math.max(1,Math.ceil(t.length/s));i=Math.min(i,n);let a=(i-1)*s,o=t.slice(a,a+s);if(_.textContent=e?.name||`Меню`,ne.textContent=r?`${t.length} знайдено`:`${t.length} позицій`,!t.length){h.innerHTML=`
      <div class="products-empty">
        <h3>Нічого не знайшли</h3>
        <p>Спробуйте змінити запит або оберіть іншу категорію.</p>
      </div>
    `,U(t.length);return}h.innerHTML=o.map(e=>`
    <article class="product-card">
      <div class="product-photo">
        ${e.image?`<img src="${e.image}" alt="${e.name}" style="width:100%;height:100%;object-fit:cover">`:`<span style="font-size:96px">🍣</span>`}
      </div>
      <div class="product-body">
        <div class="product-top">
          <h3>${e.name}</h3>
          <div class="price">${j(e.price)}</div>
        </div>
        <p class="ingredients">${e.description||``}</p>
        <div class="product-meta">
          ${e.weight?`<span class="weight"><span aria-hidden="true">⚖</span> Вага: ${e.weight}</span>`:``}
        </div>
        <div class="product-actions">
          <button class="btn btn-green order-btn" data-product-id="${e.id}">У кошик</button>
          <a class="btn btn-outline" href="${M(F()[0])}">Зателефонувати</a>
        </div>
      </div>
    </article>
  `).join(``),h.querySelectorAll(`.order-btn`).forEach(e=>{e.addEventListener(`click`,()=>ce(Number(e.dataset.productId)))}),U(t.length)}function U(e){if(!g)return;let t=Math.ceil(e/s);if(t<=1){g.replaceChildren();return}g.innerHTML=`
    <button class="pagination-btn" type="button" data-page-action="prev" ${i===1?`disabled`:``}>Назад</button>
    <span class="pagination-status">${i} / ${t}</span>
    <button class="pagination-btn" type="button" data-page-action="next" ${i===t?`disabled`:``}>Далі</button>
  `,g.querySelectorAll(`[data-page-action]`).forEach(e=>{e.addEventListener(`click`,()=>{i+=e.dataset.pageAction===`next`?1:-1,H(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})})}function W(){return t?t.categories.flatMap(e=>e.products):[]}function G(e){let t=r.trim().toLowerCase();return t?e.filter(e=>[e.name,e.description,e.weight].some(e=>String(e||``).toLowerCase().includes(t))):e}function K(e){return W().find(t=>t.id===Number(e))}function q(){localStorage.setItem(`avocatoCart`,JSON.stringify(o))}function ce(e){let t=K(e);if(!t)return;X();let n=o.find(t=>t.productId===e);n?n.qty++:o.push({productId:e,qty:1}),q(),Q(),A(`${t.name} додано до кошика`)}function J(e,t){let n=o.find(t=>t.productId===e);n&&(X(),n.qty+=t,n.qty<=0&&(o=o.filter(t=>t.productId!==e)),q(),Q())}function le(e){X(),o=o.filter(t=>t.productId!==e),q(),Q()}function Y(e=!0){E.classList.add(`hidden`),T.classList.toggle(`hidden`,!e)}function ue(){if(!o.length){A(`Додайте товари в кошик перед оформленням.`);return}E.classList.remove(`hidden`),T.classList.add(`hidden`),E.scrollIntoView({behavior:`smooth`,block:`nearest`})}function X(){a=null,O.classList.add(`hidden`),k.replaceChildren(),o.length&&T.classList.remove(`hidden`)}function de(e){a=e,oe.textContent=`Замовлення #${e} створено`,Z(),T.classList.add(`hidden`),O.classList.remove(`hidden`)}function Z(){k.replaceChildren(),F().forEach(e=>{let t=document.createElement(`a`);t.className=`btn btn-green cart-checkout`,t.href=M(e),t.textContent=`Подзвонити ${e}`,t.addEventListener(`click`,fe),k.append(t)})}function fe(){o=[],q(),X(),Q()}function Q(){if(!t){w.textContent=o.reduce((e,t)=>e+t.qty,0);return}o=o.filter(e=>K(e.productId)),q();let e=o.reduce((e,t)=>e+t.qty,0),n=o.reduce((e,t)=>e+Number(K(t.productId).price)*t.qty,0);if(w.textContent=e,re.textContent=e,ie.textContent=j(n),!o.length){x.innerHTML=``,S.classList.toggle(`show`,!a),C.classList.add(`hidden`),Y();return}S.classList.remove(`show`),C.classList.remove(`hidden`),x.innerHTML=o.map(e=>{let t=K(e.productId);return`
      <div class="cart-item">
        <div class="cart-item__photo">${t.image?`<img src="${t.image}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:14px">`:`🍣`}</div>
        <div>
          <div class="cart-item__name">${t.name}</div>
          <div class="cart-item__price">${j(t.price)} / шт.</div>
          <div class="cart-item__controls">
            <button data-action="minus" data-id="${t.id}">−</button>
            <span>${e.qty}</span>
            <button data-action="plus" data-id="${t.id}">+</button>
          </div>
          <button class="cart-remove" data-action="remove" data-id="${t.id}">Видалити</button>
        </div>
        <div class="cart-item__sum">${j(Number(t.price)*e.qty)}</div>
      </div>`}).join(``),x.querySelectorAll(`[data-action]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=Number(e.dataset.id);e.dataset.action===`plus`&&J(t,1),e.dataset.action===`minus`&&J(t,-1),e.dataset.action===`remove`&&le(t)})})}async function pe(n){if(n.preventDefault(),!e||!t){A(`Спочатку оберіть місто.`),L();return}if(!o.length){A(`Кошик порожній.`);return}if(N(D.value).length!==9){A(`Введіть повний український номер телефону.`),D.focus();return}let r=new FormData(E),i=E.querySelector(`button[type="submit"]`);i.disabled=!0;try{let t=await fetch(`/api/orders`,{method:`POST`,headers:{Accept:`application/json`,"Content-Type":`application/json`},body:JSON.stringify({city_slug:e,customer:{name:r.get(`name`),phone:r.get(`phone`)},items:o.map(e=>({product_id:e.productId,qty:e.qty}))})}),n=await t.json();if(!t.ok)throw Error(n.message||`Не вдалося створити замовлення`);E.reset(),Y(!1),de(n.order.id),Q(),A(`Замовлення #${n.order.id} створено. Зателефонуйте для підтвердження.`)}catch(e){A(e.message)}finally{i.disabled=!1}}async function me(n){let r=e,i=r&&r!==n&&o.length>0;try{await z(n,{clearCartBeforeRender:i}),r&&r!==n&&(X(),Q()),R(),A(i?`Місто змінено. Кошик очищено.`:`Обрано місто: ${t.city.name}`)}catch(e){A(e.message)}}function he(){Q(),b.setAttribute(`aria-hidden`,`false`),document.body.classList.add(`cart-open`)}function $(){b.setAttribute(`aria-hidden`,`true`),document.body.classList.remove(`cart-open`)}document.querySelectorAll(`[data-city]`).forEach(e=>e.addEventListener(`click`,()=>me(e.dataset.city))),c(`citySwitch`).addEventListener(`click`,L),c(`footerCityBtn`).addEventListener(`click`,L),c(`cartButton`).addEventListener(`click`,he),c(`cartClose`).addEventListener(`click`,$),c(`cartBackdrop`).addEventListener(`click`,$),c(`cartGoMenu`).addEventListener(`click`,()=>{$(),c(`menu`).scrollIntoView({behavior:`smooth`})}),T.addEventListener(`click`,ue),E.addEventListener(`submit`,pe),D.addEventListener(`input`,()=>{D.value=P(D.value)}),v.addEventListener(`input`,()=>{r=v.value,i=1,H()}),document.addEventListener(`keydown`,e=>{e.key===`Escape`&&$()}),(async()=>{if(e)try{await z(e),R();return}catch{localStorage.removeItem(`avocatoCity`)}L(),Q()})();