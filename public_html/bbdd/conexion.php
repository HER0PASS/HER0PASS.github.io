<?php
  $host = "localhost"; // Cambia si tu servidor no es local
  $user = "root"; // Tu usuario de MySQL
  $password = ""; // Tu contraseña de MySQL
  $database = "heropass"; // Nombre de la base de datos
  // Crear conexión
  $conn = new mysqli($host, $user, $password, $database);

  // Verificar conexión
  if ($conn->connect_error) {
      die("❌ Conexión fallida: " . $conn->connect_error);
  } 
?>
