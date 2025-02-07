
# TWITCH ANALYTICS

Primera entrega de la asignatura Verificación y Validación del Software.  
API pública programada en PHP, capaz de hacer 3 tipos de consultas que obtienen información sobre streamers, usuarios y streams a partir de consultas a la API de Twitch.  
Alumnos: Asier Muñoz, Iñigo Aznárez, Miguel Vallés y Alejandro Molina

## Ejecutar el proyecto:

### En línea

#### ACLARACIÓN: PETICIONES POSTMAN DETALLADAS EN EL ARCHIVO Entrega1.pdf

#### CASO DE USO 1: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH  
Este endpoint permite a los clientes consultar la información de un streamer de Twitch mediante su ID.  
Ejemplo con el id de usuario de ibai: 83232866
```bash
  http://heropass.es/analytics/streamer.php?id=83232866
```

#### CASO DE USO 2: CONSULTAR STREAMS EN VIVO  
Este endpoint permite a los clientes obtener una lista de los 10  streams con más espectadores que están actualmente en vivo en Twitch.
```bash
  http://heropass.es/analytics/streams.php
```

#### CASO DE USO 3: CONSULTAR “TOP STREAMS ENRIQUECIDOS”  
Este caso de uso realiza un filtrado y enriquecimiento del listado de Streams en Vivo. El objetivo es:
1. Obtener la lista de streams en vivo de Twitch (en este caso hecho con los 10 primeros).  
2. Ordenar esos streams por el número de espectadores (en orden ascendente en nuestro caso).  
3. Seleccionar los primeros N (parámetro limit: de 1 hasta 10).
4. Enriquecer cada stream con información adicional del usuario display_name o profile_image_url.  
5. Devolver al cliente un listado combinado.

```bash
  http://heropass.es/analytics/topStreams.php?limit=2
```

### Localmente

Para ejecutar el proyecto localmente hemos utilizado la herramienta XAMPP, mediante la cual levantábamos el servicio web en local. Las direcciones para ejecutar los casos son las siguientes:

#### CASO DE USO 1: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH  
```bash
  http://localhost/.../HER0PASS.github.io/public_html/analytics/streamer.php?id=1234
```
Aclaración: los 3 puntos (...) se refieren a la localización en local del proyecto  

#### CASO DE USO 2: CONSULTAR STREAMS EN VIVO  
```bash
  http://localhost/.../HER0PASS.github.io/public_html/analytics/streams.php
```

#### CASO DE USO 3: CONSULTAR “TOP STREAMS ENRIQUECIDOS”  
```bash
  http://localhost/.../HER0PASS.github.io/public_html/analytics/topStreams.php?limit=2
```

## Extra
A pesar de que no se pidiese en la entrega, realizamos una página web en html que permite interaccionar con la API
 - [Página heropass HTML](http://heropass.es/)


