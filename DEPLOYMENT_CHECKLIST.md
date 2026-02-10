# 🚀 Hostinger Deployment Checklist

## Pre-Deployment
- [ ] Review all files in the hostinger-export folder
- [ ] Test locally if possible
- [ ] Backup any existing website data

## Deployment Steps

### 1. Access Hostinger
- [ ] Login to Hostinger control panel
- [ ] Navigate to File Manager or connect via FTP

### 2. Upload Files
- [ ] Upload all files from `hostinger-export/` to `public_html/`
- [ ] Verify `.htaccess` file is uploaded (may be hidden)
- [ ] Ensure `api/` folder is uploaded with all PHP files
- [ ] Confirm `admin/` folder is uploaded
- [ ] Check that `css/`, `js/` folders are present

### 3. Set Permissions
- [ ] Set `data/` directory to 755 permissions
- [ ] Set `uploads/` directory to 755 permissions
- [ ] Verify PHP files have 644 permissions

### 4. Configure PHP
- [ ] Check PHP version is 7.4 or higher
- [ ] Set `upload_max_filesize` to 10M
- [ ] Set `post_max_size` to 10M
- [ ] Enable `mod_rewrite` (usually enabled by default)

### 5. Test Website
- [ ] Visit homepage: `https://yourdomain.com`
- [ ] Check if products load (if any exist)
- [ ] Test product page (product.php)
- [ ] Verify images load correctly

### 6. Test Admin Panel
- [ ] Access admin: `https://yourdomain.com/admin/`
- [ ] Login with default credentials:
  - Username: `admin`
  - Password: `admin123`
- [ ] Verify dashboard loads
- [ ] Check all tabs (Orders, Products, Pages, Settings)

### 7. Security Setup
- [ ] **CRITICAL:** Change admin password immediately
- [ ] Enable SSL/HTTPS certificate (free with Let's Encrypt)
- [ ] Test HTTPS access
- [ ] Consider IP whitelisting for admin panel

### 8. Add Content
- [ ] Add your first product with image
- [ ] Test product display on frontend
- [ ] Create custom pages if needed
- [ ] Configure settings (colors, labels)

### 9. Test Order Flow
- [ ] Place a test order from frontend
- [ ] Verify order appears in admin panel
- [ ] Test order status updates
- [ ] Test order editing

### 10. Final Checks
- [ ] Test on mobile device
- [ ] Test on different browsers
- [ ] Verify all images load
- [ ] Check form submissions
- [ ] Test all admin functions

## Post-Deployment

### Backup Strategy
- [ ] Set up automatic backups in Hostinger
- [ ] Download backup of `data/db.json`
- [ ] Download backup of `uploads/` folder
- [ ] Schedule weekly manual backups

### Monitoring
- [ ] Check error logs in Hostinger panel
- [ ] Monitor order submissions
- [ ] Test regularly (weekly)

### Maintenance
- [ ] Keep admin credentials secure
- [ ] Regularly backup database
- [ ] Monitor disk space usage
- [ ] Clean up old uploaded images if needed

## Common Issues & Solutions

### ❌ API returns 404
**Solution:** Verify `.htaccess` is uploaded and mod_rewrite is enabled

### ❌ Can't upload images
**Solution:** Check `uploads/` directory permissions (755)

### ❌ Database not saving
**Solution:** Check `data/` directory permissions (755)

### ❌ Admin panel blank
**Solution:** Check browser console, verify all JS files uploaded

### ❌ CORS errors
**Solution:** Verify `.htaccess` CORS headers are present

## Support Resources

- Hostinger Knowledge Base: https://support.hostinger.com
- PHP Documentation: https://www.php.net/docs.php
- Check server error logs in Hostinger control panel

---

## Quick Reference

**Admin URL:** `https://yourdomain.com/admin/`  
**Default Username:** `admin`  
**Default Password:** `admin123` ⚠️ CHANGE THIS!

**Important Files:**
- Database: `data/db.json`
- Images: `uploads/`
- Config: `api/config.php`
- Routing: `.htaccess`

---

✅ **Deployment Complete!** Your store is now live on Hostinger.
