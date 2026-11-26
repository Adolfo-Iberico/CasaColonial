# Casa Colonial

Sistema de gestión administrativa desarrollado con Laravel para centralizar las operaciones de un restaurante: clientes, proveedores, compras, mesas y órdenes, con usuarios y permisos según el puesto.

## Funcionalidades

- Gestión de clientes y proveedores.
- Registro de compras y sus detalles.
- Administración de mesas y órdenes.
- Exportación de información a PDF.
- Autenticación de usuarios y gestión de perfil, contraseña y apariencia.
- Roles para administración, caja/barra, mozo, cocina, panadería, limpieza y reparto.
- Permisos aplicados en rutas, navegación y acciones de cada módulo.
- Registro público deshabilitado: las cuentas las crea un administrador desde Gestión de usuarios.
- MVP operativo de POS/caja, comandas de salón y pantalla KDS para barra y cocina.
- Pagos en efectivo, Yape, Plin, tarjeta y transferencia; permite registrar abonos de distintos medios.
- Apertura y cierre de caja por usuario, con efectivo esperado, contado y diferencia.
- Carta de productos con precio y estación de preparación; cada venta guarda el precio y nombre aplicados.
- Estados de mesa derivados de las órdenes: libre, ocupada, por cobrar o fuera de servicio.

## Tecnologías

- PHP 8.2+ y Laravel 12
- Livewire 3, Livewire Volt y Flux UI
- Tailwind CSS 4 y Vite
- SQLite por defecto (o cualquier base de datos compatible con Laravel)
- DomPDF para generar PDF

## Requisitos

- PHP 8.2 o superior con las extensiones habituales de Laravel
- Composer
- Node.js y npm

## Instalación

1. Clona el repositorio y entra en la carpeta:

   ```bash
   git clone https://github.com/Adolfo-Iberico/CasaColonial.git
   cd CasaColonial
   ```

2. Instala las dependencias:

   ```bash
   composer install
   npm install
   ```

3. Crea el archivo de entorno y genera la clave de la aplicación:

   - Windows (PowerShell):

     ```powershell
     Copy-Item .env.example .env
     ```

   - Linux / macOS:

     ```bash
     cp .env.example .env
     ```

   ```bash
   php artisan key:generate
   ```

4. Prepara la base de datos SQLite:

   - Windows (PowerShell):

     ```powershell
     New-Item -ItemType File -Path database\database.sqlite -Force
     ```

   - Linux / macOS:

     ```bash
     touch database/database.sqlite
     ```

   ```bash
   php artisan migrate
   ```

## Ejecución

```bash
composer run dev
```

Inicia el servidor de Laravel, la cola y Vite. La aplicación queda disponible en `http://127.0.0.1:8000`.

También puedes ejecutar los procesos por separado:

```bash
php artisan serve
npm run dev
```

## Acceso

Las cuentas nuevas se crean desde `/admin/users`, disponible solo para administradores. Desde el mismo gestor se pueden reasignar puestos a cuentas existentes; el sistema sincroniza el acceso de administrador con el puesto asignado.

| Puesto | Acceso actual |
| --- | --- |
| Administrador | Todos los módulos y la gestión de usuarios |
| Cajero / Barra | POS, apertura/cierre de caja, cobros, comandas, mesas y dashboard financiero |
| Mozo | Comandas, toma de pedidos, mapa de mesas y actualización de entrega |
| Barista | KDS de barra |
| Cocinero / Ayudante de cocina | KDS de cocina |
| Panadero / Pastelero, repartidor y limpieza | Dashboard; las funciones específicas de producción, delivery y checklists quedan para fases posteriores |

Compras, proveedores y sus detalles siguen siendo módulos administrativos. Antes de vender, la administración debe cargar los productos, precios y estaciones en `/admin/menu`. El KDS separa automáticamente los pedidos de barra y cocina y se actualiza cada cinco segundos; avisa visualmente cuando un producto lleva diez minutos o más esperando.

El POS puede registrar si corresponde boleta o factura y guardar el RUC indicado, pero **todavía no emite comprobantes ni se conecta con SUNAT**. La integración requiere escoger un proveedor de facturación y configurar credenciales. Tampoco se incluyen aún inventario, recetas, producción, delivery, asistencia ni checklists.

Para crear el primer administrador de forma interactiva, ejecuta:

```bash
php artisan app:create-admin
```

El comando solicita correo, nombre y contraseña; también permite promover a administrador una cuenta existente. No guarda credenciales en el código ni utiliza `DatabaseSeeder`.

Para insertar datos aleatorios de prueba en las tablas de usuarios, clientes, proveedores, compras, detalles, mesas y órdenes, ejecuta:

```bash
php artisan db:seed --class="Database\Seeders\RandomDataSeeder"
```

Este comando es independiente de `DatabaseSeeder` y agrega nuevos registros sin borrar los existentes.

## Últimas modificaciones

- Las rutas y acciones administrativas se protegen según los permisos del puesto.
- El dashboard oculta datos financieros para los puestos que no manejan caja.
- La gestión de usuarios en `/admin/users` solo está disponible para administradores y permite asignar puestos.
- El registro público está deshabilitado.
- El entorno local tiene 8 productos de demostración (4 de cocina y 4 de barra) y 3 comandas abiertas para validar carta, mesas y KDS. Estos registros se insertaron directamente en la base configurada; no se generan automáticamente ni se usa `DatabaseSeeder`.
- Los pedidos con cobros no pueden editarse ni borrarse desde el módulo administrativo para preservar el historial de pagos.

## Rutas principales

Las rutas administrativas requieren autenticación y están bajo `/admin`:

| Ruta               | Módulo             |
| ------------------ | ------------------ |
| `/admin/customers` | Clientes           |
| `/admin/proveedor` | Proveedores        |
| `/admin/compra`    | Compras            |
| `/admin/detalle`   | Detalle de compras |
| `/admin/mesas`     | Mesas              |
| `/admin/menu`      | Carta de productos (administradores) |
| `/admin/orden`     | Órdenes            |
| `/admin/users`    | Gestión de usuarios (administradores) |
| `/pos`             | POS y caja (cajero/administrador) |
| `/comandas`        | Salón y comandas (mozo/cajero/administrador) |
| `/kds`              | Pantalla de barra/cocina |

Los PDF se generan desde cada sección correspondiente.

## Pruebas

```bash
php artisan test
```
