<?php
  $host = "db5017192845.hosting-data.io"; // Cambia si tu servidor no es local
  $user = "dbu2750275"; // Tu usuario de MySQL
  $password = "HeroPassPass1"; // Tu contraseña de MySQL
  $database = "dbs13808414"; // Nombre de la base de datos
  // Crear conexión
  $conn = new mysqli($host, $user, $password, $database);

  // Verificar conexión
  if ($conn->connect_error) {
      die("❌ Conexión fallida: " . $conn->connect_error);
  } 
?>
