// ------- Utilidades -------
const EUR = new Intl.NumberFormat("es-ES", { style: "currency", currency: "EUR" });
const USD = new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" });
const fmt = (n, currency="USD") => ({EUR, USD}[currency] || USD).format(n);
const el = (sel) => document.querySelector(sel);
const cls = (...xs) => xs.filter(Boolean).join(" ");

// ------- Datos en memoria (fallback si el backend no responde) -------
const FALLBACK = {
  categories: [
    { id: "all", label: "Todas", icon: "📦" },
    { id: "clasicos", label: "Clásicos", icon: "🍦" },
    { id: "premium", label: "Premium", icon: "✨" },
    { id: "paletas", label: "Paletas", icon: "🍭" },
    { id: "toppings", label: "Toppings", icon: "🍫" },
    { id: "bebidas", label: "Bebidas", icon: "🥤" },
  ],
  products: [
    { id: "cono-vainilla", name: "Cono Vainilla", price: 2.5, category: "clasicos", image: "./images/helado-fresa.jpg" },
    { id: "cono-chocolate", name: "Cono Chocolate", price: 2.7, category: "clasicos", image: "./images/helado-coco.jpg" },
    { id: "cono-fresa", name: "Cono Fresa", price: 2.6, category: "clasicos", image: "./images/Helado_fresa-leche.png" },
    { id: "sundae-oreo", name: "Sundae Oreo", price: 3.9, category: "premium", image: "./images/grapes-strawberries-pineapple-kiwi-apricot-banana-whole-pineapple.jpg" },
    { id: "banana-split", name: "Banana Split", price: 4.9, category: "premium", image: "./images/close-up-cocoa-powder-with-truffles.jpg" },
    { id: "paleta-mango", name: "Paleta Mango", price: 1.8, category: "paletas", image: "./images/paleta-mango.jpg" },
    { id: "paleta-coco", name: "Paleta Coco", price: 1.9, category: "paletas", image: "./images/paleta-coco.jpg" },
    { id: "topping-chispas", name: "Topping Chispas", price: 0.6, category: "toppings", image: "./images/topping-chispas.jpg" },
    { id: "topping-caramelo", name: "Sirope Caramelo", price: 0.7, category: "toppings", image: "./images/topping-caramelo.jpg" },
    { id: "refresco", name: "Refresco Lata", price: 1.5, category: "bebidas", image: "./images/refresco.jpg" },
  ]
};

// ------- Estado -------
const state = {
  currency: "USD",
  taxRate: 0.13,
  query: "",
  category: "all",
  cart: {}, // { [productId]: { qty, unitPrice } }
  products: [],
  categories: [],
  paymentMethod: "card", // card | cash | paypal
  cardType: "debit",
  cashAmount: "",
  invoice: null
};

// ------- Notificaciones -------
function notify(message, type="success") {
  const root = el("#notification-root");
  const box = document.createElement("div");
  box.className = cls(
    "fixed top-4 left-1/2 -translate-x-1/2 z-50 p-4 rounded-xl shadow-lg text-white font-medium transition-all animate-slide-in",
    type === "success" ? "bg-green-500" : "bg-rose-500"
  );
  box.innerHTML = `
    <div class="flex items-center gap-2">
      <span>${type === "success" ? "✅" : "✖️"}</span>
      <span>${message}</span>
      <button class="ml-4 opacity-80 hover:opacity-100">✖️</button>
    </div>`;
  const remove = () => root.removeChild(box);
  box.querySelector("button").onclick = remove;
  root.appendChild(box);
  setTimeout(remove, 3000);
}

// ------- Render categorías -------
function renderCategories() {
  const bar = el("#categoriesBar");
  bar.innerHTML = "";
  state.categories.forEach(c => {
    const btn = document.createElement("button");
    const active = state.category === c.id;
    btn.className = cls(
      "px-4 py-2 rounded-xl border border-sky-200 transition-all flex items-center gap-2",
      active ? "bg-sky-100 border-sky-400 text-sky-700 font-medium" : "bg-white hover:bg-sky-50"
    );
    btn.innerHTML = `<span>${c.icon || ""}</span><span>${c.label}</span>`;
    btn.onclick = () => { state.category = c.id; renderCategories(); renderProducts(); };
    bar.appendChild(btn);
  });
}

// ------- Filtro productos -------
function getFilteredProducts() {
  const q = state.query.toLowerCase();
  return state.products.filter(p =>
    (state.category === "all" || p.category === state.category) &&
    p.name.toLowerCase().includes(q)
  );
}

// ------- Render productos (evita doble agregado) -------
function renderProducts() {
  const grid = el("#productsGrid");
  grid.innerHTML = "";

  getFilteredProducts().forEach(p => {
    const art = document.createElement("article");
    art.className =
      "rounded-2xl border border-sky-100 bg-white shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden cursor-pointer";
    art.innerHTML = `
      <div class="aspect-square overflow-hidden bg-gradient-to-br from-sky-100 to-cyan-100 flex items-center justify-center">
        <img src="${p.image}" alt="${p.name}" class="w-full h-full object-cover"/>
      </div>
      <div class="p-4">
        <h3 class="font-semibold text-sky-800 truncate">${p.name}</h3>
        <div class="flex justify-between items-center mt-2">
          <span class="text-sm font-medium text-sky-600">${fmt(p.price, state.currency)}</span>
          <button class="js-add rounded-xl bg-sky-700 px-3 py-1.5 text-white text-sm font-medium hover:bg-sky-800 active:scale-95 transition-all">
            Agregar
          </button>
        </div>
      </div>
    `;

    // 👉 clic en toda la tarjeta
    art.addEventListener("click", () => addToCart(p));

    // 👉 clic en el botón "Agregar"
    const addBtn = art.querySelector(".js-add");
    addBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      addToCart(p);
    });

    grid.appendChild(art);
  });
}


// ------- Carrito -------
function addToCart(product) {
  const key = String(product.id); // 🔑 aseguramos que siempre sea string
  const curr = state.cart[key] || { qty: 0, unitPrice: product.price };
  state.cart[key] = { qty: curr.qty + 1, unitPrice: product.price };
  notify(`${product.name} agregado al carrito`);
  renderCart();
}

function removeFromCart(product) {
  const key = String(product.id);
  const curr = state.cart[key];
  if (!curr) return;
  if (curr.qty <= 1) delete state.cart[key];
  else state.cart[key] = { ...curr, qty: curr.qty - 1 };
  notify(`${product.name} eliminado del carrito`, "error");
  renderCart();
}

function clearCart() {
  state.cart = {};
  notify("Carrito Limpio", "error");
  renderCart();
}

function cartEntries() {
  return Object.entries(state.cart).map(([id, line]) => {
    const product = state.products.find(p => String(p.id) === id);
    return { product, ...line };
  }).filter(e => e.product); // seguridad extra
}

function totals() {
  const subtotal = cartEntries().reduce((s, l) => s + l.unitPrice * l.qty, 0);
  const tax = subtotal //* state.taxRate;
  const total = subtotal + tax;
  return { subtotal, tax, total };
}

function renderCart() {
  const container = el("#cartLines");
  const btnClear = el("#clearCartBtn");
  const totalsBox = el("#cartTotals");
  container.innerHTML = "";

  const entries = cartEntries();
  if (entries.length === 0) {
    btnClear.classList.add("hidden");
    totalsBox.classList.add("hidden");
    container.innerHTML = `
      <div class="text-center py-8 text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p>Carrito vacío</p>
        <p class="text-sm mt-1">Agrega productos para comenzar</p>
      </div>`;
    return;
  }

  btnClear.classList.remove("hidden");
  totalsBox.classList.remove("hidden");

  entries.forEach(l => {
    const row = document.createElement("div");
    row.className = "flex justify-between items-center p-3 rounded-xl bg-sky-50 border border-sky-100";
    row.innerHTML = `
      <div>
        <span class="font-medium text-sky-800">${l.product.name}</span>
        <div class="text-sm text-sky-600">${fmt(l.unitPrice, state.currency)} c/u</div>
      </div>
      <div class="flex gap-2 items-center">
        <button class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 hover:bg-rose-200">−</button>
        <span class="font-bold min-w-[20px] text-center">${l.qty}</span>
        <button class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 hover:bg-sky-200">+</button>
        <span class="font-medium text-sky-700 min-w-[70px] text-right">${fmt(l.unitPrice * l.qty, state.currency)}</span>
      </div>
    `;

    // ✅ Selección correcta de botones
    const buttons = row.querySelectorAll("button");
    const btnMinus = buttons[0];
    const btnPlus = buttons[1];

    btnMinus.onclick = () => removeFromCart(l.product);
    btnPlus.onclick = () => addToCart(l.product);

    container.appendChild(row);
  });

  const { subtotal, tax, total } = totals();
  el("#subtotalText").textContent = fmt(subtotal, state.currency);
  el("#taxText").textContent = fmt(tax, state.currency);
  el("#totalText").textContent = fmt(total, state.currency);
}


// ------- Modal genérico (header + body scroll + footer fijo) -------
function openModal({ titleHTML = "", bodyHTML = "", footerHTML = "", size = "lg", onClose = () => {} }) {
  const root = el("#modalRoot");
  root.innerHTML = `
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" role="dialog" aria-modal="true">
      <div class="${cls(
        "bg-white rounded-2xl w-full relative shadow-2xl border border-sky-100 animate-pop-in flex flex-col max-h-[95vh]",
        size === "sm" && "max-w-sm",
        size === "md" && "max-w-md",
        size === "lg" && "max-w-lg",
        size === "xl" && "max-w-xl"
      )}">
        <button id="modalCloseBtn"
          class="absolute top-4 right-4 z-10 text-gray-400 hover:text-gray-600 transition-colors bg-white rounded-full p-1 shadow-sm">✖️</button>

        ${titleHTML ? `<div class="flex-shrink-0 p-6 border-b border-sky-100">${titleHTML}</div>` : ""}

        <div class="flex-grow overflow-y-auto">${bodyHTML}</div>

        ${footerHTML ? `<div class="flex-shrink-0 p-6 border-t border-sky-100">${footerHTML}</div>` : ""}
      </div>
    </div>
  `;
  el("#modalCloseBtn").onclick = () => { root.innerHTML = ""; onClose(); };
  return () => { root.innerHTML = ""; onClose(); };
}

// ------- Checkout (usa header/footer del modal) -------
function updateConfirmButton(total) {
  const btn = el("#confirmPayBtn");
  if (!btn) return;
  const needsCash = state.paymentMethod === "cash";
  const cash = parseFloat(state.cashAmount || "0");
  const disabled = needsCash && (isNaN(cash) || cash < total);
  btn.disabled = disabled;
  btn.className = disabled
    ? "w-full rounded-xl py-3 px-4 font-semibold bg-gray-200 text-gray-400 cursor-not-allowed"
    : "w-full rounded-xl py-3 px-4 font-semibold bg-gradient-to-r from-sky-500 to-indigo-500 text-white shadow-lg hover:shadow-xl";
}

function openCheckout() {
  const { subtotal, tax, total } = totals();

  const titleHTML = `
    <h2 class="text-2xl font-bold text-sky-700 flex items-center gap-2">
      <span class="text-xl">💳</span> Finalizar Compra
    </h2>
    <div class="bg-sky-50 rounded-xl p-4 mt-4">
      <div class="text-center font-bold text-lg text-sky-700 mb-2">
        Total a pagar: ${fmt(total, state.currency)}
      </div>
      <div class="flex justify-between text-sm">
        <span>Subtotal: ${fmt(subtotal, state.currency)}</span>
        <span>IVA: ${fmt(tax, state.currency)}</span>
      </div>
    </div>
  `;

  const bodyHTML = `
    <div class="p-6 space-y-6">
      <div class="space-y-4">
        <h3 class="font-medium text-gray-700">Método de pago</h3>
        <div class="grid grid-cols-3 gap-3">
        ${["card","cash","paypal"].map(m=>`
          <label class="payment-btn flex flex-col items-center p-4 border-2 ${state.paymentMethod===m?"border-sky-500 bg-sky-50":"border-sky-100"} rounded-xl cursor-pointer hover:border-sky-300">
            <input type="radio" name="payment" value="${m}" ${state.paymentMethod===m?"checked":""} class="sr-only" />
            <div class="h-8 w-8 text-sky-600 mb-2">${m==="card"?"💳":m==="cash"?"💶":"💠"}</div>
            <span class="font-medium">${m==="card"?"Tarjeta":m==="cash"?"Efectivo":"PayPal"}</span>
          </label>
        `).join("")}
        </div>
        <div id="paymentOptions"></div>
      </div>

      <div class="space-y-3">
        <div>
          <input id="customerName" type="text" placeholder="Nombre del cliente"
            class="w-full border border-sky-200 rounded-xl p-3 focus:ring-2 focus:ring-sky-300 focus:border-sky-300" required />
          <small id="errCustomer" class="text-red-500 text-sm hidden">El nombre del cliente es obligatorio</small>
        </div>
        <textarea id="orderNotes" placeholder="Notas del pedido (opcional)"
          class="w-full border border-sky-200 rounded-xl p-3 focus:ring-2 focus:ring-sky-300 focus:border-sky-300 h-20"></textarea>
      </div>
    </div>
  `;

  const footerHTML = `
    <div id="formError" class="text-center text-red-500 text-sm mb-2 hidden">
      Complete los campos obligatorios para continuar.
    </div>
    <button id="confirmPayBtn" class="w-full rounded-xl py-3 px-4 font-semibold">
      Confirmar pago
    </button>
  `;

  const close = openModal({ titleHTML, bodyHTML, footerHTML, size: "lg" });

  document.getElementsByName("payment").forEach(r => {
    r.onchange = (e) => {
      state.paymentMethod = e.target.value;

      // Quitar clases activas de todos
      document.querySelectorAll(".payment-btn").forEach(btn => {
        btn.classList.remove("border-sky-500", "bg-sky-50");
        btn.classList.add("border-sky-100");
      });

      // Agregar clase activa al botón seleccionado
      const selected = e.target.closest("label");
      if (selected) {
        selected.classList.remove("border-sky-100");
        selected.classList.add("border-sky-500", "bg-sky-50");
      }

      renderPaymentOptions(total);
      updateConfirmButton(total);
    };
  });

  renderPaymentOptions(total);
  updateConfirmButton(total);

  el("#confirmPayBtn").onclick = async () => {
    let hasError = false;

    // Validar campos según el método de pago
    if (state.paymentMethod === "card") {
      const cardNumber = document.querySelector("input[placeholder='Número de tarjeta']");
      const cvv = document.querySelector("input[placeholder='CVV']");
      const exp = document.querySelector("input[placeholder='MM/AA']");
      const holder = document.querySelector("input[placeholder='Titular']");

      [cardNumber, cvv, exp, holder].forEach(input => {
        if (!input.value.trim()) {
          input.classList.add("border-red-500");
          hasError = true;
        } else {
          input.classList.remove("border-red-500");
        }
      });
    }

    if (state.paymentMethod === "cash") {
      const cash = parseFloat(state.cashAmount || "0");
      if (isNaN(cash) || cash < total) {
        notify("El monto en efectivo es insuficiente", "error");
        hasError = true;
      }
    }

    // Validar nombre del cliente
    const customerName = el("#customerName");
    const errCustomer = el("#errCustomer");
    if (!customerName.value.trim()) {
      customerName.classList.add("border-red-500");
      errCustomer.classList.remove("hidden");
      hasError = true;
    } else {
      customerName.classList.remove("border-red-500");
      errCustomer.classList.add("hidden");
    }

    // Mostrar mensaje general si hay errores
    const formError = el("#formError");
      if (hasError) {
        formError.classList.remove("hidden");

        // Ocultar automáticamente a los 3 segundos
        setTimeout(() => {
          formError.classList.add("hidden");
        }, 3000);

        return;
      } else {
        formError.classList.add("hidden");
      }

    // Si todo está correcto → procesar la orden
    const invoice = buildInvoice();
    const saved = await saveOrder(invoice);
    if (!saved.ok) {
      notify(`No se guardó en BD: ${saved.error || "Error"}`, "error");
      return;
    }

    notify("Venta registrada correctamente");
    state.invoice = invoice;
    state.cart = {};
    state.cashAmount = "";
    renderCart();
    close();
    openInvoice(invoice);
  };
}



function renderPaymentOptions(total) {
  const host = el("#paymentOptions");
  if (state.paymentMethod === "card") {
    host.innerHTML = `
      <div class="mt-4 space-y-3">
        <h4 class="font-medium text-gray-700">Tipo de tarjeta</h4>
        <div class="flex gap-4">
          <label class="flex items-center gap-2">
            <input type="radio" name="cardType" value="debit" ${state.cardType==="debit"?"checked":""}/> Débito
          </label>
          <label class="flex items-center gap-2">
            <input type="radio" name="cardType" value="credit" ${state.cardType==="credit"?"checked":""}/> Crédito
          </label>
        </div>
        <div class="grid grid-cols-2 gap-3 mt-3">
          <input type="number" placeholder="Número de tarjeta" class="w-full border border-sky-200 rounded-xl p-3 focus:ring-2 focus:ring-sky-300 focus:border-sky-300" />
          <input type="number" placeholder="CVV" class="w-full border border-sky-200 rounded-xl p-3 focus:ring-2 focus:ring-sky-300 focus:border-sky-300" />
          <input type="text" placeholder="MM/AA" class="w-full border border-sky-200 rounded-xl p-3 focus:ring-2 focus:ring-sky-300 focus:border-sky-300" />
          <input type="text" placeholder="Titular" class="w-full border border-sky-200 rounded-xl p-3 focus:ring-2 focus:ring-sky-300 focus:border-sky-300" />
        </div>
      </div>
    `;
    document.getElementsByName("cardType").forEach(r => r.onchange = (e)=> state.cardType = e.target.value);
  } else if (state.paymentMethod === "cash") {
    host.innerHTML = `
      <div class="mt-4 space-y-3">
        <div class="flex items-center gap-3">
          <label class="font-medium text-gray-700">Paga con:</label>
          <input id="cashInput" type="number" value="${state.cashAmount}" placeholder="0.00" step="0.01" min="${total}"
            class="border border-sky-200 rounded-xl p-2 focus:ring-2 focus:ring-sky-300 focus:border-sky-300" />
        </div>
        <div id="cashInfo"></div>
      </div>
    `;
    const input = el("#cashInput");
    const info = el("#cashInfo");
    const renderCashInfo = () => {
      const val = parseFloat(input.value);
      state.cashAmount = input.value;
      if (!isNaN(val) && val >= total) {
        info.innerHTML = `
          <div class="bg-green-50 p-3 rounded-xl border border-green-200">
            <div class="font-medium text-green-800">Cambio: ${fmt(val - total, state.currency)}</div>
          </div>`;
      } else if (!isNaN(val)) {
        info.innerHTML = `
          <div class="bg-rose-50 p-3 rounded-xl border border-rose-200">
            <div class="font-medium text-rose-800">Faltan: ${fmt(total - val, state.currency)}</div>
          </div>`;
      } else {
        info.innerHTML = "";
      }
      updateConfirmButton(total);
    };
    input.oninput = renderCashInfo;
    renderCashInfo();
  } else {
    host.innerHTML = `
      <div class="mt-4 bg-blue-50 p-4 rounded-xl border border-blue-200">
        <div class="text-blue-800 font-medium">Será redirigido a PayPal para completar el pago</div>
      </div>`;
  }
}

// ------- Factura -------
function buildInvoice() {
  const { subtotal, tax, total } = totals();
  const entries = cartEntries();
  return {
    cartEntries: entries.map(l => ({
      id: Number(l.product.id), // aseguramos que se mande numérico al backend
      name: l.product.name,
      unitPrice: l.unitPrice,
      qty: l.qty,
      lineTotal: l.unitPrice * l.qty
    })),
    subtotal,
    tax,
    total,
    currency: state.currency,
    date: new Date().toLocaleString(),
    paymentMethod: state.paymentMethod,
    cardType: state.cardType,
    cashAmount: state.paymentMethod==="cash" ? state.cashAmount : null,
    change: state.paymentMethod==="cash" ? (parseFloat(state.cashAmount||"0") - total) : null,
    customerName: el("#customerName")?.value || "",
    notes: el("#orderNotes")?.value || ""
  };
}

// Usa el mismo patrón de modal (header/footer fijos)
function openInvoice(inv) {
  const titleHTML = `
    <div class="text-center mb-4">
      <h2 class="text-2xl font-bold text-sky-700">¡Gracias por su compra!</h2>
      <p class="text-gray-600">Transacción completada exitosamente</p>
      <p class="text-sm text-gray-500 mt-1">${inv.date}</p>
    </div>
    <div class="bg-gradient-to-r from-sky-500 to-indigo-500 text-white rounded-2xl p-5">
      <div class="text-center text-2xl font-bold">${fmt(inv.total, inv.currency)}</div>
      <div class="text-center text-sm opacity-90">Total pagado</div>
    </div>
  `;

  const bodyHTML = `
    <div class="print-section p-6">
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-sky-50 p-3 rounded-xl">
          <div class="text-xs text-gray-500">Método de pago</div>
          <div class="font-medium">
            ${inv.paymentMethod === "card" ? "Tarjeta" : inv.paymentMethod === "cash" ? "Efectivo" : "PayPal"}
            ${inv.paymentMethod === "card" ? ` (${inv.cardType==="credit"?"Crédito":"Débito"})` : ""}
          </div>
        </div>
        ${inv.paymentMethod==="cash" ? `
          <div class="bg-sky-50 p-3 rounded-xl">
            <div class="text-xs text-gray-500">Pagado con</div>
            <div class="font-medium">${fmt(parseFloat(inv.cashAmount||"0"), inv.currency)}</div>
          </div>
          <div class="bg-sky-50 p-3 rounded-xl">
            <div class="text-xs text-gray-500">Cambio</div>
            <div class="font-medium">${fmt(inv.change||0, inv.currency)}</div>
          </div>
        ` : ""}
      </div>

      <h3 class="font-medium text-gray-700 mb-3">Detalles del pedido</h3>
      <div class="space-y-3 mb-6">
        ${inv.cartEntries.map(l=>`
          <div class="flex justify-between items-center p-3 rounded-xl bg-sky-50">
            <div>
              <span class="font-medium">${l.name}</span>
              <div class="text-sm text-gray-500">${l.qty} × ${fmt(l.unitPrice, inv.currency)}</div>
            </div>
            <span class="font-medium">${fmt(l.lineTotal, inv.currency)}</span>
          </div>`).join("")}
      </div>

      <div class="border-t border-sky-100 pt-4 space-y-2">
        <div class="flex justify-between">
          <span class="text-gray-600">Subtotal</span>
          <span class="font-medium">${fmt(inv.subtotal, inv.currency)}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">IVA (${Math.round(100*state.taxRate)}%)</span>
          <span class="font-medium">${fmt(inv.tax, inv.currency)}</span>
        </div>
        <div class="flex justify-between text-lg font-bold pt-2">
          <span>Total</span>
          <span class="text-sky-700">${fmt(inv.total, inv.currency)}</span>
        </div>
      </div>
    </div>
  `;

  const footerHTML = `
    <div class="grid grid-cols-2 gap-3 no-print">
      <button onclick="window.print()"
        class="w-full rounded-xl py-3 px-4 font-semibold bg-gradient-to-r from-sky-500 to-indigo-500 text-white shadow-lg hover:shadow-xl">
        Imprimir
      </button>
      <button id="invCloseBtn2"
        class="w-full rounded-xl py-3 px-4 font-semibold border border-sky-200 bg-white text-sky-700 hover:bg-sky-50">
        Cerrar
      </button>
    </div>
  `;

  const close = openModal({ titleHTML, bodyHTML, footerHTML, size: "xl" });
  setTimeout(() => {
    const btn = el("#invCloseBtn2");
    if (btn) btn.onclick = () => close();
  });
}

// ------- Persistencia (PHP) -------
async function fetchProducts() {
  try {
    const res = await fetch("../backend/get_products.php");
    if (!res.ok) throw new Error("HTTP "+res.status);
    const data = await res.json();
    return {
      categories: [{ id: "all", label: "Todas", icon: "🧺" }, ...data.categories],
      products: data.products
    };
  } catch (e) {
    console.warn("Fallo get_products.php, usando fallback:", e.message);
    return FALLBACK;
  }
}

async function saveOrder(invoice) {
  try {
    const res = await fetch("../backend/save_order.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(invoice)
    });
    const data = await res.json();
    return data;
  } catch (e) {
    return { ok: false, error: e.message };
  }
}

// ------- Init -------
async function init() {
  const { categories, products } = await fetchProducts();
  state.categories = categories;
  state.products = products;

  el("#currencySelect").onchange = (e)=>{ state.currency = e.target.value; renderProducts(); renderCart(); };
  el("#searchInput").oninput = (e)=>{ state.query = e.target.value; renderProducts(); };
  el("#clearCartBtn").onclick = clearCart;
  el("#checkoutBtn").onclick = openCheckout;

  renderCategories();
  renderProducts();
  renderCart();
}
init();
