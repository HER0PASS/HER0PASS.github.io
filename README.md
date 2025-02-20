# TWITCH ANALYTICS

Primera entrega de la asignatura Verificación y Validación del Software.  
API pública programada en PHP, capaz de hacer consultas a la API de Twitch para obtener información sobre streamers, usuarios y streams.  

**Alumnos:** Asier Muñoz, Iñigo Aznárez, Miguel Vallés y Alejandro Molina

## Índice
- [Ejecutar el proyecto](#ejecutar-el-proyecto)
  - [En línea](#en-línea)
  - [Localmente](#localmente)
- [ENTREGA 1: Endpoints iniciales](#entrega-1-endpoints-iniciales)
  - [En línea](#en-línea-1)
  - [Localmente](#localmente-1)
- [ENTREGA 2: Nuevos Endpoints](#entrega-2-nuevos-endpoints)
  - [En línea](#en-línea-2)
  - [Localmente](#localmente-2)

---

## **ENTREGA 1: Endpoints iniciales**

### En línea

#### ACLARACIÓN: PETICIONES POSTMAN DETALLADAS EN EL ARCHIVO `Entrega1.pdf`

---

### **CASO DE USO 1: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH**  
Este endpoint permite consultar la información de un streamer de Twitch mediante su ID.  
Ejemplo con el id de usuario de Ibai: `83232866`
```bash
http://heropass.es/analytics/user?id=83232866
```

### **CASO DE USO 2: CONSULTAR STREAMS EN VIVO**  
Este endpoint obtiene una lista de los 10 streams con más espectadores que están actualmente en vivo en Twitch.
```bash
http://heropass.es/analytics/streams
```

### **CASO DE USO 3: CONSULTAR “TOP STREAMS ENRIQUECIDOS”**  
Este caso de uso realiza un filtrado y enriquecimiento del listado de Streams en Vivo.
1. Obtener la lista de streams en vivo de Twitch (los 10 primeros).
2. Ordenar los streams por número de espectadores (ascendente).
3. Seleccionar los primeros N (`limit`: de 1 hasta 10).
4. Enriquecer cada stream con información adicional del usuario (`display_name` o `profile_image_url`).
5. Devolver al cliente un listado combinado.

```bash
http://heropass.es/analytics/streams/enriched?limit=3
```

### Localmente

Para ejecutar el proyecto localmente hemos utilizado la herramienta XAMPP, mediante la cual levantábamos el servicio web en local. Las direcciones para ejecutar los casos son las siguientes:

#### CASO DE USO 1: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH  
```bash
  http://localhost/.../HER0PASS.github.io/public_html/analytics/user?id=83232866
```
Aclaración: los 3 puntos (...) se refieren a la localización en local del proyecto  

#### CASO DE USO 2: CONSULTAR STREAMS EN VIVO  
```bash
  http://localhost/.../HER0PASS.github.io/public_html/analytics/streams
```

#### CASO DE USO 3: CONSULTAR “TOP STREAMS ENRIQUECIDOS”  
```bash
  http://localhost/.../HER0PASS.github.io/public_html/analytics/streams/enriched?limit=3
```

---
## **ENTREGA 2: Nuevos Endpoints**

### En línea 2

#### ACLARACIÓN: PETICIONES POSTMAN DETALLADAS EN EL ARCHIVO `Entrega2.pdf`

---

### **CASO DE USO 1: REGISTRO DE USUARIOS**  
Este endpoint permite registrar un nuevo usuario y obtener una API Key única.
```bash
POST http://heropass.es/register
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

### **CASO DE USO 2: OBTENCIÓN DE TOKEN DE SESIÓN**
Este endpoint permite obtener un token de sesión válido por 3 días utilizando una API Key.
```bash
POST http://heropass.es/token
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

### **CASO DE USO 3: TOPS OF THE TOPS**
Este endpoints requieren autenticación mediante un token válido. El token debe ser enviado en el encabezado `X-Auth-Token` en cada petición.
Este endpoint proporcionará información sobre los 40 videos más visualizados de cada uno de los tres juegos más populares en Twitch.

Ejemplo de petición autenticada:
```bash
GET /analytics/topsofthetops
Host: heropass.es
X-Auth-Token: generated_token
```

Este endopoint permite el parámetro since con el cual se fuerza a la actualización de la base de datos. Se indica en segundos
Ejemplo de petición autenticada con el parámetro sice:
```bash
GET /analytics/topsofthetops?since=300
Host: heropass.es
X-Auth-Token: generated_token
```


### Localmente 2

Para ejecutar el proyecto localmente hemos utilizado la herramienta XAMPP, mediante la cual levantábamos el servicio web y la base de datos en local. Las direcciones para ejecutar los casos son las siguientes:


### **CASO DE USO 1: REGISTRO DE USUARIOS**  
Este endpoint permite registrar un nuevo usuario y obtener una API Key única.
```bash
POST http://localhost/.../HER0PASS.github.io/public_html/register
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

### **CASO DE USO 2: OBTENCIÓN DE TOKEN DE SESIÓN**
Este endpoint permite obtener un token de sesión válido por 3 días utilizando una API Key.
```bash
POST http://localhost/.../HER0PASS.github.io/public_html/token
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

### **CASO DE USO 3: TOPS OF THE TOPS**
Este endpoints requieren autenticación mediante un token válido. El token debe ser enviado en el encabezado `X-Auth-Token` en cada petición.
Este endpoint proporcionará información sobre los 40 videos más visualizados de cada uno de los tres juegos más populares en Twitch.

```bash
GET http://localhost/.../HER0PASS.github.io/public_html/analytics/topsofthetops
```


```bash
GET http://localhost/.../HER0PASS.github.io/public_html/analytics/topsofthetops?since=300

