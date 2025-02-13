<?php

    include 'conexion.php';


    // Procesar el formulario cuando se envía
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los datos del formulario

        $email = $_POST['email'];

        // Validar que el campo email no esté vacío
        if (empty($email)) {
            echo "❌ El email es obligatorio.";
        } else {

            echo "✅ El email es: " . $email;

            // Generar una API Key aleatoria (16 caracteres alfanuméricos)
            $api_key = bin2hex(random_bytes(8)); // Esto genera una API Key de 16 caracteres hexadecimales

            echo "✅ La API Key es: " . $api_key;

            
           // Intentamos preparar la consulta
            $conn->execute("SELECT * FROM users");

            // Verificar si la consulta se preparó correctamente
            if ($conn->error) {
                echo "❌ Error en la preparación de la consulta: " . $conn->error;
            } else {
                echo "✅ Preparación de la consulta exitosa.";
            }

            '''
           $stmt = $conn->prepare("SELECT * FROM users");

           // Verificar si la consulta se preparó correctamente
           if ($stmt === false) {
               echo "❌ Error en la preparación de la consulta: " . $conn->error;
           } else {
                echo "✅ Preparación de la consulta exitosa.";
               // Ejecutar la declaración
               $stmt->execute();

               // Comprobar si la consulta afectó filas
               if ($stmt->affected_rows > 0) {
                   echo "✅ Se encontraron usuarios en la base de datos.";
               } else {
                   echo "❌ No se encontraron usuarios en la base de datos.";
               }

               // Cerrar la declaración
               $stmt->close();
           }
               '''
       }
   }

   // Cerrar la conexión
   $conn->close();
?>