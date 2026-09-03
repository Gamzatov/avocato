var e=localStorage.getItem(`avocatoCity`),t=null,n=`all`,r=``,i=1,a=null,o=JSON.parse(localStorage.getItem(`avocatoCart`)||`[]`),s={},c=9,l=e=>document.getElementById(e),u=l(`cityModal`),ee=l(`selectedCity`),te=l(`heroCity`),d=l(`headerPhones`),f=l(`headerPhone`),p=l(`heroPhone`),ne=l(`promoPhone`),m=l(`categoryGrid`),h=l(`productsGrid`),g=l(`productPagination`),re=l(`categoryTitle`),ie=l(`resultCount`),_=l(`productSearch`),v=l(`toast`),y=l(`cartDrawer`),b=l(`cartItems`),x=l(`cartEmpty`),S=l(`cartSummary`),C=l(`cartCount`),w=l(`cartItemsCount`),ae=l(`cartTotal`),oe=l(`cartCity`),T=l(`checkoutButton`),E=l(`checkoutForm`),D=l(`checkoutPhone`),O=l(`checkoutSuccess`),se=l(`checkoutSuccessTitle`),k=l(`checkoutCallButtons`);function A(e){v.textContent=e,v.classList.add(`show`),setTimeout(()=>v.classList.remove(`show`),2200)}function j(e){return`${Number(e||0).toLocaleString(`uk-UA`)} ₴`}function M(e){return`tel:${String(e||``).replace(/[^\d+]/g,``)}`}function N(e){let t=String(e||``).replace(/\D/g,``);return t.startsWith(`380`)?t=t.slice(3):t.startsWith(`38`)&&(t=t.slice(2)),t.startsWith(`0`)&&(t=t.slice(1)),t.slice(0,9)}function ce(e){let t=N(e),n=t.slice(0,2),r=t.slice(2,5),i=t.slice(5,7),a=t.slice(7,9),o=`+38`;return n&&(o+=` (0${n}`),n.length===2&&(o+=`)`),r&&(o+=` ${r}`),i&&(o+=`-${i}`),a&&(o+=`-${a}`),o}function P(){let e=Array.isArray(t?.city?.phones)?t.city.phones:[];return e.length?e:[t?.city?.phone].filter(Boolean)}function le(){if(!d){let e=P()[0];f&&(f.textContent=e||`—`,f.href=M(e));return}d.replaceChildren();let e=P();if(!e.length){let e=document.createElement(`a`);e.className=`phone-link`,e.href=`#`,e.textContent=`—`,d.append(e);return}e.forEach(e=>{let t=document.createElement(`a`);t.className=`phone-link`,t.href=M(e),t.textContent=e,d.append(t)})}function F(){return n===`all`}function I(){u.setAttribute(`aria-hidden`,`false`)}function L(){u.setAttribute(`aria-hidden`,`true`)}async function R(r,a={}){let s=await fetch(`/api/menu/${r}`);if(!s.ok)throw Error(`Не вдалося завантажити меню`);t=await s.json(),e=r,localStorage.setItem(`avocatoCity`,r),ee.textContent=t.city.name,te.textContent=t.city.name,oe.textContent=t.city.name;let c=P()[0];le(),[p,ne].forEach(e=>e.href=M(c)),!F()&&!t.categories.some(e=>e.id===n)&&(n=t.categories[0]?.id??null),z(),i=1,B(),a.clearCartBeforeRender&&(o=[],q()),Q()}function z(){t&&(m.innerHTML=[{id:`all`,name:`Все`,icon:`🍽️`,image:null,products:H()},...t.categories].map(e=>`
    <button class="category-card ${e.id===n?`active`:``}" data-category-id="${e.id}">
      <span class="category-icon">
        ${e.image?`<img src="${e.image}" alt="">`:e.icon||`🍣`}
      </span>
      <span>
        <span class="category-name">${e.name}</span>
        <small>${e.products.length} позицій</small>
      </span>
    </button>
  `).join(``),m.querySelectorAll(`[data-category-id]`).forEach(e=>{e.addEventListener(`click`,()=>{n=e.dataset.categoryId===`all`?`all`:Number(e.dataset.categoryId),i=1,z(),B(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})}))}function ue(){return F()?{name:`Все меню`,products:H()}:t?.categories.find(e=>e.id===n)}function B(){let e=ue(),t=de(e?.products||[]),n=Math.max(1,Math.ceil(t.length/c));i=Math.min(i,n);let a=(i-1)*c,o=t.slice(a,a+c);if(re.textContent=e?.name||`Меню`,ie.textContent=r?`${t.length} знайдено`:`${t.length} позицій`,!t.length){h.innerHTML=`
      <div class="products-empty">
        <h3>Нічого не знайшли</h3>
        <p>Спробуйте змінити запит або оберіть іншу категорію.</p>
      </div>
    `,V(t.length);return}h.innerHTML=o.map(e=>{let t=W(e),n=G(e),r=fe(e,n?.id);return`
      <article class="product-card">
        <div class="product-photo">
          ${e.image?`<img src="${e.image}" alt="${e.name}" style="width:100%;height:100%;object-fit:cover">`:`<span style="font-size:96px">🍣</span>`}
        </div>
        <div class="product-body">
          <div class="product-top">
            <h3>${e.name}</h3>
            <div class="price">${j(K(e,n?.id))}</div>
          </div>
          <p class="ingredients">${e.description||``}</p>
          ${t.length?`<label class="product-option">
                <span>Варіант</span>
                <select class="product-option-select" data-product-id="${e.id}">
                  ${t.map(e=>`
                    <option value="${e.id}" ${Number(e.id)===Number(n?.id)?`selected`:``}>
                      ${e.name} - ${j(e.price)}
                    </option>
                  `).join(``)}
                </select>
              </label>`:``}
          <div class="product-card__footer">
            <div class="product-meta">
              ${r?`<span class="weight"><span aria-hidden="true">⚖</span> Вага: ${r}</span>`:``}
            </div>
            <div class="product-actions">
              <button class="btn btn-green order-btn" data-product-id="${e.id}" data-option-id="${n?.id||``}">У кошик</button>
              <a class="btn btn-outline" href="${M(P()[0])}">Зателефонувати</a>
            </div>
          </div>
        </div>
      </article>
    `}).join(``),h.querySelectorAll(`.product-option-select`).forEach(e=>{e.addEventListener(`change`,()=>{s[Number(e.dataset.productId)]=Number(e.value),B()})}),h.querySelectorAll(`.order-btn`).forEach(e=>{e.addEventListener(`click`,()=>pe(Number(e.dataset.productId),Number(e.dataset.optionId)||null))}),V(t.length)}function V(e){if(!g)return;let t=Math.ceil(e/c);if(t<=1){g.replaceChildren();return}g.innerHTML=`
    <button class="pagination-btn" type="button" data-page-action="prev" ${i===1?`disabled`:``}>Назад</button>
    <span class="pagination-status">${i} / ${t}</span>
    <button class="pagination-btn" type="button" data-page-action="next" ${i===t?`disabled`:``}>Далі</button>
  `,g.querySelectorAll(`[data-page-action]`).forEach(e=>{e.addEventListener(`click`,()=>{i+=e.dataset.pageAction===`next`?1:-1,B(),document.querySelector(`.products-section`).scrollIntoView({behavior:`smooth`})})})}function H(){return t?t.categories.flatMap(e=>e.products):[]}function de(e){let t=r.trim().toLowerCase();return t?e.filter(e=>[e.name,e.description,e.weight].some(e=>String(e||``).toLowerCase().includes(t))):e}function U(e){return H().find(t=>t.id===Number(e))}function W(e){return Array.isArray(e.options)?e.options:[]}function G(e){let t=W(e);if(!t.length)return null;let n=s[e.id]||t[0].id;return t.find(e=>Number(e.id)===Number(n))||t[0]}function K(e,t=null){let n=t?W(e).find(e=>Number(e.id)===Number(t)):G(e);return Number(n?.price??e.price)}function fe(e,t=null){return(t?W(e).find(e=>Number(e.id)===Number(t)):G(e))?.weight||e.weight}function q(){localStorage.setItem(`avocatoCart`,JSON.stringify(o))}function pe(e,t=null){let n=U(e);if(!n)return;Z();let r=(t?W(n).find(e=>Number(e.id)===Number(t)):null)?.id||null,i=o.find(t=>t.productId===e&&(t.optionId||null)===r);i?i.qty++:o.push({productId:e,optionId:r,qty:1}),q(),Q(),A(`${n.name} додано до кошика`)}function J(e,t,n){let r=o.find(n=>n.productId===e&&(n.optionId||null)===(t||null));r&&(Z(),r.qty+=n,r.qty<=0&&(o=o.filter(n=>n.productId!==e||(n.optionId||null)!==(t||null))),q(),Q())}function Y(e,t){Z(),o=o.filter(n=>n.productId!==e||(n.optionId||null)!==(t||null)),q(),Q()}function X(e=!0){E.classList.add(`hidden`),T.classList.toggle(`hidden`,!e)}function me(){if(!o.length){A(`Додайте товари в кошик перед оформленням.`);return}E.classList.remove(`hidden`),T.classList.add(`hidden`),E.scrollIntoView({behavior:`smooth`,block:`nearest`})}function Z(){a=null,O.classList.add(`hidden`),k.replaceChildren(),o.length&&T.classList.remove(`hidden`)}function he(e){a=e,se.textContent=`Замовлення #${e} створено`,ge(),T.classList.add(`hidden`),O.classList.remove(`hidden`)}function ge(){k.replaceChildren(),P().forEach(e=>{let t=document.createElement(`a`);t.className=`btn btn-green cart-checkout`,t.href=M(e),t.textContent=`Подзвонити ${e}`,t.addEventListener(`click`,_e),k.append(t)})}function _e(){o=[],q(),Z(),Q()}function Q(){if(!t){C.textContent=o.reduce((e,t)=>e+t.qty,0);return}o=o.filter(e=>{let t=U(e.productId);if(!t)return!1;let n=W(t);return!e.optionId&&n.length?(e.optionId=n[0].id,!0):!e.optionId||n.some(t=>Number(t.id)===Number(e.optionId))}),q();let e=o.reduce((e,t)=>e+t.qty,0),n=o.reduce((e,t)=>e+K(U(t.productId),t.optionId)*t.qty,0);if(C.textContent=e,w.textContent=e,ae.textContent=j(n),!o.length){b.innerHTML=``,x.classList.toggle(`show`,!a),S.classList.add(`hidden`),X();return}x.classList.remove(`show`),S.classList.remove(`hidden`),b.innerHTML=o.map(e=>{let t=U(e.productId),n=e.optionId?W(t).find(t=>Number(t.id)===Number(e.optionId)):null,r=K(t,e.optionId);return`
      <div class="cart-item">
        <div class="cart-item__photo">${t.image?`<img src="${t.image}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:14px">`:`🍣`}</div>
        <div>
          <div class="cart-item__name">${t.name}</div>
          ${n?`<div class="cart-item__option">${n.name}</div>`:``}
          <div class="cart-item__price">${j(r)} / шт.</div>
          <div class="cart-item__controls">
            <button data-action="minus" data-id="${t.id}" data-option-id="${e.optionId||``}">−</button>
            <span>${e.qty}</span>
            <button data-action="plus" data-id="${t.id}" data-option-id="${e.optionId||``}">+</button>
          </div>
          <button class="cart-remove" data-action="remove" data-id="${t.id}" data-option-id="${e.optionId||``}">Видалити</button>
        </div>
        <div class="cart-item__sum">${j(r*e.qty)}</div>
      </div>`}).join(``),b.querySelectorAll(`[data-action]`).forEach(e=>{e.addEventListener(`click`,()=>{let t=Number(e.dataset.id),n=Number(e.dataset.optionId)||null;e.dataset.action===`plus`&&J(t,n,1),e.dataset.action===`minus`&&J(t,n,-1),e.dataset.action===`remove`&&Y(t,n)})})}async function ve(n){if(n.preventDefault(),!e||!t){A(`Спочатку оберіть місто.`),I();return}if(!o.length){A(`Кошик порожній.`);return}if(N(D.value).length!==9){A(`Введіть повний український номер телефону.`),D.focus();return}let r=new FormData(E),i=E.querySelector(`button[type="submit"]`);i.disabled=!0;try{let t=await fetch(`/api/orders`,{method:`POST`,headers:{Accept:`application/json`,"Content-Type":`application/json`},body:JSON.stringify({city_slug:e,customer:{name:r.get(`name`),phone:r.get(`phone`)},items:o.map(e=>({product_id:e.productId,product_option_id:e.optionId,qty:e.qty}))})}),n=await t.json();if(!t.ok)throw Error(n.message||`Не вдалося створити замовлення`);E.reset(),X(!1),he(n.order.id),Q(),A(`Замовлення #${n.order.id} створено. Зателефонуйте для підтвердження.`)}catch(e){A(e.message)}finally{i.disabled=!1}}async function ye(n){let r=e,i=r&&r!==n&&o.length>0;try{await R(n,{clearCartBeforeRender:i}),r&&r!==n&&(Z(),Q()),L(),A(i?`Місто змінено. Кошик очищено.`:`Обрано місто: ${t.city.name}`)}catch(e){A(e.message)}}function be(){Q(),y.setAttribute(`aria-hidden`,`false`),document.body.classList.add(`cart-open`)}function $(){y.setAttribute(`aria-hidden`,`true`),document.body.classList.remove(`cart-open`)}document.querySelectorAll(`[data-city]`).forEach(e=>e.addEventListener(`click`,()=>ye(e.dataset.city))),l(`citySwitch`).addEventListener(`click`,I),l(`footerCityBtn`).addEventListener(`click`,I),l(`cartButton`).addEventListener(`click`,be),l(`cartClose`).addEventListener(`click`,$),l(`cartBackdrop`).addEventListener(`click`,$),l(`cartGoMenu`).addEventListener(`click`,()=>{$(),l(`menu`).scrollIntoView({behavior:`smooth`})}),T.addEventListener(`click`,me),E.addEventListener(`submit`,ve),D.addEventListener(`input`,()=>{D.value=ce(D.value)}),_.addEventListener(`input`,()=>{r=_.value,i=1,B()}),document.addEventListener(`keydown`,e=>{e.key===`Escape`&&$()}),(async()=>{if(e)try{await R(e),L();return}catch{localStorage.removeItem(`avocatoCity`)}I(),Q()})();