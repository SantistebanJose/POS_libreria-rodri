## Requisitos
Asegúrate de que las siguientes extensiones de PHP estén instaladas en tu entorno de desarrollo:

- `php-pgsql`: Para conectarse a bases de datos PostgreSQL.

## Configuración del archivo .env

Para configurar las variables de entorno necesarias para el proyecto, debes crear un archivo `.env` en la raíz del proyecto. Este archivo contendrá las configuraciones sensibles como las credenciales de la base de datos. Asegúrate de no compartir este archivo públicamente ni subirlo a tu repositorio de control de versiones.

### Ejemplo de archivo .env

Crea un archivo `.env` en la raíz del proyecto con el siguiente contenido:

```properties
HOST="localhost"
DB_NAME="nombre_base_de_datos"
DB_USER="usuario"
PASSWORD="contraseña"
