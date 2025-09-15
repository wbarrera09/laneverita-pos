<?php

// IMPORTANTE Cambiar el nombre del archivo a db.php dentro de la carpeta backend

const DB_HOST = '127.0.0.1';
const DB_NAME = 'database';
const DB_USER = 'usuario';
const DB_PASS = 'contraseña';


function pdo() {
  static $pdo = null;
  if ($pdo === null) {
    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false
    ]);
  }
  return $pdo;
}
