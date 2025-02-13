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
  http://heropass.es/analytics/user?id=83232866
```

#### CASO DE USO 2: CONSULTAR STREAMS EN VIVO  
Este endpoint permite a los clientes obtener una lista de los 10  streams con más espectadores que están actualmente en vivo en Twitch.
```bash
  http://heropass.es/analytics/streams
```

#### CASO DE USO 3: CONSULTAR “TOP STREAMS ENRIQUECIDOS”  
Este caso de uso realiza un filtrado y enriquecimiento del listado de Streams en Vivo. El objetivo es:
1. Obtener la lista de streams en vivo de Twitch (en este caso hecho con los 10 primeros).  
2. Ordenar esos streams por el número de espectadores (en orden ascendente en nuestro caso).  
3. Seleccionar los primeros N (parámetro limit: de 1 hasta 10).
4. Enriquecer cada stream con información adicional del usuario display_name o profile_image_url.  
5. Devolver al cliente un listado combinado.

```bash
  http://heropass.es/analytics/streams/enriched?limit=3
```

### Nuevos Endpoints

#### REGISTRO DE USUARIOS
Este endpoint permite registrar un nuevo usuario y obtener una API Key única.
```bash
  POST http://heropass.es/api/register
```
Ejemplo de Request:
```json
{
  "email": "usuario@example.com"
}
```
Ejemplo de Respuesta:
```json
{
  "api_key": "abcd1234efgh5678"
}
```

#### OBTENCIÓN DE TOKEN DE SESIÓN
Este endpoint permite obtener un token de sesión válido por 3 días utilizando una API Key.
```bash
  POST http://heropass.es/api/token
```
Ejemplo de Request:
```json
{
  "email": "usuario@example.com",
  "api_key": "abcd1234efgh5678"
}
```
Ejemplo de Respuesta:
```json
{
  "token": "generated_token"
}
```

#### AUTENTICACIÓN
Todos los endpoints requieren autenticación mediante un token válido. El token debe ser enviado en el encabezado Authorization en cada petición.
Ejemplo de petición autenticada:
```bash
  GET /analytics/topsofthetops
  Host: miserver.com
  Authorization: Bearer generated_token
```


