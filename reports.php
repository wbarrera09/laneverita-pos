<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ordenes - POS Sorbetes</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- CSS optimizado (animaciones + print + spinner) -->
  <link rel="stylesheet" href="public/assets/styles.css">

  
</head>

<body class="min-h-screen bg-gradient-to-br from-sky-50 to-cyan-50 text-gray-800 p-4 md:p-6">

  <!-- Header -->
  <header class="bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-white/50 p-4 md:p-6 mb-6 flex flex-col gap-4 items-start md:flex-row md:items-center md:justify-between">
    <div class="flex items-center gap-3">
      <div class="bg-gradient-to-r from-sky-500 to-indigo-500 p-2 rounded-xl shadow-md">
        <span class="text-white text-2xl">📝</span>
      </div>
      <h1 class="text-2xl font-bold text-sky-700">Historial de Órdenes</h1>
    </div>

    <div class="flex items-center gap-4">
    
      <a href="sold_items.php"
        class="ml-auto px-4 py-2 rounded-xl border border-sky-300 text-sky-700 font-medium bg-white hover:bg-sky-50 transition">
        🛍️ Productos Vendidos
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

    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
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
        <label class="block text-sm font-medium text-gray-700 mb-1">ID de orden</label>
        <input type="number" name="order_id" id="order_id" placeholder="Ej: 15"
               class="w-full rounded-xl border border-sky-200 px-4 py-2 focus:ring-2 focus:ring-sky-300 focus:border-sky-300">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
        <input type="text" name="customer" id="customer" placeholder="Nombre cliente"
               class="w-full rounded-xl border border-sky-200 px-4 py-2 focus:ring-2 focus:ring-sky-300 focus:border-sky-300">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Método de pago</label>
        <select name="payment_method" id="payment_method"
                class="w-full rounded-xl border border-sky-200 px-4 py-2 focus:ring-2 focus:ring-sky-300 focus:border-sky-300">
          <option value="">Todos</option>
        </select>
      </div>
    </form>
  </section>


  <!-- Estadísticas con contenido estático que luego actualizaremos -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" id="statsContainer">
  <!-- Total Ventas - verde esmeralda -->
  <div id="cardTotalVentas" class="p-5 rounded-2xl shadow-sm bg-gradient-to-r from-emerald-100 to-emerald-50 border border-emerald-200 hover:shadow-md transition flex flex-col">
    <div class="flex items-center justify-between mb-2">
      <div class="text-sm font-semibold text-emerald-600">Total Ventas</div>
      <i class="fas fa-dollar-sign text-xl text-emerald-500"></i>
    </div>
    <div class="text-3xl font-bold text-emerald-800" id="statTotalVentas">$0.00</div>
  </div>

  <!-- Órdenes - sin cambios -->
  <div id="cardOrdenes" class="p-5 rounded-2xl shadow-sm bg-gradient-to-r from-indigo-100 to-indigo-50 border border-indigo-200 hover:shadow-md transition flex flex-col">
    <div class="flex items-center justify-between mb-2">
      <div class="text-sm font-semibold text-indigo-600">Órdenes</div>
      <i class="fas fa-receipt text-xl text-indigo-500"></i>
    </div>
    <div class="text-3xl font-bold text-indigo-800" id="statOrdenes">0</div>
  </div>

  <!-- Ticket Promedio - gris con más contraste -->
  <div id="cardTicketPromedio" class="p-5 rounded-2xl shadow-sm bg-gradient-to-r from-gray-200 to-gray-100 border border-gray-300 hover:shadow-md transition flex flex-col">
    <div class="flex items-center justify-between mb-2">
      <div class="text-sm font-semibold text-gray-700">Ticket Promedio</div>
      <i class="fas fa-chart-line text-xl text-gray-600"></i>
    </div>
    <div class="text-3xl font-bold text-gray-900" id="statTicketPromedio">$0.00</div>
  </div>

  <!-- Método Principal - amarillo claro -->
  <div id="cardMetodoPrincipal" class="p-5 rounded-2xl shadow-sm bg-gradient-to-r from-amber-100 to-amber-50 border border-amber-200 hover:shadow-md transition flex flex-col">
    <div class="flex items-center justify-between mb-2">
      <div class="text-sm font-semibold text-amber-600">Método Principal</div>
      <i class="fas fa-credit-card text-xl text-amber-500"></i>
    </div>
    <div class="text-3xl font-bold text-amber-800" id="statMetodoPrincipal">-</div>
  </div>
</div>



  <!-- Resultados -->
  <section class="bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-white/50 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-sky-800 flex items-center gap-2">
        <i class="fas fa-list"></i> Resultados
      </h2>
      <div class="text-sm text-gray-500" id="resultsCount">Mostrando 0 resultados</div>
    </div>

    <div class="relative">
      <div id="loadingIndicator" class="hidden absolute inset-0 bg-white/80 flex items-center justify-center z-10 rounded-xl">
        <div class="loading-spinner"></div>
        <span class="ml-2 text-sky-700">Cargando...</span>
      </div>

      <div class="overflow-x-auto rounded-xl">
        <table class="min-w-full border-collapse" id="resultsTable">
          <thead>
            <tr class="bg-sky-50">
              <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider cursor-pointer hover:bg-sky-100" data-sort="id">
                ID <i class="fas fa-sort ml-1"></i>
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider cursor-pointer hover:bg-sky-100" data-sort="date_time">
                Fecha <i class="fas fa-sort ml-1"></i>
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider cursor-pointer hover:bg-sky-100" data-sort="customer_name">
                Cliente <i class="fas fa-sort ml-1"></i>
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider cursor-pointer hover:bg-sky-100" data-sort="total">
                Total <i class="fas fa-sort ml-1"></i>
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider cursor-pointer hover:bg-sky-100" data-sort="payment_method">
                Método de pago <i class="fas fa-sort ml-1"></i>
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-sky-700 uppercase tracking-wider">Notas</th>
            </tr>
          </thead>
          <tbody id="resultsBody" class="divide-y divide-sky-100">
            <tr><td colspan="6" class="text-center py-8 text-gray-500">Utilice los filtros para buscar órdenes</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Paginación -->
    <div class="mt-6 flex items-center justify-between" id="paginationContainer">
      <div class="text-sm text-gray-500">Página 1 de 1</div>
      <div class="flex gap-2">
        <button class="px-3 py-1 rounded-lg border border-sky-200 text-sky-700 disabled:opacity-50" disabled>
          <i class="fas fa-chevron-left"></i>
        </button>
        <button class="px-3 py-1 rounded-lg border border-sky-200 text-sky-700 disabled:opacity-50" disabled>
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </section>

  <!-- JS -->
  <script>
    const form = document.getElementById("filterForm");
    const tbody = document.getElementById("resultsBody");
    const btnExcel = document.getElementById("btnExcel");
    const loadingIndicator = document.getElementById("loadingIndicator");
    const resultsCount = document.getElementById("resultsCount");
    const clearFiltersBtn = document.getElementById("clearFilters");

    // Estadísticas
    const statTotalVentas = document.getElementById("statTotalVentas");
    const statOrdenes = document.getElementById("statOrdenes");
    const statTicketPromedio = document.getElementById("statTicketPromedio");
    const statMetodoPrincipal = document.getElementById("statMetodoPrincipal");

    let currentData = [];
    let currentSort = { field: null, direction: 'asc' };

    async function loadReports() {
      showLoading();
      const params = new URLSearchParams(new FormData(form)).toString();
      btnExcel.href = "./backend/export_excel.php?" + params;

      try {
        const res = await fetch("./backend/get_reports.php?" + params);
        const data = await res.json();

        currentData = data;
        renderResults(data);
        updateStats(data);
        updatePagination(data);
      } catch (error) {
        console.error("Error loading reports:", error);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-rose-600">Error al cargar los datos</td></tr>`;
      } finally {
        hideLoading();
      }
    }

    function renderResults(data) {
      // limpiar filas existentes
      while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
      }

      if (data.length === 0) {
        const tr = document.createElement("tr");
        const td = document.createElement("td");
        td.setAttribute("colspan", "6");
        td.className = "text-center py-8 text-gray-500";
        td.textContent = "No se encontraron resultados";
        tr.appendChild(td);
        tbody.appendChild(tr);
        resultsCount.textContent = "Mostrando 0 resultados";
        return;
      }

      resultsCount.textContent = `Mostrando ${data.length} resultado${data.length !== 1 ? 's' : ''}`;

      data.forEach(o => {
        const tr = document.createElement("tr");
        tr.className = "transition-colors hover:bg-sky-50";

        const tdId = document.createElement("td");
        tdId.className = "px-4 py-3 whitespace-nowrap text-sm font-medium text-sky-700";
        tdId.textContent = o.id;
        tr.appendChild(tdId);

        const tdFecha = document.createElement("td");
        tdFecha.className = "px-4 py-3 whitespace-nowrap text-sm text-gray-700";
        tdFecha.textContent = formatDateTime(o.date_time);
        tr.appendChild(tdFecha);

        const tdCliente = document.createElement("td");
        tdCliente.className = "px-4 py-3 whitespace-nowrap text-sm text-gray-700";
        tdCliente.textContent = o.customer_name || "-";
        tr.appendChild(tdCliente);

        const tdTotal = document.createElement("td");
        tdTotal.className = "px-4 py-3 whitespace-nowrap text-sm font-medium text-sky-700";
        tdTotal.textContent = `$${parseFloat(o.total).toFixed(2)}`;
        tr.appendChild(tdTotal);

        const tdMetodo = document.createElement("td");
        tdMetodo.className = "px-4 py-3 whitespace-nowrap text-sm text-gray-700";
        const spanMetodo = document.createElement("span");
        spanMetodo.className = "px-2 py-1 bg-sky-100 text-sky-700 rounded-full text-xs";
        spanMetodo.textContent = o.payment_method;
        tdMetodo.appendChild(spanMetodo);
        tr.appendChild(tdMetodo);

        const tdNotas = document.createElement("td");
        tdNotas.className = "px-4 py-3 text-sm text-gray-700";
        tdNotas.textContent = o.notes || "";
        tr.appendChild(tdNotas);

        tbody.appendChild(tr);
      });
    }

    function updateStats(data) {
      if (!data || data.length === 0) {
        statTotalVentas.textContent = "$0.00";
        statOrdenes.textContent = "0";
        statTicketPromedio.textContent = "$0.00";
        statMetodoPrincipal.textContent = "-";
        return;
      }

      const totalSales = data.reduce((sum, order) => sum + parseFloat(order.total), 0);
      const averageTicket = totalSales / data.length;

      const paymentMethods = data.reduce((acc, order) => {
        acc[order.payment_method] = (acc[order.payment_method] || 0) + 1;
        return acc;
      }, {});
      const mainPaymentMethod = Object.keys(paymentMethods).reduce((a, b) =>
        paymentMethods[a] > paymentMethods[b] ? a : b, 'N/A');

      statTotalVentas.textContent = `$${totalSales.toFixed(2)}`;
      statOrdenes.textContent = `${data.length}`;
      statTicketPromedio.textContent = `$${averageTicket.toFixed(2)}`;
      statMetodoPrincipal.textContent = mainPaymentMethod;
    }

    function formatDateTime(dateTimeString) {
      if (!dateTimeString) return '-';
      const date = new Date(dateTimeString);
      return new Intl.DateTimeFormat('es-ES', {
        day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
      }).format(date);
    }

    function updatePagination(_) {
      // tu lógica si hay paginación
    }

    function showLoading(){ loadingIndicator.classList.remove('hidden'); }
    function hideLoading(){ loadingIndicator.classList.add('hidden'); }

    async function loadPaymentMethods() {
      try {
        const res = await fetch("./backend/get_payment_methods.php");
        const data = await res.json();
        const select = document.getElementById("payment_method");
        // Limpiar opciones anteriores si hay
        select.innerHTML = '<option value="">Todos</option>';
        data.forEach(pm => {
          const opt = document.createElement("option");
          opt.value = pm; opt.textContent = pm;
          select.appendChild(opt);
        });
      } catch (error) {
        console.error("Error loading payment methods:", error);
      }
    }

    function clearFilters() {
      form.reset();
      setMonthFilters();
      loadReports();
    }

    function sortTable(field) {
      if (currentSort.field === field) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
      } else {
        currentSort.field = field;
        currentSort.direction = 'asc';
      }

      const sortedData = [...currentData].sort((a, b) => {
        let valueA = a[field], valueB = b[field];
        if (valueA == null) valueA = ''; if (valueB == null) valueB = '';
        if (field === 'id' || field === 'total') {
          valueA = parseFloat(valueA); valueB = parseFloat(valueB);
          return currentSort.direction === 'asc' ? valueA - valueB : valueB - valueA;
        }
        if (typeof valueA === 'string') valueA = valueA.toLowerCase();
        if (typeof valueB === 'string') valueB = valueB.toLowerCase();
        if (valueA < valueB) return currentSort.direction === 'asc' ? -1 : 1;
        if (valueA > valueB) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
      });

      renderResults(sortedData);
      updateSortIndicators();
    }

    function updateSortIndicators() {
      document.querySelectorAll('th[data-sort]').forEach(th => {
        const icon = th.querySelector('i');
        if (th.dataset.sort === currentSort.field) {
          icon.className = currentSort.direction === 'asc' ? 'fas fa-sort-up ml-1' : 'fas fa-sort-down ml-1';
        } else {
          icon.className = 'fas fa-sort ml-1';
        }
      });
    }

    form.querySelectorAll("input, select").forEach(input => {
      if (input.name === "order_id" || input.name === "customer") {
        let timeout;
        input.addEventListener("input", () => {
          clearTimeout(timeout);
          timeout = setTimeout(loadReports, 500);
        });
      } else {
        input.addEventListener("change", loadReports);
      }
    });

    function setMonthFilters() {
      const start = document.getElementById("fecha_inicio");
      const end = document.getElementById("fecha_fin");
      const now = new Date();
      const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
      const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
      start.value = firstDay.toISOString().split("T")[0];
      end.value = lastDay.toISOString().split("T")[0];
    }

    document.addEventListener("DOMContentLoaded", async () => {
      setMonthFilters();
      await loadPaymentMethods();
      clearFiltersBtn.addEventListener('click', clearFilters);
      document.querySelectorAll('th[data-sort]').forEach(th => th.addEventListener('click', () => sortTable(th.dataset.sort)));
      loadReports();
    });
  </script>
</body>
</html>
