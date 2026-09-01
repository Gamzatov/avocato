var e=localStorage.getItem(`avocatoCity`),t=null,n=`all`,r=``,i=1,a=null,o=JSON.parse(localStorage.getItem(`avocatoCart`)||`[]`),s=10,c=e=>document.getElementById(e),l=c(`cityModal`),u=c(`selectedCity`),d=c(`heroCity`),f=c(`headerPhones`),p=c(`heroPhone`),m=c(`promoPhone`),h=c(`categoryGrid`),g=c(`productsGrid`),_=c(`productPagination`),v=c(`categoryTitle`),y=c(`resultCount`),b=c(`productSearch`),x=c(`toast`),S=c(`cartDrawer`),C=c(`cartItems`),w=c(`cartEmpty`),T=c(`cartSummary`),E=c(`cartCount`),ee=c(`cartItemsCount`),te=c(`cartTotal`),ne=c(`cartCity`),D=c(`checkoutButton`),O=c(`checkoutForm`),k=c(`checkoutSuccess`),re=c(`checkoutSuccessTitle`),A=c(`checkoutCallButtons`);function j(e){x.textContent=e,x.classList.add(`show`),setTimeout(()=>x.classList.remove(`show`),2200)}function M(e){return`${Number(e||0).toLocaleString(`uk-UA`)} ₴`}function N(e){return`tel:${String(e||``).replace(/[^\d+]/g,``)}`}function P(){let e=Array.isArray(t?.city?.phones)?t.city.phones:[];return e.length?e:[t?.city?.phone].filter(Boolean)}function F(){f.replaceChildren();let e=P();if(!e.length){let e=document.createElement(`a`);e.className=`phone-link`,e.href=`#`,e.textContent=`—`,f.append(e);return}e.forEach(e=>{let t=document.createElement(`a`);t.className=`phone-link`,t.href=N(e),t.textContent=e,f.append(t)})}function I(){return n===`all`}function L(){l.setAttribute(`aria-hidden`,`false`)}function R(){l.setAttribute(`aria-hidden`,`true`)}async function z(r,a={}){let s=await fetch(`/api/menu/${r}`);if(!s.ok)throw Error(`Не вдалося завантажити меню`);t=await s.json(),e=r,localStorage.setItem(`avocatoCity`,r),u.textContent=t.city.name,d.textContent=t.city.name,ne.textContent=t.city.name;let c=P()[0];F(),[p,m].forEach(e=>e.href=N(c)),!I()&&!t.categories.some(e=>e.id===n)&&(n=t.categories[0]?.id??null),B(),i=1,H(),a.clearCartBeforeRender&&(o=[],q()),Q()}function B(){t&&(h.innerHTML=[{id:`all`,name:`Все`,icon:`🍽️`,image:null,products:W()},...t.categories].map(e=>`
    <button class="category-card ${e.id===n?`active`:``}" data-category-id="${e.id}">
      <span class="category-icon">
        ${e.image?`<img src="${e.image}" alt="">`:e.icon||`🍣`}
      </span>
      <span>
        <span class="category-name">${e.name}</span>
        <small>${e.products.length} позицій</small>
      </span>
    </button>
  `).join(``),h.querySelectorAll(`[data-category-id]`).forEach(e=>{e.addEventListener(`click`,()=>{n=e.dataset.categoryId===`all`?`all`:Number(e.dataset.categoryId),i=1,B(),H(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})}))}function V(){return I()?{name:`Все меню`,products:W()}:t?.categories.find(e=>e.id===n)}function H(){let e=V(),t=G(e?.products||[]),n=Math.max(1,Math.ceil(t.length/s));i=Math.min(i,n);let a=(i-1)*s,o=t.slice(a,a+s);if(v.textContent=e?.name||`Меню`,y.textContent=r?`${t.length} знайдено`:`${t.length} позицій`,!t.length){g.innerHTML=`
      <div class="products-empty">
        <h3>Нічого не знайшли</h3>
        <p>Спробуйте змінити запит або оберіть іншу категорію.</p>
      </div>
    `,U(t.length);return}g.innerHTML=o.map(e=>`
    <article class="product-card">
      <div class="product-photo">
        ${e.image?`<img src="${e.image}" alt="${e.name}" style="width:100%;height:100%;object-fit:cover">`:`<span style="font-size:96px">🍣</span>`}
      </div>
      <div class="product-body">
        <div class="product-top">
          <h3>${e.name}</h3>
          <div class="price">${M(e.price)}</div>
        </div>
        <p class="ingredients">${e.description||``}</p>
        <div class="product-meta">
          ${e.weight?`<span class="weight"><span aria-hidden="true">⚖</span> Вага: ${e.weight}</span>`:``}
        </div>
        <div class="product-actions">
          <button class="btn btn-green order-btn" data-product-id="${e.id}">У кошик</button>
          <a class="btn btn-outline" href="${N(P()[0])}">Зателефонувати</a>
        </div>
      </div>
    </article>
  `).join(``),g.querySelectorAll(`.order-btn`).forEach(e=>{e.addEventListener(`click`,()=>J(Number(e.dataset.productId)))}),U(t.length)}function U(e){let t=Math.ceil(e/s);if(t<=1){_.replaceChildren();return}_.innerHTML=`
    <button class="pagination-btn" type="button" data-page-action="prev" ${i===1?`disabled`:``}>Назад</button>
    <span class="pagination-status">${i} / ${t}</span>
    <button class="pagination-btn" type="button" data-page-action="next" ${i===t?`disabled`:``}>Далі</button>
  `,_.querySelectorAll(`[data-page-action]`).forEach(e=>{e.addEventListener(`click`,()=>{i+=e.dataset.pageAction===`next`?1:-1,H(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})})}function W(){return t?t.categories.flatMap(e=>e.products):[]}function G(e){let t=r.trim().toLowerCase();return t?e.filter(e=>[e.name,e.description,e.weight].some(e=>String(e||``).toLowerCase().includes(t))):e}function K(e){return W().find(t=>t.id===Number(e))}function q(){localStorage.setItem(`avocatoCart`,JSON.stringify(o))}function J(e){let t=K(e);if(!t)return;Z();let n=o.find(t=>t.productId===e);n?n.qty++:o.push({productId:e,qty:1}),q(),Q(),j(`${t.name} додано до кошика`)}function Y(e,t){let n=o.find(t=>t.productId===e);n&&(Z(),n.qty+=t,n.qty<=0&&(o=o.filter(t=>t.productId!==e)),q(),Q())}function ie(e){Z(),o=o.filter(t=>t.productId!==e),q(),Q()}function X(e=!0){O.classList.add(`hidden`),D.classList.toggle(`hidden`,!e)}function ae(){if(!o.length){j(`Додайте товари в кошик перед оформленням.`);return}O.classList.remove(`hidden`),D.classList.add(`hidden`),O.scrollIntoView({behavior:`smooth`,block:`nearest`})}function Z(){a=null,k.classList.add(`hidden`),A.replaceChildren(),o.length&&D.classList.remove(`hidden`)}function oe(e){a=e,re.textContent=`Замовлення #${e} створено`,se(),D.classList.add(`hidden`),k.classList.remove(`hidden`)}function se(){A.replaceChildren(),P().forEach(e=>{let t=document.createElement(`a`);t.className=`btn btn-green cart-checkout`,t.href=N(e),t.textContent=`Подзвонити ${e}`,t.addEventListener(`click`,ce),A.append(t)})}function ce(){o=[],q(),Z(),Q()}function Q(){if(!t){E.textContent=o.reduce((e,t)=>e+t.qty,0);return}o=o.filter(e=>K(e.productId)),q();let e=o.reduce((e,t)=>e+t.qty,0),n=o.reduce((e,t)=>e+Number(K(t.productId).price)*t.qty,0);if(E.textContent=e,ee.textContent=e,te.textContent=M(n),!o.length){C.innerHTML=``,w.classList.toggle(`show`,!a),T.classList.add(`hidden`),X();return}w.classList.remove(`show`),T.classList.remove(`hidden`),C.innerHTML=o.map(e=>{let t=K(e.productId);return`
      <div class="cart-item">
        <div class="cart-item__photo">${t.image?`<img src="${t.image}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:14px">`:`🍣`}</div>
        <div>
          <div class="cart-item__name">${t.name}</div>
          <div class="cart-item__price">${M(t.price)} / шт.</div>
          <div class="cart-item__controls">
            <button data-action="minus" data-id="${t.id}">−</button>
            <span>${e.qty}</span>
            <button data-action="plus" data-id="${t.id}">+</button>
          </div>
          <button class="cart-remove" data-action="remove" data-id="${t.id}">Видалити</button>
        </div>
        <div class="cart-item__sum">${M(Number(t.price)*e.qty)}</div>
      </div>`}).join(``),C.querySelectorAll(`[data-action]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=Number(e.dataset.id);e.dataset.action===`plus`&&Y(t,1),e.dataset.action===`minus`&&Y(t,-1),e.dataset.action===`remove`&&ie(t)})})}async function le(n){if(n.preventDefault(),!e||!t){j(`Спочатку оберіть місто.`),L();return}if(!o.length){j(`Кошик порожній.`);return}let r=new FormData(O),i=O.querySelector(`button[type="submit"]`);i.disabled=!0;try{let t=await fetch(`/api/orders`,{method:`POST`,headers:{Accept:`application/json`,"Content-Type":`application/json`},body:JSON.stringify({city_slug:e,customer:{name:r.get(`name`),phone:r.get(`phone`)},items:o.map(e=>({product_id:e.productId,qty:e.qty}))})}),n=await t.json();if(!t.ok)throw Error(n.message||`Не вдалося створити замовлення`);O.reset(),X(!1),oe(n.order.id),Q(),j(`Замовлення #${n.order.id} створено. Зателефонуйте для підтвердження.`)}catch(e){j(e.message)}finally{i.disabled=!1}}async function ue(n){let r=e,i=r&&r!==n&&o.length>0;try{await z(n,{clearCartBeforeRender:i}),r&&r!==n&&(Z(),Q()),R(),j(i?`Місто змінено. Кошик очищено.`:`Обрано місто: ${t.city.name}`)}catch(e){j(e.message)}}function de(){Q(),S.setAttribute(`aria-hidden`,`false`),document.body.classList.add(`cart-open`)}function $(){S.setAttribute(`aria-hidden`,`true`),document.body.classList.remove(`cart-open`)}document.querySelectorAll(`[data-city]`).forEach(e=>e.addEventListener(`click`,()=>ue(e.dataset.city))),c(`citySwitch`).addEventListener(`click`,L),c(`footerCityBtn`).addEventListener(`click`,L),c(`cartButton`).addEventListener(`click`,de),c(`cartClose`).addEventListener(`click`,$),c(`cartBackdrop`).addEventListener(`click`,$),c(`cartGoMenu`).addEventListener(`click`,()=>{$(),c(`menu`).scrollIntoView({behavior:`smooth`})}),D.addEventListener(`click`,ae),O.addEventListener(`submit`,le),b.addEventListener(`input`,()=>{r=b.value,i=1,H()}),document.addEventListener(`keydown`,e=>{e.key===`Escape`&&$()}),(async()=>{if(e)try{await z(e),R();return}catch{localStorage.removeItem(`avocatoCity`)}L(),Q()})();