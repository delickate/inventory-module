# 📦 Inventory Module for Laravel

A **plug-and-play Inventory Management Module** for Laravel modular applications using `delickate/module-generator`.

This package installs a **fully developed Inventory module** into your Laravel application's `Modules` directory.

It is designed for **ERP, POS, and stock management systems**.

---

# ✨ Features

- 📦 Product Management
- 🏬 Warehouse Management
- 📊 Stock Tracking
- 🔄 Stock Movement History
- 📥 Purchase Stock Entries
- 📤 Sales Stock Deduction
- 🧾 Inventory Reports
- 🏷 Category & Brand Support
- ⚡ Modular Architecture
- 🔌 Easy Laravel Integration

---

# 📂 Module Structure

After installation, the module will be placed inside:

```
Modules/Inventory
```

Structure example:

```
Modules/
 └── Inventory
     ├── Config
     ├── Database
     │   ├── Migrations
     │   └── Seeders
     │
     ├── Entities
     │
     ├── Http
     │   ├── Controllers
     │   └── Requests
     │
     ├── Providers
     │
     ├── Routes
     │
     ├── Resources
     │   └── views
     │
     └── module.json
```

---

# ⚙️ Requirements

- PHP **8.1+**
- Laravel **10 / 11**
- Installed `delickate/module-generator`

---

# 📥 Installation

Install the package via Composer.

```
composer require delickate/inventory-module
```

---

# 🚀 Module Installation

Run the installer command:

```
php artisan inventory:install
```

This command will:

- Publish the Inventory module
- Installed it to:

```
Modules/Inventory
```

---

# 🗃 Run Migrations

After installing the module, run migrations.

```
php artisan module:migrate Inventory
```

Or run all module migrations:

```
php artisan module:migrate
```

---


# 📊 Core Components

## Products

Manage products with:

- SKU
- Category
- Brand
- Cost Price
- Selling Price
- Stock Quantity

## Warehouses

Supports:

- Multiple warehouses
- Warehouse stock tracking

## Stock Movements

Tracks:

- Stock In
- Stock Out
- Transfers
- Adjustments

---

# 🔧 Customization

Since the module is installed into your project, you can modify it directly.

Example:

```
Modules/Inventory/Http/Controllers
Modules/Inventory/Entities
Modules/Inventory/Routes
```

---

# 🛠 Artisan Commands

Install module

```
php artisan inventory:install
```

Run module migrations

```
php artisan module:migrate Inventory
```

---

# 🤝 Contributing

Contributions are welcome.

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Open a Pull Request

---

# 📄 License

This package is open-sourced software licensed under the **MIT license**.

---

# 🏢 Maintained By

Developed and maintained by **Delickate**.