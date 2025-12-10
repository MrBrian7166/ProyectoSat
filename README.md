PROYECTO: Sistema de Opinión de Cumplimiento SAT
Autor: Brian Diaz Carrillo
Fecha: 10/12/2025

========================================
1. DESCRIPCIÓN GENERAL
========================================
Esta aplicación web permite gestionar clientes y simular la consulta de la Opinión de Cumplimiento del SAT.

El flujo principal:
- El usuario añade un cliente atravez del sistema
- El usuario selecciona un cliente.
- Se simula una "consulta" al SAT a partir de un PDF de la opinión ya descargado manualmente.
- El sistema analiza el PDF, detecta si el SENTIDO es POSITIVO o NEGATIVO y guarda el resultado en la base de datos.
- Se muestra el resultado en pantalla y se puede ver el PDF asociado.

La aplicación está desarrollada con el framework Laravel (PHP) siguiendo el patrón MVC.

========================================
2. TECNOLOGÍAS UTILIZADAS
========================================
Backend:
- PHP 8.x
- Framework Laravel [versión que usas]

Frontend:
- Blade (motor de plantillas de Laravel)
- Bootstrap

Base de Datos:
- MySQL

Librerías adicionales:
- smalot/pdfparser (para leer texto de los PDFs de Opinión)

========================================
3. PROCEDIMIENTO DE INSTALACIÓN
========================================
Requisitos previos:
- PHP >= 8.0
- Composer
- MySQL
- Node.js y npm (si se usan assets con Laravel Mix o Vite)

Pasos:

1) Clonar o descomprimir el proyecto en el servidor local:
   - Carpeta base: C:\laragon\www\ProyectoSAT

2) Instalar dependencias de PHP:
   - Desde la raíz del proyecto:
     composer install

3) Copiar el archivo de entorno:
   - cp .env.example .env

4) Configurar el archivo .env:
   - DB_DATABASE=bd_proyectosat
   - DB_USERNAME=[tu_usuario_mysql]
   - DB_PASSWORD=[tu_contraseña_mysql]
   - APP_URL=http://localhost:8000

5) Generar la key de la aplicación:
   - php artisan key:generate

6) Importar la base de datos:
   - Crear la base de datos "bd_proyectosat" en MySQL.
   - Importar el archivo base_datos_proyectosat.sql incluido en este proyecto.

7) Ejecutar migraciones (si es necesario):
   - php artisan migrate

8) Crear carpeta de opiniones de SAT (si no existe):
   - storage/app/opiniones_sat/

9) Colocar manualmente los PDFs de Opinión dentro de storage/app/opiniones_sat/
   - Ejemplo:
     storage/app/opiniones_sat/opinion_AACA530204RN6_2025-12-10.pdf
     storage/app/opiniones_sat/opinion_AALA790124DC3_2025-12-10.pdf

10) Levantar el servidor de desarrollo:
   - php artisan serve
   - URL: http://localhost:8000

========================================
4. CREDENCIALES DE ACCESO
========================================
Usuario por defecto (ejemplo):
- Email: admin@example.com
- Contraseña: 123456

Rol:
- Administrador: puede gestionar clientes y ejecutar el proceso de simulación de opinión.

========================================
5. FUNCIONAMIENTO BÁSICO
========================================
1) Agregar un cliente con las credenciales indicadas.
2) Ir al menú "Proceso SAT" o a la ruta /proceso.
3) Se mostrará la lista de clientes.
4) Cada cliente tiene un botón "Resultado Opinión":
   - El sistema arma la ruta del PDF: opiniones_sat/opinion_<RFC>_2025-12-10.pdf
   - Se analiza el PDF:
       * Si contiene la palabra NEGATIVO, se guarda y muestra como NEGATIVO.
       * Si contiene la palabra POSITIVO, se guarda y muestra como POSITIVO.
5) Se muestra el resultado en la parte superior:
   - En VERDE si es POSITIVO.
   - En ROJO si es NEGATIVO.
6) Se puede ver el PDF original con el botón "Ver PDF".

========================================
6. ARQUITECTURA Y ESTRUCTURA DEL PROYECTO
========================================
Patrón de diseño: MVC de Laravel.

Carpetas principales:
- app/Models
  - Cliente.php
  - ResultadoSat.php
- app/Http/Controllers
  - ProcesoController.php
- app/Services
  - SatService.php   (contiene la lógica para analizar PDFs de opinión)
- resources/views
  - proceso/index.blade.php  (vista principal del flujo SAT)

- database/migrations
  - create_clientes_table.php
  - create_resultado_sats_table.php

========================================
7. URL DE DESCARGA (SI EL PROYECTO > 5MB)
========================================
En caso de que el archivo supere el límite permitido por Moodle, se incluye el proyecto en:
- URL: (https://github.com/MrBrian7166/ProyectoSat.git)

========================================
8. NOTAS ADICIONALES
========================================
- La consulta al SAT está simulada. El sistema no se conecta directamente al portal del SAT, sino que analiza un PDF que el usuario descarga manualmente desde el portal oficial.
- Esto reduce la complejidad y evita problemas técnicos y legales con la automatización del sitio externo.
