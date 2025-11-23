# Vendisync - Business Logic Description

## Overview

Vendisync is a comprehensive e-commerce management system built on Laravel with Filament admin panel. The system is designed to manage products, brands, retailers, and orders for businesses that need to track their sales and inventory across multiple retail channels.

## Core Entities

### 1. Users
- System users with role-based access control
- Roles include admin, retailor, and other custom roles
- Authentication and authorization managed through Laravel's built-in system

### 2. Brands
- Represent product manufacturers or own brands
- Each brand can have multiple products
- Brands are managed under "Brand Management" section

### 3. Categories
- Product categorization system
- Hierarchical organization of products
- Used to group similar products together

### 4. Products
- Core inventory items with detailed information
- Fields include name, description, pricing, quantity, and images
- Support for multilingual descriptions
- Associated with brands and categories
- Track base price (cost) and sell price for profit calculation

### 5. Retailors
- Customer entities that purchase products
- Include contact information, addresses, and status
- Can be marked as active/inactive
- Associated with authorized users for access control

### 6. Orders
- Core transaction records
- Link retailers, brands, products, and users
- Track financial details including:
  - Subtotal, tax, discount, and total amounts
  - Profit calculations (total profit, profit margin)
  - Order status tracking
  - Payment status and methods
  - Delivery dates and tracking

### 7. Order Items
- Line items within orders
- Link specific products to orders with quantities
- Automatic calculation of unit and total prices/profits

## Business Workflows

### Order Management
1. Create new orders by selecting retailer, brand, and products
2. System automatically populates retailer details
3. Add products with automatic price and profit calculations
4. Track order through various statuses (Confirmed, Shipped, Delivered, etc.)
5. Monitor payment status (Pending, Paid, Refunded)

### Product Management
1. Create and manage product catalog with multilingual support
2. Assign products to brands and categories
3. Track inventory quantities
4. Manage pricing with cost and selling price
5. Control product visibility with active/featured flags

### Retailor Management
1. Maintain customer database with contact information
2. Track retailor status (Pending, Verified, Rejected)
3. Associate with authorized system users
4. Manage active/inactive status

## Key Features

### Multilingual Support
- Full localization support for products and interface
- JSON-based translation system
- Language switcher in admin panel

### Profit Tracking
- Automatic calculation of profit margins
- Detailed financial reporting in orders
- Base price vs. sell price tracking

### Email Templates
- Customizable email templates for communications
- Markdown-based email system
- History tracking of sent emails

### Role-Based Access Control
- Fine-grained permissions through Filament Shield
- Different access levels for admin and retailor users
- Secure authentication and session management

## Data Relationships

```
User 1---* Order
Brand 1---* Product
Brand 1---* Order
Category 1---* Product
Retailor 1---* Order
Order 1---* OrderItem
Product 1---* OrderItem
```

This structure allows for comprehensive tracking of all business transactions while maintaining data integrity and enabling detailed reporting.