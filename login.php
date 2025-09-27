<?php
session_start();

// Si ya está logueado, se manda directo al POS
if (isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit();
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = $_POST['usuario'] ?? '';
  $pass = $_POST['password'] ?? '';

  // Validación simple
  if ($user === 'neverita' && $pass === 'neverita123') {
    $_SESSION['usuario'] = $user;
    header("Location: index.php");
    exit();
  } else {
    $error = "Usuario o contraseña incorrectos";
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login | La Neverita</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="public/assets/styles.css" />
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-sky-50 to-cyan-100">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 animate-pop-in">
    <div class="text-center mb-6">
      <div class="inline-flex p-3 rounded-xs">
        <img src="images/neverita-logo.jpg" alt="Logo" class="h-40 w-40 object-contain rounded-full" />

      </div>


      <h1 class="text-2xl font-bold text-sky-700 mt-3">La Neverita - POS</h1>
      <p class="text-gray-500 text-sm">Inicia sesión para continuar</p>
    </div>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Usuario</label>
        <input name="usuario" type="text" placeholder="Ingresa tu usuario"
          class="mt-1 w-full border border-sky-200 rounded-xl p-3 focus:ring-1 focus:ring-sky-300 focus:outline-none" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input name="password" type="password" placeholder="••••••••"
          class="mt-1 w-full border border-sky-200 rounded-xl p-3 focus:ring-1 focus:ring-sky-300 focus:outline-none" required>
      </div>

      <button type="submit"
        class="w-full rounded-xl py-3 px-4 font-semibold bg-gradient-to-r from-sky-500 to-indigo-500 text-white shadow-md hover:shadow-lg active:scale-95 transition">
        Ingresar
      </button>

      <?php if (!empty($error)) : ?>
        <p class="text-red-500 text-sm mt-2 text-center"><?= $error ?></p>
      <?php endif; ?>
    </form>
  </div>

  <style>
    @keyframes pop-in { 0%{transform:scale(.95);opacity:0} 100%{transform:scale(1);opacity:1} }
    .animate-pop-in { animation: pop-in .3s ease-out both; }
  </style>
</body>
</html>
