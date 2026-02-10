<?php
require_once 'api/config.php';
require_once 'api/tracking_helper.php';
$db = new Database();
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activiha</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <?php echo getTrackingScripts($db, 'page'); ?>
    <style>
        .page-content {
            background: white;
            border-radius: 2rem;
            padding: 4rem;
            margin: 4rem 0;
            box-shadow: var(--shadow-lg);
            min-height: 400px;
        }

        .page-header {
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 950;
            color: #1e293b;
            letter-spacing: -1px;
        }

        .content-body {
            font-size: 1.25rem;
            line-height: 1.8;
            color: #475569;
        }
    </style>
</head>

<body>
    <header>
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <img src="logo.png" alt="Activiha" style="height: 40px;">
            </a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="index.php#shop">Shop</a>
            </nav>
            <div class="lang-switcher" id="lang-switcher">
                <button class="lang-btn" data-lang="ar">العربية</button>
                <button class="lang-btn" data-lang="fr">Français</button>
            </div>
        </div>
    </header>

    <main class="container">
        <div id="page-loader"
            style="text-align: center; padding: 4rem; font-size: 1.5rem; font-weight: 700; color: #64748b;">
            <i class="fas fa-circle-notch fa-spin"></i> Loading...
        </div>

        <div id="page-container" class="page-content" style="display: none;">
            <div class="page-header">
                <h1 id="page-title"></h1>
            </div>
            <div id="page-body" class="content-body"></div>
        </div>
    </main>

    <footer style="margin-top: 4rem;">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2024 Activiha. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const slug = urlParams.get('slug');
        let currentLang = localStorage.getItem('lang') || 'ar';

        async function loadPage(lang) {
            currentLang = lang;
            localStorage.setItem('lang', lang);
            document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.lang = lang;

            // Update active btn
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.lang === lang);
            });

            if (!slug) {
                window.location.href = 'index.html';
                return;
            }

            try {
                const response = await fetch(`/api/pages/${slug}`);
                if (!response.ok) throw new Error('Page not found');
                const page = await response.json();

                document.title = `${lang === 'ar' ? page.titleAr : page.titleFr} - Activiha`;
                document.getElementById('page-title').innerText = lang === 'ar' ? page.titleAr : page.titleFr;
                document.getElementById('page-body').innerHTML = lang === 'ar' ? page.contentAr : page.contentFr;

                document.getElementById('page-loader').style.display = 'none';
                document.getElementById('page-container').style.display = 'block';
                document.getElementById('page-container').style.textAlign = lang === 'ar' ? 'right' : 'left';
            } catch (err) {
                document.getElementById('page-loader').innerHTML = `<p style="color:red;">${err.message}</p>`;
            }
        }

        document.getElementById('lang-switcher').addEventListener('click', (e) => {
            if (e.target.dataset.lang) {
                loadPage(e.target.dataset.lang);
            }
        });

        loadPage(currentLang);
    </script>
</body>

</html>