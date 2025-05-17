# TWITCH ANALYTICS

Tercera entrega de la asignatura Verificación y Validación del Software.  
API pública programada en PHP, capaz de hacer consultas a la API de Twitch para obtener información sobre streamers, usuarios y streams.  

**Alumnos:** Asier Muñoz, Iñigo Aznárez, Miguel Vallés y Alejandro Molina

## Índice
- [Ejecutar el proyecto](#ejecutar-el-proyecto)
  - [En línea](#en-línea)
  - [Localmente](#localmente)
- [ENTREGA 2: Nuevos Endpoints](#entrega-2-nuevos-endpoints)
- [ENTREGA 1: Endpoints iniciales](#entrega-1-endpoints-iniciales)

---

## **EJECUTAR EL PROYECTO**
Para ejecutar el proyecto, simplemente utiliza la URL base adecuada según el entorno.  
En cada endpoint, sustituye {base_url} por la URL del entorno deseado.

### EN LÍNEA
```bash
{base_url} = https://heropass-deploy-fe0660f60d3c.herokuapp.com
```

### LOCALMENTE (Docker + Lumen)
Utilizamos Docker para levantar el servidor PHP y la base de datos MySQL ya configurada.  
Pasos para levantar el entorno:

```bash
# Situarse en la raíz del proyecto Lumen
cd heropass-lumen

# Instalar dependencias PHP
composer install

# Construir e iniciar el entorno completo
docker-compose up --build
````
De esta manera se iniciará el servidor en local, creará automáticamente la base de datos lumen con los datos necesarios y generará el archivo .env si no existe.

```bash
{base_url} = http://localhost:8000
```

---

## **ENTREGA 2: Nuevos Endpoints**

### **CASO DE USO 1: REGISTRO DE USUARIOS**
Este endpoint permite registrar un nuevo usuario y obtener una API Key única.
```bash
POST {base_url}/register
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
POST {base_url}/token
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
Este endpoint requiere autenticación mediante un token válido. El token debe ser enviado en el encabezado `Authorization: Bearer {generated_token}` en cada petición.
Este endpoint proporcionará información sobre los 40 videos más visualizados de cada uno de los tres juegos más populares en Twitch.

Ejemplo de petición autenticada:
```bash
GET /analytics/topsofthetops
Host: {base_url}
Authorization: Bearer generated_token
```

Este endpoint permite el parámetro since con el cual se fuerza a la actualización de la base de datos. Se indica en segundos
Ejemplo de petición autenticada con el parámetro sice:
```bash
GET /analytics/topsofthetops?since=300
Host: {base_url}
Authorization: Bearer generated_token
```

---

## **ENTREGA 1: Endpoints iniciales**

### **CASO DE USO 1: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH**  
Este endpoint permite consultar la información de un streamer de Twitch mediante su ID.  
Ejemplo con el id de usuario de Ibai: `83232866`
```bash
GET /analytics/user?id=83232866
Host: {base_url}
Authorization: Bearer generated_token
```

### **CASO DE USO 2: CONSULTAR STREAMS EN VIVO**  
Este endpoint obtiene una lista de los 10 streams con más espectadores que están actualmente en vivo en Twitch.
```bash
GET /analytics/streams
Host: {base_url}
Authorization: Bearer generated_token
```

### **CASO DE USO 3: CONSULTAR “TOP STREAMS ENRIQUECIDOS”**  
Este caso de uso realiza un filtrado y enriquecimiento del listado de Streams en Vivo.
1. Obtener la lista de streams en vivo de Twitch (los 10 primeros).
2. Ordenar los streams por número de espectadores (ascendente).
3. Seleccionar los primeros N (`limit`: de 1 hasta 10).
4. Enriquecer cada stream con información adicional del usuario (`display_name` o `profile_image_url`).
5. Devolver al cliente un listado combinado.

```bash
GET /analytics/streams/enriched?limit=3
Host: {base_url}
Authorization: Bearer generated_token
```
