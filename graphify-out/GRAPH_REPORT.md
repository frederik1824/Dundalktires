# Graph Report - Dunkdalk  (2026-09-25)

## Corpus Check
- 141 files · ~68,718 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 21 file(s) not represented in the graph (top: (none) 16, .example 1, .xml 1)

## Summary
- 828 nodes · 1395 edges · 91 communities (50 shown, 41 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 5 edges (avg confidence: 0.89)
- Token cost: 1,200 input · 800 output

## Community Hubs (Navigation)
- Admin & Settings Controllers
- Inventory & Product Controller
- Frontend Dependencies
- Expense Tracking
- Purchase Order Management
- Services Catalog
- Customer & Vehicle Management
- Point of Sale UI
- Employee Management
- Dashboard & Analytics
- Purchase Order Detail
- Work Order Builder
- Base Controllers
- Core Domain Models
- Employee UI
- Customer Detail UI
- Customer Forms
- Vue UI Components
- Product Detail View
- Work Order Domain Model
- Invoice Controller
- Purchase Order Form
- Invoice Model
- Config & Database Setup
- App Bootstrap & i18n
- Expense List UI
- Product Inventory List
- Purchase Orders List
- Reports Controller
- App Layout & Navigation
- Authentication
- Dashboard Controller
- Composer Package Config
- Composer Scripts
- Invoice Detail UI
- Services List UI
- Customer Controller
- Dev Dependencies
- Suppliers List UI
- Composer Plugin Config
- Composer Requirements
- Branch & Work Order Migrations
- Purchase & Service Migrations
- WO Products & Invoice Items Migrations
- Expense Form UI
- Product Form UI
- Settings UI
- App Service Provider
- Feature Tests
- Stock Badge Component
- Supplier Form UI
- Vehicle Form UI
- Work Order Detail UI
- AI Agent Documentation
- Composer Autoload
- Logging Config
- Unit Tests
- Artisan Console
- Test Autoload Config
- Composer Extras
- Brand Identity
- Artisan Routes
- Robots Config

## God Nodes (most connected - your core abstractions)
1. `Product` - 37 edges
2. `@inertiajs/vue3` - 33 edges
3. `WorkOrder` - 30 edges
4. `vue-i18n` - 30 edges
5. `Controller` - 28 edges
6. `vue` - 28 edges
7. `Customer` - 27 edges
8. `User` - 25 edges
9. `Invoice` - 24 edges
10. `PurchaseOrder` - 22 edges

## Surprising Connections (you probably didn't know these)
- `AGENTS.md - Laravel Agent Setup Guide` --semantically_similar_to--> `CLAUDE.md - Laravel Agent Setup Guide`  [INFERRED] [semantically similar]
  AGENTS.md → CLAUDE.md
- `AGENTS.md - Laravel Agent Setup Guide` --references--> `Laravel - PHP Web Application Framework`  [EXTRACTED]
  AGENTS.md → README.md
- `CLAUDE.md - Laravel Agent Setup Guide` --references--> `Laravel Boost - AI Agent Development Package`  [EXTRACTED]
  CLAUDE.md → AGENTS.md
- `README.md - Laravel Framework Documentation` --references--> `Laravel Boost - AI Agent Development Package`  [EXTRACTED]
  README.md → AGENTS.md
- `Logo Dundalk.png - Dundalk Tires Brand Logo (dark background)` --semantically_similar_to--> `logo.png - Dundalk Tires Brand Logo (white background)`  [INFERRED] [semantically similar]
  public/Logo Dundalk.png → public/logo.png

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Laravel AI Agent Development Setup Flow** — agents_md, claude_md, laravel_boost [EXTRACTED 1.00]
- **Dundalk Tires Brand Identity Assets** — public_logo_dundalk_png, public_logo_png, dundalk_tires_business [INFERRED 0.95]
- **Laravel Project Documentation Ecosystem** — agents_md, claude_md, readme_md [INFERRED 0.85]

## Communities (91 total, 41 thin omitted)

### Community 0 - "Admin & Settings Controllers"
Cohesion: 0.05
Nodes (18): SettingsController, WorkOrderController, Setting, User, WorkOrderProduct, WorkOrderService, Carbon\Carbon, Collection (+10 more)

### Community 1 - "Inventory & Product Controller"
Cohesion: 0.07
Nodes (6): ProductController, Product, ProductCategory, ProductMovement, InventoryService, Inertia\Inertia

### Community 2 - "Frontend Dependencies"
Cohesion: 0.08
Nodes (27): dependencies, chart.js, @inertiajs/vue3, @vitejs/plugin-vue, vue, vue-i18n, devDependencies, concurrently (+19 more)

### Community 3 - "Expense Tracking"
Cohesion: 0.10
Nodes (8): ExpenseController, ExpenseCategory, DatabaseSeeder, ExpenseCategorySeeder, ProductCategorySeeder, ServiceCategorySeeder, Illuminate\Database\Seeder, Illuminate\Support\Facades\Storage

### Community 4 - "Purchase Order Management"
Cohesion: 0.10
Nodes (3): PurchaseOrderController, PurchaseOrder, PurchaseOrderItem

### Community 5 - "Services Catalog"
Cohesion: 0.11
Nodes (4): ServiceController, Service, ServiceCategory, ServicePriceRule

### Community 6 - "Customer & Vehicle Management"
Cohesion: 0.15
Nodes (3): VehicleController, Customer, Vehicle

### Community 7 - "Point of Sale UI"
Cohesion: 0.10
Nodes (12): amountPaid, balance, form, itemResults, itemSearch, paymentMethod, paymentRef, props (+4 more)

### Community 8 - "Employee Management"
Cohesion: 0.15
Nodes (7): EmployeeController, HandleInertiaRequests, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Http\Request, Inertia\Middleware

### Community 9 - "Dashboard & Analytics"
Cohesion: 0.12
Nodes (13): chart.js, chartRef, kpis, props, statusColors, { t }, chartRef, from (+5 more)

### Community 10 - "Purchase Order Detail"
Cohesion: 0.11
Nodes (8): fileInput, fileSelected, orderTotal, props, receiveForm, showReceive, { t }, uploading

### Community 11 - "Work Order Builder"
Cohesion: 0.12
Nodes (9): customerVehicles, form, productResults, productSearch, props, serviceResults, serviceSearch, subtotal (+1 more)

### Community 12 - "Base Controllers"
Cohesion: 0.17
Nodes (3): Controller, SupplierController, Supplier

### Community 13 - "Core Domain Models"
Cohesion: 0.23
Nodes (4): Branch, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes

### Community 14 - "Employee UI"
Cohesion: 0.13
Nodes (9): ref_components, props, q, { t }, props, q, status, statusColors (+1 more)

### Community 15 - "Customer Detail UI"
Cohesion: 0.13
Nodes (9): @inertiajs/vue3, props, { t }, vehicleTypeLabel, columns, grouped, props, { t } (+1 more)

### Community 16 - "Customer Forms"
Cohesion: 0.13
Nodes (10): ref_layouts, form, props, { t }, props, search, { t }, form (+2 more)

### Community 17 - "Vue UI Components"
Cohesion: 0.13
Nodes (10): vue, message, page, type, visible, form, form, props (+2 more)

### Community 18 - "Product Detail View"
Cohesion: 0.14
Nodes (9): adjustNotes, adjustQty, margin, productName, props, showAdjust, stockColor, { t } (+1 more)

### Community 21 - "Purchase Order Form"
Cohesion: 0.17
Nodes (7): form, orderTotal, productQuery, productResults, props, showProductSearch, { t }

### Community 23 - "Config & Database Setup"
Cohesion: 0.22
Nodes (5): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Str, Pdo\Mysql, static

### Community 24 - "App Bootstrap & i18n"
Cohesion: 0.20
Nodes (4): ref_vendor_tightenco_ziggy, vue-i18n, resources_css_app, i18n

### Community 25 - "Expense List UI"
Cohesion: 0.18
Nodes (8): category, from, methodIcons, props, q, { t }, to, type

### Community 26 - "Product Inventory List"
Cohesion: 0.20
Nodes (8): applyFilters(), categoryId, debouncedSearch(), lowStock, props, search, supplierId, { t }

### Community 27 - "Purchase Orders List"
Cohesion: 0.20
Nodes (7): applyFilters(), debouncedSearch(), props, search, status, supplierId, { t }

### Community 28 - "Reports Controller"
Cohesion: 0.24
Nodes (3): ReportController, InvoiceItem, Illuminate\Support\Facades\DB

### Community 29 - "App Layout & Navigation"
Cohesion: 0.20
Nodes (7): ref_i18n, NavIcon, navigation, page, sidebarOpen, { t, locale }, user

### Community 30 - "Authentication"
Cohesion: 0.22
Nodes (4): AuthenticatedSessionController, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Route, Illuminate\Validation\ValidationException

### Community 32 - "Composer Package Config"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 33 - "Composer Scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 34 - "Invoice Detail UI"
Cohesion: 0.22
Nodes (6): balance, payForm, props, showPayModal, statusColors, { t }

### Community 35 - "Services List UI"
Cohesion: 0.22
Nodes (4): category, props, q, { t }

### Community 37 - "Dev Dependencies"
Cohesion: 0.25
Nodes (8): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, phpunit/phpunit

### Community 38 - "Suppliers List UI"
Cohesion: 0.29
Nodes (6): activeOnly, applyFilters(), debouncedSearch(), props, search, { t }

### Community 39 - "Composer Plugin Config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 40 - "Composer Requirements"
Cohesion: 0.29
Nodes (7): require, inertiajs/inertia-laravel, laravel/framework, laravel/tinker, php, spatie/laravel-permission, tightenco/ziggy

### Community 44 - "Expense Form UI"
Cohesion: 0.29
Nodes (4): evidencePreview, form, props, { t }

### Community 45 - "Product Form UI"
Cohesion: 0.29
Nodes (5): form, isEdit, props, { t }, tireSizePreview

### Community 46 - "Settings UI"
Cohesion: 0.29
Nodes (4): form, groups, props, { t }

### Community 48 - "Feature Tests"
Cohesion: 0.47
Nodes (3): Illuminate\Foundation\Testing\TestCase, ExampleTest, TestCase

### Community 49 - "Stock Badge Component"
Cohesion: 0.33
Nodes (5): badgeClass, isLow, isOut, label, props

### Community 50 - "Supplier Form UI"
Cohesion: 0.33
Nodes (4): form, isEdit, props, { t }

### Community 51 - "Vehicle Form UI"
Cohesion: 0.33
Nodes (4): currentYear, form, props, { t }

### Community 52 - "Work Order Detail UI"
Cohesion: 0.33
Nodes (3): props, statusColors, { t }

### Community 53 - "AI Agent Documentation"
Cohesion: 0.70
Nodes (5): AGENTS.md - Laravel Agent Setup Guide, CLAUDE.md - Laravel Agent Setup Guide, Laravel Boost - AI Agent Development Package, Laravel - PHP Web Application Framework, README.md - Laravel Framework Documentation

### Community 54 - "Composer Autoload"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 55 - "Logging Config"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 58 - "Test Autoload Config"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 59 - "Composer Extras"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 80 - "Brand Identity"
Cohesion: 1.00
Nodes (3): Dundalk Used & New Tires - Business Entity, Logo Dundalk.png - Dundalk Tires Brand Logo (dark background), logo.png - Dundalk Tires Brand Logo (white background)

## Knowledge Gaps
- **218 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+213 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 486 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **41 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `@inertiajs/vue3` connect `Customer Detail UI` to `Frontend Dependencies`, `Point of Sale UI`, `Dashboard & Analytics`, `Purchase Order Detail`, `Work Order Builder`, `Employee UI`, `Customer Forms`, `Vue UI Components`, `Product Detail View`, `Purchase Order Form`, `App Bootstrap & i18n`, `Expense List UI`, `Product Inventory List`, `Purchase Orders List`, `App Layout & Navigation`, `Invoice Detail UI`, `Services List UI`, `Suppliers List UI`, `Expense Form UI`, `Product Form UI`, `Settings UI`, `Supplier Form UI`, `Vehicle Form UI`, `Work Order Detail UI`?**
  _High betweenness centrality (0.039) - this node is a cross-community bridge._
- **Why does `vue-i18n` connect `App Bootstrap & i18n` to `Frontend Dependencies`, `Point of Sale UI`, `Dashboard & Analytics`, `Purchase Order Detail`, `Work Order Builder`, `Employee UI`, `Customer Detail UI`, `Customer Forms`, `Vue UI Components`, `Product Detail View`, `Purchase Order Form`, `Expense List UI`, `Product Inventory List`, `Purchase Orders List`, `App Layout & Navigation`, `Invoice Detail UI`, `Services List UI`, `Suppliers List UI`, `Expense Form UI`, `Product Form UI`, `Settings UI`, `Supplier Form UI`, `Vehicle Form UI`, `Work Order Detail UI`?**
  _High betweenness centrality (0.037) - this node is a cross-community bridge._
- **Why does `vue` connect `Vue UI Components` to `Frontend Dependencies`, `Point of Sale UI`, `Dashboard & Analytics`, `Purchase Order Detail`, `Work Order Builder`, `Employee UI`, `Customer Detail UI`, `Customer Forms`, `Product Detail View`, `Purchase Order Form`, `App Bootstrap & i18n`, `Expense List UI`, `Product Inventory List`, `Purchase Orders List`, `App Layout & Navigation`, `Invoice Detail UI`, `Services List UI`, `Suppliers List UI`, `Expense Form UI`, `Product Form UI`, `Stock Badge Component`, `Supplier Form UI`, `Work Order Detail UI`?**
  _High betweenness centrality (0.035) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _218 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Admin & Settings Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.05407925407925408 - nodes in this community are weakly interconnected._
- **Should `Inventory & Product Controller` be split into smaller, more focused modules?**
  _Cohesion score 0.07293868921775898 - nodes in this community are weakly interconnected._
- **Should `Frontend Dependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.07635467980295567 - nodes in this community are weakly interconnected._