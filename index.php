<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>La Neverita</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="public/assets/styles.css" />
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-50 to-cyan-50 text-gray-800">

  <!-- Notificación -->
  <div id="notification-root"></div>

  <!-- Header -->
  <header class="p-4 flex flex-col md:flex-row md:justify-between md:items-center gap-4 border-b border-sky-200 bg-white shadow-sm">
  <div class="flex items-center gap-3">
      <div class="inline-flex p-3 rounded-xs">
        <img src="images/neverita-logo.jpg" alt="Logo" class="h-16 object-contain rounded-xl" />
      </div>
    <h1 class="text-2xl font-bold text-sky-700">La Neverita - POS</h1>
  </div>

  <div class="flex flex-col gap-3 w-full md:flex-row md:gap-3 md:w-auto text-center">
    <a href="orders_report.php"
      class="px-4 py-2 rounded-xl border border-sky-300 text-sky-700 font-medium bg-white hover:bg-sky-50 transition">
      📝 Historial de Órdenes
    </a>

    <a href="sold_items.php"
      class="px-4 py-2 rounded-xl border border-sky-300 text-sky-700 font-medium bg-white hover:bg-sky-50 transition">
      🛍️ Productos Vendidos
    </a>

    <select id="currencySelect"
      class="px-4 py-2 rounded-xl border border-sky-300 text-sky-700 text-center font-medium bg-white hover:bg-sky-50 shadow-sm transition cursor-pointer appearance-none">
      <option value="USD">USD $</option>
      <option value="EUR">EUR €</option>
    </select>

    <a href="logout.php"
      class="px-4 py-2 rounded-xl border border-sky-300 text-sky-700 font-medium bg-white hover:bg-sky-50 transition">
      Cerrar Sesión
    </a>
  </div>

</header>


  <!-- Contenedor -->
  <div class="max-w-full mx-auto w-full">

    <!-- Main -->
    <main class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-6 p-4 md:p-6">

      <!-- Catálogo -->
      <section>
        <!-- Buscador -->
        <div class="relative mb-4">
          <input id="searchInput" placeholder="Buscar producto..."
            class="w-full rounded-xl border border-sky-200 p-3 pl-10 focus:ring-1 focus:ring-sky-300 focus:outline-none"/>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3.5"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>

        <!-- Categorías -->
        <div id="categoriesBar" class="flex gap-2 mb-6 flex-wrap"></div>

        <!-- Grid de productos -->
        <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4"></div>
      </section>

      <!-- Carrito Desktop -->
      <aside class="hidden md:block bg-white border border-sky-100 rounded-2xl p-5 shadow-sm md:h-[600px] sticky top-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-bold text-xl text-sky-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Carrito
          </h2>
          
          <button id="clearCartBtn" class="text-sm text-rose-500 hover:text-rose-700 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Vaciar
          </button>
        </div>

        <div id="cartLines" class="space-y-3 max-h-80 overflow-y-auto pr-2"></div>

        <div id="cartTotals" class="hidden">
          <hr class="my-4 border-sky-100" />
          <div class="flex justify-between text-lg font-bold pt-2 border-t border-sky-100">
            <span>Total</span>
            <span id="totalText" class="text-sky-700">—</span>
          </div>
          <button id="checkoutBtn"
            class="mt-6 w-full rounded-xl py-3 px-4 font-semibold bg-gradient-to-r from-sky-500 to-indigo-500 text-white shadow-md hover:shadow-lg active:scale-95">
            <div class="flex items-center justify-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
              </svg>
              Ir a pagar
            </div>
          </button>
        </div>
      </aside>
    </main>
  </div>

  <!-- Botón carrito flotante (solo móvil) -->
<button id="toggleCarrito"
  class="fixed bottom-6 right-6 bg-blue-600 text-white p-4 rounded-full shadow-lg z-50 md:hidden">
  <!-- Icono Heroicon -->
  <svg xmlns="http://www.w3.org/2000/svg" 
       fill="none" 
       viewBox="0 0 24 24" 
       stroke-width="2" 
       stroke="currentColor" 
       class="w-6 h-6">
    <path stroke-linecap="round" 
          stroke-linejoin="round" 
          d="M2.25 2.25h1.5l2.25 12.75h12.75l2.25-9H6.75M9 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm9 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
  </svg>
</button>
  <!-- Backdrop para el drawer (móvil) -->
  <div id="drawerBackdrop"
       class="fixed inset-0 bg-black/30 opacity-0 pointer-events-none transition-opacity md:hidden z-40"></div>

  <!-- Carrito lateral Mobile (se abre desde la IZQUIERDA) -->
  <div id="carritoMobile"
       class="fixed top-0 left-0 w-80 h-full bg-white shadow-xl transform -translate-x-full transition-transform duration-300 md:hidden z-50">
    <div class="p-4 flex justify-between items-center border-b">
      <h2 class="font-bold text-lg">Carrito</h2>
      <button id="closeCarrito" class="text-gray-500 hover:text-gray-800">✖</button>
    </div>

    <div id="cartLinesMobile" class="p-4 space-y-3 overflow-y-auto h-[65%]"></div>

    <div class="p-4 border-t">
      <div class="flex justify-between font-bold mb-4">
        <span>Total</span>
        <span id="totalTextMobile">—</span>
      </div>
      <button id="checkoutBtnMobile"
              class="w-full bg-gradient-to-r from-sky-500 to-indigo-500 text-white py-3 rounded-xl font-semibold">
        Ir a pagar
      </button>
    </div>
  </div>

  <!-- Modal (container) -->
  <div id="modalRoot"></div>

  <!-- Factura (modal/imprimible) -->
  <div id="invoiceRoot"></div>

  <!-- JS de la app -->
  <script src="public/assets/app.js"></script>

  <!-- JS del drawer móvil -->
  <script>
    const toggleBtn = document.getElementById('toggleCarrito');
    const drawer = document.getElementById('carritoMobile');
    const closeBtn = document.getElementById('closeCarrito');
    const backdrop = document.getElementById('drawerBackdrop');

    function openDrawer() {
      drawer.classList.remove('-translate-x-full');
      backdrop.classList.remove('opacity-0', 'pointer-events-none');
      document.body.classList.add('overflow-hidden');
    }
    function closeDrawer() {
      drawer.classList.add('-translate-x-full');
      backdrop.classList.add('opacity-0', 'pointer-events-none');
      document.body.classList.remove('overflow-hidden');
    }

    if (toggleBtn && drawer && closeBtn && backdrop) {
      toggleBtn.addEventListener('click', openDrawer);
      closeBtn.addEventListener('click', closeDrawer);
      backdrop.addEventListener('click', closeDrawer);
      window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });
    }
  </script>
</body>
</html>
