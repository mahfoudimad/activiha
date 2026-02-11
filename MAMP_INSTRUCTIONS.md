# How to Preview with MAMP

Since you have MAMP installed, you can use it to run your website locally.

## Step 1: Configure Document Root

1.  Open **MAMP**.
2.  Go to **Preferences** (screens might vary by version, look for the "Web Server" tab).
3.  Find the **Document Root** setting.
4.  Click the folder icon to choose a new folder.
5.  Select this folder:
    `/Users/mac/Desktop/hostinger-export`
6.  Click **OK**.

## Step 2: Start Servers

1.  Click **Start Servers** in the main MAMP window.
2.  Wait for the lights to turn green.

## Step 3: Open in Browser

Once servers are running, click "Open WebStart page" or go directly to:
[http://localhost:8888](http://localhost:8888)

*(Note: If MAMP uses port 80, it might be http://localhost)*

## Troubleshooting

-   **403 Forbidden**: If you see this, make sure your `.htaccess` file is correct (I updated it earlier).
-   **Database**: If your site needs a database, you will need to import your SQL dump into MAMP's phpMyAdmin (usually at http://localhost:8888/phpMyAdmin) and update `api/config.php` with MAMP's database credentials (user: `root`, password: `root`).
