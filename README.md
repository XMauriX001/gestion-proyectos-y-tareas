# Gestión de Proyectos y Tareas API 🚀

Este proyecto es una API robusta construida con **Laravel 13** para la gestión ágil de proyectos, tareas y sprints. Incluye funcionalidades avanzadas como el cierre automático de sprints, gestión de roles y permisos, e historiales de cambios.

---

## ✨ Características Principales

- **Gestión de Proyectos**: Creación, seguimiento e historial de auditoría de proyectos.
- **Ciclo de Vida de Sprints**: Planificación de sprints, seguimiento de fechas límite y procesos de cierre automatizados con resúmenes ejecutivos.
- **Gestión de Tareas**: Asignación de tareas, control de estados (Pendiente, En Progreso, Completada, etc.) y prioridades.
- **Seguridad y Roles (RBAC)**: Integración con `spatie/laravel-permission` para manejar roles de **Product Owner**, **Project Manager** y **Team Member**.
- **Documentación Automática**: Documentación de API generada dinámicamente con **Scramble**.
- **Autenticación**: Segura mediante **Laravel Sanctum**.

---

## 🛠️ Requisitos del Sistema

- **PHP**: ^8.3
- **Composer**
- **Node.js & NPM**
- **Base de Datos**: SQLite (configurada por defecto) o MySQL/PostgreSQL.

---

## 🚀 Instalación y Configuración

Sigue estos pasos para levantar el entorno de desarrollo localmente:

### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio>
cd gestion-proyectos-y-tareas
```

### 2. Configuración Rápida (Recomendada)
El proyecto incluye un script optimizado para realizar toda la instalación básica (dependencias, base de datos y assets) en un solo comando:

```bash
composer setup
```

### 3. Configuración Manual (Opcional)
Si prefieres realizar los pasos uno por uno, ejecuta:

```bash
# Instalar dependencias de PHP
composer install

# Crear archivo de entorno
cp .env.example .env

# Generar llave de la aplicación
php artisan key:generate

# Crear base de datos SQLite (si se usa la config por defecto)
touch database/database.sqlite

# Correr migraciones y seeders
php artisan migrate --seed

# Instalar y compilar dependencias de Frontend
npm install
npm run build
```

---

## ⚙️ Ejecución del Servidor

Para iniciar el servidor de desarrollo, la escucha de colas y el proceso de Vite de forma simultánea, utiliza:

```bash
npm run dev
```

Esto ejecutará:
- **API Server** en `http://localhost:8000`
- **Vite** para el manejo de assets.
- **Queue Worker** para procesos en segundo plano.

---

## 📚 Documentación de la API

Una vez que el servidor esté corriendo, puedes acceder a la documentación interactiva de la API en la siguiente ruta:

👉 [http://localhost:8000/docs/api](http://localhost:8000/docs/api)

---

## 👥 Usuarios de Prueba

El sistema viene con usuarios preconfigurados mediante el seeder (`php artisan db:seed`):

| Rol | Email 
| :--- | :--- 
| **Product Owner** | `po@example.com` 
| **Project Manager** | `pm@example.com` 
| **Developer** | `dev1@example.com` 

---

## 🧪 Pruebas

Para ejecutar la suite de pruebas automatizadas:

```bash
php artisan test
```

---

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 13
- **Autenticación**: Laravel Sanctum
- **Roles**: Spatie Laravel Permission
- **Documentación**: Scramble
- **Testing**: Pest PHP
- **Frontend/Assets**: Vite & Tailwind CSS
