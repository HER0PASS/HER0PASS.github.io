<?php

require __DIR__ . '/bbdd/conexion.php';
require __DIR__ . '/funcionesToken.php';

header("Content-Type: application/json");

handleRequest();
