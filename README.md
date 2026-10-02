<div align="left" dir="ltr">

# Royal-Weave 🧵✨

### 📝 Project Overview
**Royal-Weave** is an elegant, modern e-commerce website specifically designed for selling premium fabrics and high-quality traditional Shemaghs. The platform aims to deliver a seamless shopping experience, allowing customers to browse through exclusive collections and buy their favorite products with ease.

---

### 📊 System Architecture & Comprehensive Component Descriptions

#### 1. 🗺️ ER Diagram (Entity-Relationship Layout)
*The architecture driving the database engine of Royal-Weave.*

<img width="1600" height="1042" alt="er_diagram" src="https://github.com/user-attachments/assets/a7dcda15-c4a2-45f4-ad91-db5c8383ed8c" />


* **🔍 Comprehensive Database Architecture Analysis:**
  This blueprint design reveals a highly scalable and normalized relational database schema carefully tailored for an enterprise-level e-commerce application. The architecture effectively decouples core business domains into distinct entities to maximize transactional integrity and query optimization:
  
  * **Core Product & Inventory Subsystem (`product`, `product_variant`, `category`):** The schema implements a robust Product-Variant catalog structure. The base `product` table contains shared attributes (name, description, main image), while the `product_variant` table isolates fluctuating inventory characteristics (price, stock quantity, size, color, origin country, weave type, and season parameters). This eliminates data redundancy and seamlessly supports textile variations such as specific fabric dimensions or unique traditional Shemagh sizes. The self-referencing hierarchy in the `category` table via `parent_category_id` facilitates endless multi-level menu nested structures.
  
  * **Customer & Session Tracking Subsystem (`customer`, `user_session`, `activity_log`):** User management is guarded with strict separation of roles. The `customer` entity safely handles authentication data via encrypted `password_hash` fields. To enforce stateless security practices, a distinct `user_session` table tracks real-time connections, IP addresses, tokens, and browser signatures. Security audit trails are fully realized through the `activity_log` mechanism, logging multi-actor operations across customers and admins.
  
  * **Transactional & Order Lifecycle Management (`orders`, `order_items`):** Shopping carts and finalized purchases share an optimized database path. The `orders` entity utilizes an `is_cart` boolean flag alongside complex enumerated status flags (`status`, `payment_method`, `payment_status`) to manage active shopping carts and completed checkout pipelines under a single relational layout. Individual line items are captured granularly inside `order_items`, binding specific variations and fixed unit prices directly to preserve historical financial audits against future catalog price updates.
  
  * **Administrative Governance (`admin`):** Back-end security infrastructure is isolated into a dedicated `admin` personnel control system, completely decoupled from customer profiles, enforcing strong multi-factor operational bounds and tracking inventory publishing ownership via foreign-key constraints on creation records.

---

#### 2. 🏪 Homepage / Main Showcase
*The virtual storefront welcoming consumers to luxury textiles.*

<img width="519" height="258" alt="homepage" src="https://github.com/user-attachments/assets/b428d94c-6fc0-4cab-851b-23169e50cdf4" />


* **🔍 Comprehensive Description:** The main landing interface serves as the primary visual display of the platform. It features dynamic banner sections highlighting seasonal shemagh collections, alongside a clean product grid displaying premium fabrics. The homepage integrates advanced UI components, responsive navigation bars, search filtering parameters, and instant cart shortcut triggers, ensuring high user retention and a modern retail journey.

---

#### 3. 🔍 Product Details
*Deep-dive specifications for fabric textures and traditional textiles.*

<img width="624" height="274" alt="product_details" src="https://github.com/user-attachments/assets/de50ac12-50e4-40fe-bf52-1fd465fb701e" />

* **🔍 Comprehensive Description:** This page offers a dedicated, comprehensive look into individual inventory listings. It allows customers to view high-resolution image samples, choose specific textile patterns, select traditional shemagh sizes, and inspect pricing tables. Programmatically, it queries the product database via dynamic routing and uses real-time state management to update availability and pricing variations based on selected options before the item is added to the user's cart.

---

#### 4. 🛒 Shopping Cart & Checkout Page
*The transactional flow bridge connecting user intent to secure sales.*

<img width="481" height="265" alt="shopping_cart" src="https://github.com/user-attachments/assets/d54a26b7-e5c5-432a-9cad-7534e94eb6d5" />


* **🔍 Comprehensive Description:** The cart interface displays an absolute summary of selected products, quantities, prices, and shipping logistics. It provides real-time calculations of totals, tax fields, and potential discounts. The checkout section securely handles user address confirmations, delivery notes, and transaction processing flows, modifying database inventory counts efficiently upon a successful checkout routine.

---

#### 5. ⚙️ Admin Dashboard & Features
*The full-stack administrative management hub driving business operations.*

<img width="624" height="274" alt="admin_features" src="https://github.com/user-attachments/assets/3a816960-5561-4518-9da1-2f20d3b876b3" />


* **🔍 Comprehensive Description:** This highly protected back-end interface provides administrators with full control over the e-commerce lifecycle. It includes data analytical summaries mapping out store performance metrics, custom inventory controls to dynamically add or toggle the status of fabrics and shemaghs, user account moderations, and comprehensive tracking systems for processing and shipping client orders.

---

### 🚀 Key Features & Functionalities

- **🗄️ Database-Driven System:** Backed by a structured database architecture optimized for e-commerce.
- **🎨 Product Showcase:** Elegant and dynamic homepage featuring luxury textile collections.
- **🏷️ Detailed Product Display:** Dedicated layout tailored for textile and Shemagh specifications.
- **💳 Complete Shopping Cycle:** Smooth functionality spanning from cart addition to final checkout.
- **💼 Admin Management Panel:** Comprehensive control center for managing products, monitoring sales, and updating store inventory.
- **📱 Responsive Layout:** Perfectly optimized for mobile, tablet, and desktop screens.

</div>
