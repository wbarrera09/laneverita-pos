<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Productos Vendidos - POS Sorbetes</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="public/assets/styles.css">
</head>

<body class="min-h-screen bg-gradient-to-br from-sky-50 to-cyan-50 text-gray-800 p-4 md:p-6">

  <!-- Header -->
  <header class="bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-white/50 p-4 md:p-6 mb-6 flex flex-col gap-4 items-start md:flex-row md:items-center md:justify-between">
    <div class="flex items-center gap-3">
      <div class="bg-gradient-to-r from-sky-500 to-indigo-500 p-2 rounded-xl shadow-md">
        <span class="text-white text-2xl">🛍️</span>
      </div>
      <h1 class="text-2xl font-bold text-sky-700">Productos Vendidos</h1>
    </div>

    
    <div class="flex items-center gap-4"> 
        <a href="orders_report.php"
        class="ml-auto px-4 py-2 rounded-xl border border-sky-300 text-sky-700 font-medium bg-white hover:bg-sky-50 transition">
        📝 Historial de Órdenes
        </a>
        <a href="index.html"
        class="ml-auto px-4 py-2 rounded-xl border border-sky-300 text-sky-700 font-medium bg-white hover:bg-sky-50 transition">
        <i class="fas fa-arrow-left"></i>
        Volver al POS
        </a>
    </div>
  </header>

  <!-- Filtros -->
  <section class="bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-white/50 p-6 mb-6">
    <div class="flex justify-between items-center border-b border-sky-100 pb-3 mb-4">
      <h2 class="text-2xl font-bold text-sky-800 flex items-center gap-2">
        <i class="fas fa-filter text-sky-600"></i>
        Filtros de búsqueda
      </h2>
      <div class="flex gap-3">
        <button id="clearFilters" type="button"
          class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 text-white rounded-xl font-medium shadow-md hover:bg-gray-600 hover:shadow-sm transition">
          <i class="fas fa-times"></i> Limpiar filtros
        </button>

        <a id="btnExcel" href="#"
          class="inline-flex items-center gap-2 px-4 py-2 bg-green-800 text-white rounded-xl font-medium shadow-md hover:bg-green-700 hover:shadow-sm transition">
          <i class="fas fa-file-excel"></i> Exportar Excel
        </a>
      </div>
    </div>

    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
        <input type="date" name="start" id="fecha_inicio"
               class="w-full rounded-xl border border-sky-200 px-4 py-2 focus:ring-2 focus:ring-sky-300 focus:border-sky-300">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin</label>
        <input type="date" name="end" id="fecha_fin"
               class="w-full rounded-xl border border-sky-200 px-4 py-2 focus:ring-2 focus:ring-sky-300 focus:border-sky-300">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
        <input type="text" name="customer" placeholder="Nombre cliente"
               class="w-full rounded-xl border border-sky-200 px-4 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
        <input type="text" name="product" placeholder="Nombre producto"
               class="w-full rounded-xl border border-sky-200 px-4 py-2">
      </div>
    </form>
  </section>

  <!-- Resultados -->
  <section class="bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-white/50 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-sky-800 flex items-center gap-2">
        <i class="fas fa-list"></i> Resultados
      </h2>
      <div class="text-sm text-gray-500" id="resultsCount">Mostrando 0 resultados</div>
    </div>

    <div class="overflow-x-auto rounded-xl">
      <table class="min-w-full border-collapse">
        <thead>
          <tr class="bg-sky-50">
            <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase">Orden</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase">Fecha</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase">Cliente</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase">Producto</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-sky-700 uppercase">Cantidad</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-sky-700 uppercase">P. Unitario</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-sky-700 uppercase">Subtotal</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase">Método</th>
          </tr>
        </thead>
        <tbody id="resultsBody" class="divide-y divide-sky-100">
          <tr><td colspan="8" class="text-center py-8 text-gray-500">Sin resultados</td></tr>
        </tbody>
      </table>
    </div>
  </section>

  <!-- JS -->
<script>
  const form = document.getElementById("filterForm");
  const tbody = document.getElementById("resultsBody");
  const resultsCount = document.getElementById("resultsCount");
  const btnExcel = document.getElementById("btnExcel");
  const clearFiltersBtn = document.getElementById("clearFilters");

  // Spinner
  const loadingIndicator = document.createElement("div");
  loadingIndicator.id = "loadingIndicator";
  loadingIndicator.className = "hidden absolute inset-0 bg-white/80 flex items-center justify-center z-10 rounded-xl";
  loadingIndicator.innerHTML = `
    <div class="loading-spinner"></div>
    <span class="ml-2 text-sky-700">Cargando...</span>
  `;
  document.body.appendChild(loadingIndicator);

  function showLoading() { loadingIndicator.classList.remove("hidden"); }
  function hideLoading() { loadingIndicator.classList.add("hidden"); }

  async function loadItems() {
    showLoading();
    try {
      const params = new URLSearchParams(new FormData(form)).toString();
      btnExcel.href = "./backend/export_sold_items.php?" + params;

      const res = await fetch("./backend/get_sold_items.php?" + params);
      const data = await res.json();

      renderResults(data);
    } catch (err) {
      console.error(err);
      tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-rose-600">Error al cargar datos</td></tr>`;
    } finally {
      hideLoading();
    }
  }

  function renderResults(data) {
    tbody.innerHTML = "";

    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-gray-500">No se encontraron resultados</td></tr>`;
      resultsCount.textContent = "Mostrando 0 resultados";
      return;
    }

    resultsCount.textContent = `Mostrando ${data.length} resultado${data.length !== 1 ? "s" : ""}`;

    data.forEach(item => {
      const tr = document.createElement("tr");
      tr.className = "hover:bg-sky-50 transition";
      tr.innerHTML = `
        <td class="px-4 py-3 text-sm text-sky-700 font-medium">${item.order_id}</td>
        <td class="px-4 py-3 text-sm text-gray-700">${formatDate(item.date_time)}</td>
        <td class="px-4 py-3 text-sm text-gray-700">${item.customer_name || "-"}</td>
        <td class="px-4 py-3 text-sm text-gray-700">${item.product_name}</td>
        <td class="px-4 py-3 text-sm text-center">${item.qty}</td>
        <td class="px-4 py-3 text-sm text-right">$${parseFloat(item.unit_price).toFixed(2)}</td>
        <td class="px-4 py-3 text-sm text-right">$${parseFloat(item.line_total).toFixed(2)}</td>
        <td class="px-4 py-3 text-sm">${item.payment_method}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  function formatDate(dt) {
    const d = new Date(dt);
    return new Intl.DateTimeFormat("es-ES", {
      day: "2-digit", month: "2-digit", year: "numeric",
      hour: "2-digit", minute: "2-digit"
    }).format(d);
  }

  function clearFilters() {
    form.reset();
    setMonthFilters();
    loadItems();
  }

  function setMonthFilters() {
    const start = document.getElementById("fecha_inicio");
    const end = document.getElementById("fecha_fin");
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    start.value = firstDay.toISOString().split("T")[0];
    end.value = lastDay.toISOString().split("T")[0];
  }

  // Eventos de filtros
  form.querySelectorAll("input").forEach(input => {
    if (input.type === "text") {
      let timeout;
      input.addEventListener("input", () => {
        clearTimeout(timeout);
        timeout = setTimeout(loadItems, 500); // debounce
      });
    } else {
      input.addEventListener("change", loadItems); // fechas usan change
    }
  });

  document.addEventListener("DOMContentLoaded", () => {
    setMonthFilters();
    clearFiltersBtn.addEventListener("click", clearFilters);
    loadItems();
  });
</script>

</body>
</html>
