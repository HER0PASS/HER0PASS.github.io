# TWITCH ANALYTICS

Primera entrega de la asignatura Verificación y Validación del Software.  
API pública programada en PHP, capaz de hacer consultas a la API de Twitch para obtener información sobre streamers, usuarios y streams.  

**Alumnos:** Asier Muñoz, Iñigo Aznárez, Miguel Vallés y Alejandro Molina

---

## Ejecutar el proyecto:

### En línea

#### ACLARACIÓN: PETICIONES POSTMAN DETALLADAS EN EL ARCHIVO `Entrega1.pdf`

---

## **ENTREGA 1: Endpoints iniciales**

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

---

## **ENTREGA 2: Nuevos Endpoints**

### **REGISTRO DE USUARIOS**
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

### **OBTENCIÓN DE TOKEN DE SESIÓN**
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

### **AUTENTICACIÓN**
Todos los endpoints requieren autenticación mediante un token válido. El token debe ser enviado en el encabezado `Authorization` en cada petición.

Ejemplo de petición autenticada:
```bash
GET /analytics/topsofthetops
Host: heropass.es
Authorization: Bearer generated_token
```