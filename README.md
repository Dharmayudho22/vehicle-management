# Vehicle Management System

A web-based vehicle reservation management system for mining company operational transportation with **multi-level approval workflow**, **dashboard analytics**, **Excel reporting**, and **activity logging**.

---

## Overview

This application was developed to simplify operational vehicle reservation management in a mining company environment.

The system supports:

- Vehicle booking management
- Multi-level approval (Level 1 & Level 2)
- Booking conflict detection
- Driver & vehicle management
- Dashboard analytics
- Fuel & service logs
- Export reports to Excel
- Activity logging for auditing purposes

---

## Key Features

### Vehicle Booking
- Create operational vehicle reservations
- Assign drivers and vehicles
- Conflict detection to prevent double booking
- Trip lifecycle management

### Multi-Level Approval
- Approval Level 1 (Section Head)
- Approval Level 2 (Department Head)
- Cascading rejection mechanism

### Dashboard Analytics
- Vehicle usage statistics
- Booking trends
- Booking status distribution
- Latest reservations overview

### Reports & Export
- Date-based filtering
- Booking status filtering
- Excel export (`.xlsx`)

### Activity Logging
- System records user activity
- Approval actions logging
- Export and booking history

---

## Application Preview

### Dashboard

<p align="center">
  <img src="docs/dashboard.png" width="1000">
</p>

---

## System Design

### Activity Diagram

The following diagram illustrates the complete operational flow from vehicle reservation creation, approval process, trip execution, and completion.

<p align="center">
  <img src="docs/activity-diagram.png" width="900">
</p>

---

### Entity Relationship Diagram (ERD)

The following ERD illustrates the database structure and relationships between entities.

<p align="center">
  <img src="docs/erd.png" width="1100">
</p>

---

## Business Process

1. Admin creates a vehicle reservation.
2. The system validates booking conflicts.
3. Approver Level 1 reviews the request.
4. If approved, the request proceeds to Approver Level 2.
5. After final approval, the vehicle becomes ready for use.
6. Admin records trip information, odometer, and fuel usage.
7. The system stores logs and updates trip status.

---

## Tech Stack

| Component | Technology |
|------------|-------------|
| **Backend** | Laravel 11 |
| **Language** | PHP 8.3 |
| **Database** | MySQL 8 |
| **Frontend** | Blade |
| **UI Framework** | Tailwind CSS |
| **JavaScript** | Alpine.js |
| **Charts** | ApexCharts |
| **Export** | Laravel Excel |
| **Authentication** | Laravel Breeze |

---

## Demo Account

| Role | Email | Password |
|------|--------|----------|
| Admin | admin@nikel.co.id | `password` |
| Approver Level 1 | approver1@nikel.co.id | `password` |
| Approver Level 2 | approver2@nikel.co.id | `password` |

> For demonstration/testing purposes only.

---

## System Requirements

```bash
PHP >= 8.2
Composer >= 2.x
MySQL >= 8.0
Node.js >= 18.x (Optional)
```

---

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/Dharmayudho22/vehicle-management.git
cd vehicle-management
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vehicle_management
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migration & Seeder

```bash
php artisan migrate --seed
```

### 6. Start Development Server

```bash
php artisan serve
```

Open in browser:

```txt
http://localhost:8000
```

---

## Database Structure

| Table | Description |
|--------|-------------|
| `users` | User data & roles |
| `vehicles` | Vehicle information |
| `drivers` | Driver information |
| `bookings` | Vehicle reservations |
| `approvals` | Multi-level approvals |
| `fuel_logs` | Fuel consumption logs |
| `service_logs` | Vehicle maintenance logs |
| `app_logs` | Activity audit logs |

---

## Technical Highlights

- **Conflict Detection** — prevents overlapping vehicle booking.
- **Role-Based Access Control** — Admin & Approver permissions.
- **Multi-Level Approval Workflow** — sequential approval process.
- **Cascading Rejection** — automatic rejection propagation.
- **Responsive UI** — mobile-friendly interface.
- **Activity Logging** — audit trail for every major action.

---

## Author

**Sudarma Yudho Prayitno**  
Informatics Graduate | Fullstack Developer Enthusiast

GitHub: `github.com/Dharmayudho22`  
LinkedIn: `www.linkedin.com/in/sudarma-yudho-prayitno`

---
