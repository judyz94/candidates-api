# API de Gestión de Candidatos
Esta API proporciona funcionalidades para la gestión de candidatos, permitiendo realizar operaciones como la creación, actualización, eliminación y consulta de candidatos.

## Características principales

- **Autenticación basada en tokens JWT:** Los usuarios deben autenticarse para acceder a los recursos de la API. Se utiliza el estándar JWT para generar y validar los tokens de autenticación.

- **Gestión de roles de usuario:** Se definen roles de usuario como "manager" y "owner", lo que permite restringir el acceso a ciertas operaciones según el rol del usuario autenticado.

- **Caché de datos:** Se utiliza caché y Redis como el controlador de caché predeterminado, para mejorar el rendimiento de las consultas a la base de datos. Algunas consultas frecuentes se almacenan en caché para evitar consultas repetitivas.

## Endpoints disponibles
- POST /auth: Genera un token de autenticación.
- POST /lead: Crea un nuevo candidato.
- GET /lead/{id}: Obtiene los detalles de un candidato específico.
- GET /leads: Obtiene todos los candidatos asignados al agente o, si el usuario es un "manager", devuelve todos los candidatos.

## Requisitos
- PHP 8.1
- Laravel 9
- Base de datos MySQL

## Configuración
1. Clonar el repositorio.
2. Ejecutar composer install para instalar las dependencias.
3. Copiar el archivo .env.example a .env y configurar la conexión a la base de datos (cp .env.example .env).
4. Generar una clave de aplicación ejecutando php artisan key:generate.
5. Ejecutar php artisan migrate para crear las tablas en la base de datos.
6. Ejecutar php artisan db:seed para poblar la base de datos con datos de prueba.
7. Iniciar el servidor de desarrollo ejecutando php artisan serve.
