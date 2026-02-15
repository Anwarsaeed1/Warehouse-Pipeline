# Laravel Starter Project

Welcome to the **Laravel Starter Project**! This comprehensive starter kit is designed to simplify and accelerate the development of Laravel applications. It comes preloaded with essential features like user management, reporting, export functionality, notifications, and real-time updates powered by **Reverb**. Whether you're building a simple project or a complex application, this starter kit serves as a solid foundation to kickstart your development.

### Key Benefits of the Laravel Starter Project
- **Pre-built Functionalities:** Save development time with ready-to-use features.
- **Scalable Architecture:** Designed with scalability and maintainability in mind.
- **Modern Tools:** Includes integrations with advanced tools like **Laravel Excel**, **Postman**, and **Reverb** for seamless workflows.
- **Clean Code:** Follows best practices to ensure code clarity and efficiency.

Let’s dive in and explore what this starter project offers!



## 🚀 Features

### 1. **User Management**
- User registration and authentication.
- Role-based access control with CRUD functionality for roles and permissions.
- Login and logout functionality.
- Profile management for users.

### 2. **Password Recovery**
- Secure password reset functionality via email.
- Token-based reset process to ensure security.

### 3. **Dynamic Reporting System**
- Advanced reporting functionality with filtering, sorting, and customization options.

### 4. **Export Functionality**
- Export data in multiple formats (e.g., Excel).
- Seamless integration with **Laravel Excel** for efficient data handling.

### 5. **Notifications**
- Real-time and email-based notifications.
- Supports multiple channels, including email and SMS.

### 6. **Real-Time Updates**
- Live data synchronization powered by **Reverb**.
- Provides an enhanced user experience with instant updates.

### 7. **Settings Management**
- Centralized application settings for streamlined configuration.

### 8. **Help System**
- Efficiently fetch enumerations and metadata.
- Retrieve multiple tables in a single request for enhanced performance.

### 9. **Chunked File Uploads**
- Manage large file uploads by splitting them into smaller chunks for seamless handling.

---

## 🛠️ Installation

### **Prerequisites**
- PHP >= 8.0
- Composer
- Node.js and npm/yarn
- MySQL or any supported database

### **Setup Instructions**

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd <project-directory>
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Set up environment variables:**
   Copy the `.env.example` file to `.env` and update the necessary configurations.
   ```bash
   cp .env.example .env
   ```

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```

7. **Run the queue worker:**
   For notifications and real-time updates:
   ```bash
   php artisan queue:work
   ```

8. **Start the Reverb server:**
   ```bash
   php artisan reverb:start
   ```

---

## Warehouse Pipeline API Overview

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Login (email, password). Returns token. |
| POST | `/api/logout` | Logout (Bearer token). |
| GET | `/api/warehouses` | **List warehouses** (paginated). Filters: `page`, `pageSize`, `name`, `code`, `location`, `is_active` (0/1). |
| GET | `/api/warehouses/{id}/inventory` | Cached inventory for one warehouse. Filters: `page`, `pageSize`, `search`, `price_min`, `price_max`, `item_is_active` (0/1). |
| GET | `/api/inventory` | List stocks (all warehouses). Filters: `page`, `pageSize`, `warehouse_id`, `warehouse_is_active`, `item_is_active`, `search`, `price_min`, `price_max`. |
| GET | `/api/inventory/items` | List inventory items. Filters: `page`, `pageSize`, `search`, `price_min`, `price_max`, `is_active` (0/1). |
| PUT | `/api/stocks/{id}` | Update stock (`quantity`, `reserved_quantity`). |
| POST | `/api/stock-transfers` | Create stock transfer (`from_warehouse_id`, `to_warehouse_id`, `inventory_item_id`, `quantity`, `note`). |

All list endpoints support **pagination** (`page`, `pageSize`). Filter parameters are optional. Protected routes require `Authorization: Bearer {token}`.

---

## 📑 Postman Collection

A **Postman Collection** is included to simplify API testing. The file is located at `Postman/Warehouse-Pipeline-API.postman_collection.json`.

### **How to Use:**

1. **Import the collection:**
    - Open Postman and select **Import**.
    - Upload the provided JSON file.

2. **Set environment variables:**
    - Update the base URL to match your application’s URL.

3. **Test the APIs:**
    - Use the predefined requests to interact with the application’s features.

---

## 📂 Project Structure

This project follows a clean and modular structure for better maintainability. Below is an overview of the directory layout:

```plaintext
app
├── Console          # Custom Artisan commands
├── Enum             # Enumeration classes
├── Events           # Application events
├── Exceptions       # Custom exception handling
├── Filters          # Query filters for modular data fetching
│   ├── Example
│   ├── Global
│   ├── Setting
│   └── User
├── Helpers          # Helper functions for common utilities
├── Http             # HTTP-specific logic
│   ├── Controllers  # API controllers
│   │   ├── Auth
│   │   ├── Example
│   │   ├── Global
│   │   └── User
│   ├── Middleware   # HTTP middleware
│   ├── Requests     # Form request validation
│   │   ├── Auth
│   │   ├── Example
│   │   ├── Global
│   │   └── User
│   └── Resources    # API resources for JSON responses
│       ├── Auth
│       ├── Example
│       ├── Global
│       └── User
├── Jobs             # Queueable jobs
```

This structure ensures a clear separation of concerns and facilitates scalability.

---

## 🌐 Real-Time Integration with Reverb

This project leverages **Reverb** for real-time updates. Ensure the following environment variables are properly configured in your `.env` file:

```env
REVERB_APP_ID=1080194
REVERB_APP_KEY=bae3160ce349d284eace
REVERB_APP_SECRET=976e5b64127df42af8b6
REVERB_SCHEME=http
REVERB_HOST="127.0.0.1"
REVERB_PORT=9000
REVERB_SERVER_HOST="0.0.0.0"
REVERB_SERVER_PORT=9000
REVERB_SSL_LOCAL_CERT=""
REVERB_SSL_LOCAL_PK=""
```

Set `REALTIME=true` to enable real-time functionality.

---

## 🤝 Contribution Guidelines

We welcome contributions to improve this starter project! Follow these steps to contribute:

1. **Fork the repository.**
2. **Create a new branch:**
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Commit your changes:**
   ```bash
   git commit -m "Add your message here"
   ```
4. **Push your branch:**
   ```bash
   git push origin feature/your-feature-name
   ```
5. **Open a pull request:**
   Submit your pull request on GitHub.

---

## 📝 License

This project is open-source and available under the **MIT License**.

---

## 📧 Support

For questions or support, feel free to reach out to the project maintainer, **Hassan Elhawary**, via email at [hasanhawary1@gmail.com](mailto:hasanhawary1@gmail.com). Alternatively, you can open an issue directly on the repository for further assistance.

