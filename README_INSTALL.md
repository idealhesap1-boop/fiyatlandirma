# Component Product Module Installation Guide

## Quick Installation Steps

1. **Upload the module**
   - Go to Admin → Extensions → Installer
   - Upload the `component_product.zip` file
   - Click Continue

2. **Refresh modifications**
   - Go to Dashboard → Developer → Refresh
   - This clears the modification cache

3. **Install the module**
   - Go to Extensions → Extensions → Type: Modules
   - Search for "Component Product" or "Bileşen Bazlı Ürün"
   - Click Install button
   - **The module will automatically configure everything:**
     - Database tables and columns
     - Extension records
     - Settings
     - Events
     - Order totals extension

4. **Enable the module**
   - Click Edit on the module
   - Set "Modül Durumu" to enabled
   - Click Save

5. **Verify Order Totals**
   - Go to Extensions → Extensions → Type: Order Totals
   - Find "Component Total" (should be auto-installed)
   - Verify it's enabled

**That's it! The module handles all database operations automatically during installation.**

## Troubleshooting

### Module not appearing in the list
- Refresh modifications cache again (Dashboard → Developer → Refresh)
- Clear your browser cache
- Check that files were uploaded correctly to the server

### Installation errors
- Ensure your database user has ALTER TABLE permissions
- Check PHP error logs for specific error messages
- Verify file permissions on the server

### Vendor libraries
- This module works without vendor libraries for basic functionality
- The vendor folder has been removed to keep the package size small
- If you need advanced features (Excel export, etc.), run:
  ```bash
  cd system/library/component_product
  composer install
  ```

## What This Module Does

- Adds component-based product functionality to OpenCart
- Allows customers to select product components/options
- Tracks component data in cart and orders
- Integrates with Journal 3 and default themes
- Works with standard OpenCart installation
- **Automatically configures database during installation**

## File Structure

- `admin/controller/extension/module/component_product.php` - Admin controller (handles auto-install)
- `catalog/controller/extension/component_product/main.php` - Catalog controller
- `catalog/model/extension/component_product/main.php` - Data model
- `catalog/model/extension/total/component_total.php` - Order total extension
- `system/library/component_product/` - Library files
- `upload/ocmod/` - OCMOD modification files

## Support

For issues or questions, contact the module developer.
