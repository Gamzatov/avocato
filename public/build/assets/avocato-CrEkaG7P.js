var e=localStorage.getItem(`avocatoCity`),t=null,n=`all`,r=``,i=1,a=null,o=JSON.parse(localStorage.getItem(`avocatoCart`)||`[]`),s=10,c=e=>document.getElementById(e),l=c(`cityModal`),ee=c(`selectedCity`),u=c(`heroCity`),d=c(`headerPhones`),f=c(`headerPhone`),te=c(`heroPhone`),p=c(`promoPhone`),m=c(`categoryGrid`),h=c(`productsGrid`),g=c(`productPagination`),ne=c(`categoryTitle`),_=c(`resultCount`),v=c(`productSearch`),y=c(`toast`),b=c(`cartDrawer`),x=c(`cartItems`),S=c(`cartEmpty`),C=c(`cartSummary`),w=c(`cartCount`),re=c(`cartItemsCount`),T=c(`cartTotal`),E=c(`cartCity`),D=c(`checkoutButton`),O=c(`checkoutForm`),k=c(`checkoutSuccess`),ie=c(`checkoutSuccessTitle`),A=c(`checkoutCallButtons`);function j(e){y.textContent=e,y.classList.add(`show`),setTimeout(()=>y.classList.remove(`show`),2200)}function M(e){return`${Number(e||0).toLocaleString(`uk-UA`)} ₴`}function N(e){return`tel:${String(e||``).replace(/[^\d+]/g,``)}`}function P(){let e=Array.isArray(t?.city?.phones)?t.city.phones:[];return e.length?e:[t?.city?.phone].filter(Boolean)}function F(){if(!d){let e=P()[0];f&&(f.textContent=e||`—`,f.href=N(e));return}d.replaceChildren();let e=P();if(!e.length){let e=document.createElement(`a`);e.className=`phone-link`,e.href=`#`,e.textContent=`—`,d.append(e);return}e.forEach(e=>{let t=document.createElement(`a`);t.className=`phone-link`,t.href=N(e),t.textContent=e,d.append(t)})}function I(){return n===`all`}function L(){l.setAttribute(`aria-hidden`,`false`)}function R(){l.setAttribute(`aria-hidden`,`true`)}async function z(r,a={}){let s=await fetch(`/api/menu/${r}`);if(!s.ok)throw Error(`Не вдалося завантажити меню`);t=await s.json(),e=r,localStorage.setItem(`avocatoCity`,r),ee.textContent=t.city.name,u.textContent=t.city.name,E.textContent=t.city.name;let c=P()[0];F(),[te,p].forEach(e=>e.href=N(c)),!I()&&!t.categories.some(e=>e.id===n)&&(n=t.categories[0]?.id??null),B(),i=1,V(),a.clearCartBeforeRender&&(o=[],K()),Q()}function B(){t&&(m.innerHTML=[{id:`all`,name:`Все`,icon:`🍽️`,image:null,products:U()},...t.categories].map(e=>`
    <button class="category-card ${e.id===n?`active`:``}" data-category-id="${e.id}">
      <span class="category-icon">
        ${e.image?`<img src="${e.image}" alt="">`:e.icon||`🍣`}
      </span>
      <span>
        <span class="category-name">${e.name}</span>
        <small>${e.products.length} позицій</small>
      </span>
    </button>
  `).join(``),m.querySelectorAll(`[data-category-id]`).forEach(e=>{e.addEventListener(`click`,()=>{n=e.dataset.categoryId===`all`?`all`:Number(e.dataset.categoryId),i=1,B(),V(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})}))}function ae(){return I()?{name:`Все меню`,products:U()}:t?.categories.find(e=>e.id===n)}function V(){let e=ae(),t=W(e?.products||[]),n=Math.max(1,Math.ceil(t.length/s));i=Math.min(i,n);let a=(i-1)*s,o=t.slice(a,a+s);if(ne.textContent=e?.name||`Меню`,_.textContent=r?`${t.length} знайдено`:`${t.length} позицій`,!t.length){h.innerHTML=`
      <div class="products-empty">
        <h3>Нічого не знайшли</h3>
        <p>Спробуйте змінити запит або оберіть іншу категорію.</p>
      </div>
    `,H(t.length);return}h.innerHTML=o.map(e=>`
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
  `).join(``),h.querySelectorAll(`.order-btn`).forEach(e=>{e.addEventListener(`click`,()=>q(Number(e.dataset.productId)))}),H(t.length)}function H(e){if(!g)return;let t=Math.ceil(e/s);if(t<=1){g.replaceChildren();return}g.innerHTML=`
    <button class="pagination-btn" type="button" data-page-action="prev" ${i===1?`disabled`:``}>Назад</button>
    <span class="pagination-status">${i} / ${t}</span>
    <button class="pagination-btn" type="button" data-page-action="next" ${i===t?`disabled`:``}>Далі</button>
  `,g.querySelectorAll(`[data-page-action]`).forEach(e=>{e.addEventListener(`click`,()=>{i+=e.dataset.pageAction===`next`?1:-1,V(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})})}function U(){return t?t.categories.flatMap(e=>e.products):[]}function W(e){let t=r.trim().toLowerCase();return t?e.filter(e=>[e.name,e.description,e.weight].some(e=>String(e||``).toLowerCase().includes(t))):e}function G(e){return U().find(t=>t.id===Number(e))}function K(){localStorage.setItem(`avocatoCart`,JSON.stringify(o))}function q(e){let t=G(e);if(!t)return;X();let n=o.find(t=>t.productId===e);n?n.qty++:o.push({productId:e,qty:1}),K(),Q(),j(`${t.name} додано до кошика`)}function J(e,t){let n=o.find(t=>t.productId===e);n&&(X(),n.qty+=t,n.qty<=0&&(o=o.filter(t=>t.productId!==e)),K(),Q())}function oe(e){X(),o=o.filter(t=>t.productId!==e),K(),Q()}function Y(e=!0){O.classList.add(`hidden`),D.classList.toggle(`hidden`,!e)}function se(){if(!o.length){j(`Додайте товари в кошик перед оформленням.`);return}O.classList.remove(`hidden`),D.classList.add(`hidden`),O.scrollIntoView({behavior:`smooth`,block:`nearest`})}function X(){a=null,k.classList.add(`hidden`),A.replaceChildren(),o.length&&D.classList.remove(`hidden`)}function Z(e){a=e,ie.textContent=`Замовлення #${e} створено`,ce(),D.classList.add(`hidden`),k.classList.remove(`hidden`)}function ce(){A.replaceChildren(),P().forEach(e=>{let t=document.createElement(`a`);t.className=`btn btn-green cart-checkout`,t.href=N(e),t.textContent=`Подзвонити ${e}`,t.addEventListener(`click`,le),A.append(t)})}function le(){o=[],K(),X(),Q()}function Q(){if(!t){w.textContent=o.reduce((e,t)=>e+t.qty,0);return}o=o.filter(e=>G(e.productId)),K();let e=o.reduce((e,t)=>e+t.qty,0),n=o.reduce((e,t)=>e+Number(G(t.productId).price)*t.qty,0);if(w.textContent=e,re.textContent=e,T.textContent=M(n),!o.length){x.innerHTML=``,S.classList.toggle(`show`,!a),C.classList.add(`hidden`),Y();return}S.classList.remove(`show`),C.classList.remove(`hidden`),x.innerHTML=o.map(e=>{let t=G(e.productId);return`
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
      </div>`}).join(``),x.querySelectorAll(`[data-action]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=Number(e.dataset.id);e.dataset.action===`plus`&&J(t,1),e.dataset.action===`minus`&&J(t,-1),e.dataset.action===`remove`&&oe(t)})})}async function ue(n){if(n.preventDefault(),!e||!t){j(`Спочатку оберіть місто.`),L();return}if(!o.length){j(`Кошик порожній.`);return}let r=new FormData(O),i=O.querySelector(`button[type="submit"]`);i.disabled=!0;try{let t=await fetch(`/api/orders`,{method:`POST`,headers:{Accept:`application/json`,"Content-Type":`application/json`},body:JSON.stringify({city_slug:e,customer:{name:r.get(`name`),phone:r.get(`phone`)},items:o.map(e=>({product_id:e.productId,qty:e.qty}))})}),n=await t.json();if(!t.ok)throw Error(n.message||`Не вдалося створити замовлення`);O.reset(),Y(!1),Z(n.order.id),Q(),j(`Замовлення #${n.order.id} створено. Зателефонуйте для підтвердження.`)}catch(e){j(e.message)}finally{i.disabled=!1}}async function de(n){let r=e,i=r&&r!==n&&o.length>0;try{await z(n,{clearCartBeforeRender:i}),r&&r!==n&&(X(),Q()),R(),j(i?`Місто змінено. Кошик очищено.`:`Обрано місто: ${t.city.name}`)}catch(e){j(e.message)}}function fe(){Q(),b.setAttribute(`aria-hidden`,`false`),document.body.classList.add(`cart-open`)}function $(){b.setAttribute(`aria-hidden`,`true`),document.body.classList.remove(`cart-open`)}document.querySelectorAll(`[data-city]`).forEach(e=>e.addEventListener(`click`,()=>de(e.dataset.city))),c(`citySwitch`).addEventListener(`click`,L),c(`footerCityBtn`).addEventListener(`click`,L),c(`cartButton`).addEventListener(`click`,fe),c(`cartClose`).addEventListener(`click`,$),c(`cartBackdrop`).addEventListener(`click`,$),c(`cartGoMenu`).addEventListener(`click`,()=>{$(),c(`menu`).scrollIntoView({behavior:`smooth`})}),D.addEventListener(`click`,se),O.addEventListener(`submit`,ue),v.addEventListener(`input`,()=>{r=v.value,i=1,V()}),document.addEventListener(`keydown`,e=>{e.key===`Escape`&&$()}),(async()=>{if(e)try{await z(e),R();return}catch{localStorage.removeItem(`avocatoCity`)}L(),Q()})();