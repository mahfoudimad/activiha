# Activiha Store - Hostinger Deployment Package

This package contains your Algeria COD Store ready for deployment on Hostinger.

## 📦 Package Contents

```
hostinger-export/
├── api/                    # PHP Backend API
│   ├── config.php         # Database configuration
│   ├── products.php       # Products endpoint
│   ├── orders.php         # Orders endpoint
│   ├── auth.php           # Authentication endpoint
│   ├── settings.php       # Settings endpoint
│   └── pages.php          # Pages CMS endpoint
├── admin/                 # Admin dashboard
├── css/                   # Stylesheets
├── js/                    # JavaScript files
├── uploads/               # Product images directory
├── data/                  # JSON database directory
├── .htaccess             # Apache URL rewriting rules
├── index.html            # Homepage
├── product.php           # Product page (Dynamic)
├── page.html             # Custom pages
└── thanks.html           # Thank you page
```

## 🚀 Deployment Instructions

### Step 1: Upload Files to Hostinger

1. **Login to Hostinger Control Panel**
   - Go to your Hostinger account
   - Access File Manager or use FTP

2. **Upload All Files**
   - Upload the entire contents of the `hostinger-export` folder to your `public_html` directory
   - Make sure to include the `.htaccess` file (it might be hidden)

### Step 2: Set Directory Permissions

Set the following permissions via File Manager or FTP:

```bash
data/          → 755 (read, write, execute for owner)
uploads/       → 755 (read, write, execute for owner)
```

### Step 3: Verify PHP Version

- Ensure your hosting plan uses **PHP 7.4 or higher**
- You can check/change this in Hostinger's control panel under "PHP Configuration"

### Step 4: Test Your Website

1. Visit your domain: `https://yourdomain.com`
2. Test the admin panel: `https://yourdomain.com/admin/`
   - Default credentials:
     - Username: `admin`
     - Password: `admin123`
   - **⚠️ IMPORTANT: Change these credentials immediately after first login!**

## 🔧 Configuration

### Database Location

The application uses a JSON file-based database located at:
```
data/db.json
```

This file is automatically created on first run with default data.

### Changing Admin Password

To change the admin password, you'll need to:

1. Generate a new password hash using PHP:
```php
<?php
echo password_hash('your_new_password', PASSWORD_BCRYPT);
?>
```

2. Update the password in `data/db.json` under the `users` array

### File Upload Limits

If you experience issues uploading large product images:

1. Go to Hostinger Control Panel → PHP Configuration
2. Increase these values:
   - `upload_max_filesize` → 10M or higher
   - `post_max_size` → 10M or higher

## 📱 Features

### Customer-Facing Features
- ✅ Product catalog with images
- ✅ Cash on Delivery (COD) ordering
- ✅ Arabic and French language support
- ✅ Mobile-responsive design
- ✅ Custom pages support

### Admin Dashboard Features
- ✅ Order management
- ✅ Product management (CRUD)
- ✅ Custom pages CMS
- ✅ Form customization
- ✅ Settings management
- ✅ Real-time statistics

## 🔒 Security Recommendations

1. **Change Default Credentials**
   - Change admin username and password immediately

2. **Backup Your Data**
   - Regularly backup the `data/db.json` file
   - Backup the `uploads/` directory

3. **Secure Your Admin Panel**
   - Consider adding IP restrictions via .htaccess
   - Use strong passwords

4. **SSL Certificate**
   - Enable SSL/HTTPS through Hostinger (usually free with Let's Encrypt)

## 🛠️ Troubleshooting

### Issue: API calls return 404

**Solution:** Make sure `.htaccess` file is uploaded and mod_rewrite is enabled on your server.

### Issue: Can't upload images

**Solution:** 
1. Check that `uploads/` directory has write permissions (755)
2. Increase PHP upload limits in Hostinger control panel

### Issue: Database not saving

**Solution:**
1. Check that `data/` directory has write permissions (755)
2. Ensure PHP has permission to create files

### Issue: Blank admin page

**Solution:**
1. Check browser console for JavaScript errors
2. Verify all files were uploaded correctly
3. Clear browser cache

## 📞 API Endpoints

All API endpoints are located under `/api/`:

- `GET /api/products` - Get all products
- `GET /api/products/{id}` - Get single product
- `POST /api/products` - Create product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

- `GET /api/orders` - Get all orders
- `POST /api/orders` - Create order
- `PATCH /api/orders/{id}` - Update order status
- `PUT /api/orders/{id}` - Update full order

- `POST /api/auth` - Admin login

- `GET /api/settings` - Get settings
- `PUT /api/settings` - Update settings

- `GET /api/pages` - Get all pages
- `POST /api/pages` - Create page
- `PUT /api/pages/{id}` - Update page
- `DELETE /api/pages/{id}` - Delete page

## 📊 Database Structure

The `data/db.json` file contains:

```json
{
  "products": [],
  "orders": [],
  "pages": [],
  "users": [],
  "settings": {},
  "formSettings": {}
}
```

## 🎨 Customization

### Changing Colors
1. Login to admin panel
2. Go to Settings tab
3. Modify theme colors and form settings

### Adding Products
1. Login to admin panel
2. Go to Products tab
3. Click "Add New Product"
4. Fill in details and upload image

### Creating Custom Pages
1. Login to admin panel
2. Go to Pages tab
3. Create pages with Arabic and French content

## 📝 Notes

- This is a lightweight application using JSON file storage
- For high-traffic sites, consider migrating to MySQL database
- Regular backups are recommended
- Monitor the `data/db.json` file size

## 🆘 Support

If you encounter any issues:
1. Check the troubleshooting section above
2. Verify all files are uploaded correctly
3. Check Hostinger's error logs in the control panel
4. Ensure PHP version is 7.4 or higher

---

**Version:** 1.0.0  
**Last Updated:** February 2026
