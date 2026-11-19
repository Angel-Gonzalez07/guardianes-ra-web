# Guardianes RA: instalación local

## Requisitos
- PHP 8.3 o superior, con las extensiones pdo_pgsql, pgsql, mbstring, openssl, fileinfo y curl activas
- Composer
- PostgreSQL
- Git

## Pasos
1. Clona el repositorio y entra a la carpeta:
   ```
   git clone https://github.com/Angel-Gonzalez07/guardianes-ra-web.git
   cd guardianes-ra-web
   ```
2. Instala las dependencias:
   ```
   composer install
   ```
3. Crea el archivo de configuración y la llave:
   ```
   copy .env.example .env
   php artisan key:generate
   ```
4. En `.env` escribe tu contraseña de PostgreSQL en `DB_PASSWORD=`.
5. Crea la base de datos y carga los datos del respaldo:
   ```
   psql -U postgres -c "CREATE DATABASE guardianes_ra_web;"
   psql -U postgres -d guardianes_ra_web -f database/guardianes_ra_web.sql
   ```
6. Levanta el servidor:
   ```
   php artisan serve
   ```
7. Abre http://localhost:8000

## Cuentas de prueba
- Administrador: caejaestudio@utcv.edu.mx / Admin12345

## Si algo falla
- «could not find driver»: activa `pdo_pgsql` y `pgsql` en `php.ini`.
- No encuentra `psql`: usa la ruta completa, por ejemplo `C:\Program Files\PostgreSQL\17\bin\psql.exe`.
- Alternativa sin el respaldo: `php artisan migrate --seed` (sin las imágenes de los productos).