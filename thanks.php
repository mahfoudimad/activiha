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
    <title>Thank You - Activiha</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <?php echo getTrackingScripts($db, 'purchase'); ?>
    <style>
        body {
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .thanks-card {
            background: white;
            border-radius: 3rem;
            padding: 4rem 2rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideUp {
            from {
                transform: translateY(40px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: #dcfce7;
            color: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(34, 197, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        h1 {
            font-weight: 950;
            color: #1e293b;
            margin-bottom: 1.5rem;
            line-height: 1.4;
        }

        .btn-home {
            display: inline-block;
            margin-top: 3rem;
            padding: 1.25rem 3rem;
            background: var(--premium-blue);
            color: white;
            text-decoration: none;
            border-radius: 1.5rem;
            font-weight: 800;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>

<body>
    <div class="thanks-card" id="content">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1 id="message"></h1>
        <p id="sub" style="color: #64748b; font-size: 1.1rem; font-weight: 600;"></p>
        <a href="index.php" class="btn-home" id="btn-text"></a>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const lang = urlParams.get('lang') || 'ar';

        const content = {
            ar: {
                message: 'شكرا على طلبكم سيتم إتصل بك خلال ساعات',
                sub: 'لقد تم استلام طلبك بنجاح. فريقنا سيتواصل معك قريباً لتأكيد المعلومات.',
                btn: 'العودة للمتجر',
                dir: 'rtl'
            },
            fr: {
                message: 'Merci. Votre commande a été reçue.',
                sub: 'Nous avons bien reçu votre commande. Notre équipe vous contactera sous peu pour confirmer les détails.',
                btn: 'Retour à la boutique',
                dir: 'ltr'
            }
        };

        const t = content[lang] || content.ar;
        document.documentElement.dir = t.dir;
        document.documentElement.lang = lang;
        document.getElementById('message').innerText = t.message;
        document.getElementById('sub').innerText = t.sub;
        document.getElementById('btn-text').innerText = t.btn;
    </script>
</body>

</html>