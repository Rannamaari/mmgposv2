# POS System for Garage Management

## Overview

This is a comprehensive Point of Sale (POS) system designed specifically for motorcycle garage operations. It provides a modern, mobile-friendly interface for processing payments, managing customers, and tracking inventory.

## Features

### 🎯 Core POS Features

-   **Customer Management**: Search and create customers with phone numbers
-   **Motorcycle Tracking**: Link motorcycles to customers with plate numbers and models
-   **Service Catalog**: Quick access to common garage services
-   **Parts Inventory**: Real-time stock management with automatic deduction
-   **Payment Processing**: Support for cash and bank transfers
-   **Invoice Generation**: Automatic invoice creation with unique numbers
-   **Work Order Integration**: Link POS transactions to existing work orders

### ⚡ Quick Service Buttons

-   **Oil Change** (MVR 150)
-   **Tire Check** (MVR 50)
-   **Engine Check** (MVR 200)
-   **Cleaning** (MVR 75)

### 💳 Payment Methods

-   **Cash**: Instant payment processing
-   **BML Transfer**: Bank transfer with screenshot proof upload

### 📱 Mobile-First Design

-   Responsive interface optimized for tablets and mobile devices
-   Touch-friendly buttons and controls
-   Real-time clock display
-   Full-screen POS mode

## How to Use

### 1. Accessing the POS

1. Navigate to `/admin/pos` in your browser
2. Login with your credentials
3. The POS interface will load in full-screen mode

### 2. Processing a Sale

#### Step 1: Customer Selection

-   Search for existing customers by name or phone
-   Create new customers on-the-fly
-   Select customer's motorcycle (or create new one)
-   Optionally link to existing work order

#### Step 2: Adding Items

-   **Quick Add**: Use the service buttons for common services
-   **Manual Add**: Search for services or parts in the cart section
-   **Quantity**: Adjust quantities as needed
-   **Pricing**: Prices auto-populate but can be modified

#### Step 3: Payment

-   Choose payment method (Cash or BML Transfer)
-   For transfers, upload screenshot proof
-   Review total amount
-   Complete payment

### 3. Cart Management

-   **Add Items**: Use "➕ Add Item" button or quick service buttons
-   **Remove Items**: Use delete button on each line item
-   **Clear Cart**: Use "🗑️ Clear Cart" button to start fresh
-   **Reorder**: Drag and drop items to reorder

## Technical Details

### Database Structure

-   **Customers**: Name, phone, motorcycles
-   **Motorcycles**: Plate number, model, customer relationship
-   **Services**: Name, price, category, active status
-   **Parts**: SKU, name, price, cost, stock quantity
-   **Invoices**: Number, totals, status, work order link
-   **Payments**: Method, amount, proof, cashier session
-   **Work Order Items**: Item tracking, quantities, prices

### Key Models

-   `Customer`: Customer management
-   `Motorcycle`: Vehicle tracking
-   `Service`: Service catalog
-   `Part`: Inventory management
-   `Invoice`: Transaction records
-   `Payment`: Payment processing
-   `WorkOrderItem`: Item tracking

### Features

-   **Real-time Search**: Instant customer and item search
-   **Auto-calculation**: Automatic line totals and invoice totals
-   **Inventory Management**: Automatic stock deduction for parts
-   **Error Handling**: Comprehensive error handling and logging
-   **Image Compression**: Automatic proof image compression
-   **Session Management**: Cashier session tracking

## Troubleshooting

### Common Issues

1. **Quick Service Buttons Not Working**

    - Ensure JavaScript is enabled
    - Check browser console for errors
    - Try refreshing the page

2. **Customer Search Not Working**

    - Verify database connection
    - Check if customers exist in database
    - Ensure proper permissions

3. **Payment Processing Errors**

    - Check form validation
    - Verify required fields are filled
    - Check database constraints

4. **Image Upload Issues**
    - Ensure proper file permissions
    - Check file size limits
    - Verify supported file types

### Debug Mode

-   Check Laravel logs: `storage/logs/laravel.log`
-   Enable debug mode in `.env` file
-   Use browser developer tools for frontend issues

## Security Features

-   **Authentication**: Required login for all POS operations
-   **Authorization**: Role-based access control
-   **Data Validation**: Comprehensive input validation
-   **SQL Injection Protection**: Laravel's built-in protection
-   **CSRF Protection**: Automatic CSRF token validation
-   **File Upload Security**: Validated file types and sizes

## Performance Optimizations

-   **Database Indexing**: Optimized queries with proper indexes
-   **Image Compression**: Automatic image optimization
-   **Caching**: Laravel's built-in caching mechanisms
-   **Lazy Loading**: Efficient relationship loading
-   **Pagination**: Large dataset handling

## Future Enhancements

-   **Barcode Scanning**: QR code and barcode support
-   **Receipt Printing**: Thermal printer integration
-   **Multi-language**: Internationalization support
-   **Advanced Reporting**: Sales analytics and reports
-   **Integration**: Third-party payment gateways
-   **Offline Mode**: Offline transaction support

## Support

For technical support or feature requests, please contact the development team or create an issue in the project repository.
