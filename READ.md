# Midnight Brew - Coffee Shop App

## Overview
This project is a coffee shop application with two parts:
- **Android client** (Flutter) for customers to login, browse the menu, and place orders.
- **Admin web panel** (PHP/MySQL) to manage products, orders, and users.

## Features
- Customer login/signup
- Order management
- Admin CRUD operations for products
- Live connection to a MySQL database on AwardSpace

## Tech Stack
- Flutter / Dart (Android client)
- PHP, HTML, CSS, MySQL (Admin web panel)
- HTTP requests for client-server communication

## Notes
- Sensitive database credentials are excluded. See `admin-web/include/db_connect-example.php` for setup.
- Android client uses `http://cselec1.atwebpages.com/CoffeeShop` for server API.
