<?php
require_once 'api/config.php';
require_once 'api/tracking_helper.php';

$title = 'Order Now - Activiha';
$ogTitle = 'Activiha - Premium Products';
$ogDesc = 'Fast delivery & Cash on Delivery available nationwide.';
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$ogImage = $protocol . $host . '/logo.png';
$ogUrl = $protocol . $host . $_SERVER['REQUEST_URI'];

if (isset($_GET['id'])) {
    $db = new Database();
    $product = $db->find('products', 'id', $_GET['id']);

    if ($product) {
        $title = $product['title'] . ' - Activiha';
        $ogTitle = $product['title'];
        $ogDesc = substr(strip_tags($product['description'] ?? 'Order now with Cash on Delivery'), 0, 160);
        // Ensure image has full URL
        if (strpos($product['image'], 'http') === 0) {
            $ogImage = $product['image'];
        }
        else {
            $ogImage = $protocol . $host . '/' . ltrim($product['image'], '/');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo htmlspecialchars($title); ?>
    </title>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="product">
    <meta property="og:url" content="<?php echo htmlspecialchars($ogUrl); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($ogDesc); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo htmlspecialchars($ogUrl); ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($ogDesc); ?>">
    <meta property="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <?php echo getTrackingScripts($db, 'product'); ?>
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
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
            </nav>
            <div class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </div>
        </div>
    </header>

    <main class="container">
        <div style="display: flex; justify-content: center; margin-top: 2rem;">
            <div class="lang-switcher" id="lang-switcher">
                <button class="lang-btn" data-lang="ar">العربية</button>
                <button class="lang-btn" data-lang="fr">Français</button>
            </div>
        </div>
        <div class="product-detail-container" id="product-detail">
            <!-- Product details loaded here -->
            <p>Loading product details...</p>
        </div>

        <!-- Related Products Section -->
        <!-- Related Products Section -->
        <!-- <div class="related-products-section">
            <h2 id="related-title" style="font-size: 2rem; font-weight: 900; margin-bottom: 2rem; color: #1e293b;"></h2>
            <div id="related-products-grid" class="products-grid">
               
            </div>
        </div> -->
    </main>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2024 Activiha. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');
        let currentLang = 'ar'; // Default to Arabic

        const translations = {
            ar: {
                dir: 'rtl',
                offer: 'عرض خاص - لفترة محدودة',
                endsIn: 'تتنهي العرض في :',
                price: 'DA',
                originalPrice: '14000 DA',
                freeShipping: 'توصيل مجاني لـ 58 ولاية',
                paymentOnDelivery: 'الدفع عند الاستلام + ضمان الجودة',
                formTitle: 'إملأ الاستمارة الآن',
                formSub: '(الرجاء تأكيد طلبك بالضغط على الزر أدناء)',
                nameLabel: 'الإسم الكامل *',
                namePlaceholder: 'أدخل اسمك الكامل',
                phoneLabel: 'رقم الهاتف *',
                phonePlaceholder: '0XXXXXXXXX',
                wilayaLabel: 'الولاية *',
                cityLabel: 'البلدية / المدينة *',
                cityPlaceholder: 'أدخل بلدتك',
                qtyLabel: 'الكمية',
                totalLabel: 'المجموع النهائي:',
                submitBtn: 'أطلب الآن - الدفع عند الاستلام',
                trustQuality: 'جودة مضمونة',
                relatedTitle: 'قد يعجبك أيضاً',
                viewProduct: 'عرض المنتج',
                loading: 'جاري التحميل...',
                orderSuccess: 'تم تسجيل طلبك بنجاح! سنتصل بك قريباً.',
                orderError: 'حدث خطأ ما. يرجى المحاولة مرة أخرى.',
                submitting: 'جاري الإرسال...',
                purchased: 'اشترى هذا المنتج للتو !',
                from: 'من',
                unit: 'حبة'
            },
            fr: {
                dir: 'ltr',
                offer: 'OFFRE SPÉCIALE - LIMITÉE',
                endsIn: 'L\'offre se termine dans :',
                price: 'DA',
                originalPrice: '14000 DA',
                freeShipping: 'Livraison gratuite vers 58 Wilayas',
                paymentOnDelivery: 'Paiement à la livraison + Garantie qualité',
                formTitle: 'REMPLISSEZ LE FORMULAIRE',
                formSub: '(Veuillez confirmer votre commande en cliquant sur le bouton ci-dessous)',
                nameLabel: 'Nom Complet *',
                namePlaceholder: 'Entrez votre nom complet',
                phoneLabel: 'Numéro de Téléphone *',
                phonePlaceholder: '0XXXXXXXXX',
                wilayaLabel: 'Wilaya *',
                cityLabel: 'Commune / Ville *',
                cityPlaceholder: 'Entrez votre commune',
                qtyLabel: 'Quantité',
                totalLabel: 'Total Final :',
                submitBtn: 'Commander Maintenant - COD',
                trustFast: 'Livraison Rapide',
                trustSecure: 'Paiement Sécurisé',
                trustQuality: 'Qualité Garantie',
                relatedTitle: 'Vous aimerez aussi',
                viewProduct: 'Voir le produit',
                loading: 'Chargement...',
                orderSuccess: 'Commande passée avec succès! Nous vous contacterons.',
                orderError: 'Une erreur est survenue. Veuillez réessayer.',
                submitting: 'Envoi en cours...',
                purchased: 'Vient d\'acheter ce produit !',
                from: 'de',
                unit: 'unité'
            }
        };

        async function loadProductDetail(lang = 'ar') {
            currentLang = lang;
            document.documentElement.dir = translations[lang].dir;
            document.documentElement.lang = lang;

            // Update active btn
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.lang === lang);
            });

            if (!productId) {
                window.location.href = 'index.php';
                return;
            }

            try {
                const response = await fetch(`/api/products/${productId}`);
                const product = await response.json();

                if (!product) throw new Error('Product not found');

                const t = translations[lang];

                document.getElementById('product-detail').innerHTML = `
                    <div class="product-gallery" style="direction: ${t.dir}; text-align: ${lang === 'ar' ? 'right' : 'left'};">
                        <div style="position: relative;">
                            <img src="${product.image}" alt="${product.title}" style="border-radius: 2rem; box-shadow: var(--shadow-lg);">
                            <div style="position: absolute; top: 1.5rem; ${lang === 'ar' ? 'right: 1.5rem;' : 'left: 1.5rem;'} background: rgba(239, 68, 68, 0.95); color: white; padding: 0.6rem 1.25rem; border-radius: 999px; font-weight: 800; font-size: 1rem; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);">
                                SALE -35%
                            </div>
                        </div>
                        <div style="margin-top: 2rem;">
                            <div class="flash-sale-badge" style="background: #fffbeb; color: #b45309; border: 1.5px dashed #fcd34d; font-weight: 800; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-bolt"></i> ${t.offer}
                            </div>
                            <h1 style="font-size: 2.5rem; margin-top: 1rem; margin-bottom: 0.75rem; font-weight: 950; color: #1f2937; letter-spacing: -1px;">${product.title}</h1>
                            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem;">
                                <span style="font-size: 3rem; font-weight: 950; color: #dc2626; letter-spacing: -1.5px;">${product.price} DA</span>
                                <span style="font-size: 1.5rem; text-decoration: line-through; color: #9ca3af; font-weight: 600;">${product.price + 1500} DA</span>
                            </div>
                            
                            <div style="margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: 1rem; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <p style="font-size: 0.95rem; font-weight: 800; color: #1e293b;">${t.paymentOnDelivery.split('+')[0]}</p>
                                    <p style="font-size: 0.8rem; font-weight: 600; color: #64748b;">${t.paymentOnDelivery.split('+')[1] || ''}</p>
                                </div>
                                <i class="fas fa-truck" style="font-size: 1.5rem; color: #f97316;"></i>
                            </div>

                            <p style="color: #4b5563; margin: 2rem 0; font-size: 1.15rem; line-height: 1.8; font-weight: 500;">${product.description || (lang === 'ar' ? 'منتج عالي الجودة يصلك إلى باب منزلك مع ضمان استرجاع الأموال.' : 'Produit de haute qualité livré à domicile avec garantie de remboursement.')}</p>
                        </div>
                    </div>
                    
                    <div class="cod-form-container" style="direction: ${t.dir};">
                        <div class="cod-form premium-card" style="border: 2px solid #e2e8f0; box-shadow: var(--shadow-lg); border-radius: 2.5rem; overflow: hidden; background: #fff;">
                            <!-- Promotional Header -->
                            <div style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); padding: 1.75rem 3rem; text-align: center; margin: 0;">
                                <h2 style="color: white; font-weight: 950; font-size: 1.75rem; letter-spacing: -0.5px; margin: 0 0 0.5rem 0;">${lang === 'ar' ? 'تخفيض محدود لفترة قصيرة' : 'Réduction Pour Temps Limité'}</h2>
                                <p style="color: rgba(255,255,255,0.95); font-size: 1rem; font-weight: 700; margin: 0;">${lang === 'ar' ? 'الدفع عند الاستلام • ضمان الجودة' : 'Paiement à la livraison • Garantie Qualité'}</p>
                            </div>
                            
                            <div style="padding: 3rem;">
                            <form id="order-form">
                                <input type="hidden" name="productId" value="${product.id}">
                                
                                <div class="form-group">
                                    <label style="color: #1e293b; font-weight: 800; font-size: 0.95rem; margin-bottom: 0.75rem; display: block;">${t.nameLabel} *</label>
                                    <input type="text" name="fullName" class="form-control" placeholder="${t.namePlaceholder}" required style="height: 58px; border-radius: 1rem; background: #ffffff; border: 2px solid #e2e8f0; padding: 0 1.25rem; font-size: 1rem; font-weight: 600;">
                                </div>
                                
                                <div class="form-group">
                                    <label style="color: #1e293b; font-weight: 800; font-size: 0.95rem; margin-bottom: 0.75rem; display: block;">${t.phoneLabel} *</label>
                                    <div class="phone-input-group" style="border: 2px solid #e2e8f0; border-radius: 1rem; overflow: hidden; background: #ffffff; height: 58px; display: flex; align-items: center;">
                                        <div class="phone-prefix" style="direction: ltr; min-width: 85px; background: #f1f5f9; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #1e293b; padding: 0 0.75rem; border-right: 2px solid #e2e8f0;">
                                            <span>🇩🇿</span> +213
                                        </div>
                                        <input type="tel" name="phone" class="form-control" placeholder="${t.phonePlaceholder}" required style="height: 100%; border: none; background: transparent; font-weight: 600; flex: 1; padding: 0 1.25rem;">
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div class="form-group">
                                        <label style="color: #1e293b; font-weight: 800; font-size: 0.95rem; margin-bottom: 0.75rem; display: block;">${t.wilayaLabel} *</label>
                                        <select name="city" class="form-control" required style="height: 58px; border-radius: 1rem; background: #ffffff; border: 2px solid #e2e8f0; appearance: none; padding: 0 1.25rem; font-weight: 600; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: ${lang === 'ar' ? 'left' : 'right'} 1rem center; background-size: 1.25rem;">
                                            <option value="">${lang === 'ar' ? 'اختر الولاية' : 'Sélectionner Wilaya'}</option>
                                            <option value="Adrar">01 - Adrar</option>
                                            <option value="Chlef">02 - Chlef</option>
                                            <option value="Laghouat">03 - Laghouat</option>
                                            <option value="Oum El Bouaghi">04 - Oum El Bouaghi</option>
                                            <option value="Batna">05 - Batna</option>
                                            <option value="Béjaïa">06 - Béjaïa</option>
                                            <option value="Biskra">07 - Biskra</option>
                                            <option value="Béchar">08 - Béchar</option>
                                            <option value="Blida">09 - Blida</option>
                                            <option value="Bouira">10 - Bouira</option>
                                            <option value="Tamanrasset">11 - Tamanrasset</option>
                                            <option value="Tébessa">12 - Tébessa</option>
                                            <option value="Tlemcen">13 - Tlemcen</option>
                                            <option value="Tiaret">14 - Tiaret</option>
                                            <option value="Tizi Ouzou">15 - Tizi Ouzou</option>
                                            <option value="Alger">16 - Alger</option>
                                            <option value="Djelfa">17 - Djelfa</option>
                                            <option value="Jijel">18 - Jijel</option>
                                            <option value="Sétif">19 - Sétif</option>
                                            <option value="Saïda">20 - Saïda</option>
                                            <option value="Skikda">21 - Skikda</option>
                                            <option value="Sidi Bel Abbès">22 - Sidi Bel Abbès</option>
                                            <option value="Annaba">23 - Annaba</option>
                                            <option value="Guelma">24 - Guelma</option>
                                            <option value="Constantine">25 - Constantine</option>
                                            <option value="Médéa">26 - Médéa</option>
                                            <option value="Mostaganem">27 - Mostaganem</option>
                                            <option value="M'Sila">28 - M'Sila</option>
                                            <option value="Mascara">29 - Mascara</option>
                                            <option value="Ouargla">30 - Ouargla</option>
                                            <option value="Oran">31 - Oran</option>
                                            <option value="El Bayadh">32 - El Bayadh</option>
                                            <option value="Illizi">33 - Illizi</option>
                                            <option value="Bordj Bou Arreridj">34 - Bordj Bou Arreridj</option>
                                            <option value="Boumerdès">35 - Boumerdès</option>
                                            <option value="El Tarf">36 - El Tarf</option>
                                            <option value="Tindouf">37 - Tindouf</option>
                                            <option value="Tissemsilt">38 - Tissemsilt</option>
                                            <option value="El Oued">39 - El Oued</option>
                                            <option value="Khenchela">40 - Khenchela</option>
                                            <option value="Souk Ahras">41 - Souk Ahras</option>
                                            <option value="Tipaza">42 - Tipaza</option>
                                            <option value="Mila">43 - Mila</option>
                                            <option value="Aïn Defla">44 - Aïn Defla</option>
                                            <option value="Naâma">45 - Naâma</option>
                                            <option value="Aïn Témouchent">46 - Aïn Témouchent</option>
                                            <option value="Ghardaïa">47 - Ghardaïa</option>
                                            <option value="Relizane">48 - Relizane</option>
                                            <option value="Timimoun">49 - Timimoun</option>
                                            <option value="Bordj Badji Mokhtar">50 - Bordj Badji Mokhtar</option>
                                            <option value="Ouled Djellal">51 - Ouled Djellal</option>
                                            <option value="Béni Abbès">52 - Béni Abbès</option>
                                            <option value="In Salah">53 - In Salah</option>
                                            <option value="In Guezzam">54 - In Guezzam</option>
                                            <option value="Touggourt">55 - Touggourt</option>
                                            <option value="Djanet">56 - Djanet</option>
                                            <option value="El M'Ghair">57 - El M'Ghair</option>
                                            <option value="El Meniaa">58 - El Meniaa</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label style="color: #1e293b; font-weight: 800; font-size: 0.95rem; margin-bottom: 0.75rem; display: block;">${lang === 'ar' ? 'البلدية / المدينة' : 'Commune / Ville'} *</label>
                                        <input type="text" name="address" class="form-control" placeholder="${lang === 'ar' ? 'أدخل البلدية أو المدينة' : 'Entrez votre commune'}" required style="height: 58px; border-radius: 1rem; background: #ffffff; border: 2px solid #e2e8f0; padding: 0 1.25rem; font-weight: 600;">
                                    </div>
                                </div>
                                
                                <!-- Quantity Selector -->
                                <div class="form-group" style="margin-top: 1.5rem;">
                                    <label style="color: #1e293b; font-weight: 800; font-size: 0.95rem; margin-bottom: 0.75rem; display: block; text-align: center;">${lang === 'ar' ? 'الكمية' : 'Quantité'}</label>
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; background: #f8fafc; border-radius: 1rem; padding: 1rem; border: 2px solid #e2e8f0;">
                                        <button type="button" id="qty-minus" style="width: 45px; height: 45px; border-radius: 0.75rem; background: #ffffff; border: 2px solid #e2e8f0; font-size: 1.5rem; font-weight: 800; color: #64748b; cursor: pointer; transition: all 0.2s;">-</button>
                                        <span id="qty-display" style="font-size: 1.75rem; font-weight: 900; min-width: 50px; text-align: center; color: #1e293b;">1</span>
                                        <button type="button" id="qty-plus" style="width: 45px; height: 45px; border-radius: 0.75rem; background: #ffffff; border: 2px solid #e2e8f0; font-size: 1.5rem; font-weight: 800; color: #64748b; cursor: pointer; transition: all 0.2s;">+</button>
                                    </div>
                                    <input type="hidden" name="quantity" id="qty-input" value="1">
                                </div>
                                
                                <!-- Price Breakdown -->
                                <div style="margin: 2rem 0; padding: 1.75rem; background: #f8fafc; border-radius: 1.25rem; border: 2px solid #e2e8f0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                        <span style="color: #64748b; font-weight: 700; font-size: 0.95rem;">${lang === 'ar' ? 'سعر المنتج' : 'Prix Produit'}</span>
                                        <span style="color: #1e293b; font-weight: 800; font-size: 1.1rem;">${product.price} DA</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; margin-bottom: 1rem; border-bottom: 2px dashed #e2e8f0;">
                                        <span style="color: #64748b; font-weight: 700; font-size: 0.95rem;">${lang === 'ar' ? 'التوصيل' : 'Livraison à domicile'}</span>
                                        <span style="color: #10b981; font-weight: 800; font-size: 0.9rem;">${lang === 'ar' ? 'مجاني' : 'GRATUIT'}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="color: #1e293b; font-weight: 900; font-size: 1.2rem;">${lang === 'ar' ? 'المجموع النهائي:' : 'Total Final:'}</span>
                                        <span id="total-price" style="color: #2563eb; font-size: 2rem; font-weight: 950;">DA ${product.price}</span>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="totalPrice" value="${product.price}">

                                <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); width: 100%; height: 65px; font-size: 1.4rem; border-radius: 1.25rem; box-shadow: 0 20px 40px -12px rgba(37, 99, 235, 0.4); text-transform: none; font-weight: 900; display: flex; align-items: center; justify-content: center; gap: 0.75rem; transition: all 0.3s;">
                                    <i class="fas fa-shopping-cart"></i>
                                    ${lang === 'ar' ? 'أطلب الآن - الدفع عند الاستلام' : 'Commander Maintenant - COD'}
                                    <i class="fas fa-chevron-${lang === 'ar' ? 'left' : 'right'}"></i>
                                </button>
                                
                                <div class="trust-badges" style="margin-top: 2.5rem; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; text-align: center;">
                                    <div class="trust-badge-item">
                                        <div style="width: 50px; height: 50px; background: #eff6ff; border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; border: 2px solid #dbeafe;">
                                            <i class="fas fa-shield-alt" style="color: #2563eb; font-size: 1.25rem;"></i>
                                        </div>
                                        <span style="font-size: 0.75rem; font-weight: 800; color: #475569;">${lang === 'ar' ? 'دفع آمن' : 'Paiement Sûr'}</span>
                                    </div>
                                    <div class="trust-badge-item">
                                        <div style="width: 50px; height: 50px; background: #fef3c7; border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; border: 2px solid #fde68a;">
                                            <i class="fas fa-award" style="color: #f59e0b; font-size: 1.25rem;"></i>
                                        </div>
                                        <span style="font-size: 0.75rem; font-weight: 800; color: #475569;">${lang === 'ar' ? 'ضمان الجودة' : 'Garantie Qualité'}</span>
                                    </div>
                                    <div class="trust-badge-item">
                                        <div style="width: 50px; height: 50px; background: #ecfdf5; border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; border: 2px solid #d1fae5;">
                                            <i class="fas fa-shipping-fast" style="color: #10b981; font-size: 1.25rem;"></i>
                                        </div>
                                        <span style="font-size: 0.75rem; font-weight: 800; color: #475569;">${lang === 'ar' ? 'توصيل سريع' : 'Livraison Rapide'}</span>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>

                    <div id="purchase-toast" class="purchase-notification" style="border-radius: 1.25rem; box-shadow: var(--shadow-lg); padding: 1.25rem; border: 1px solid #e2e8f0;">
                        <div style="width: 60px; height: 60px; border-radius: 1.25rem; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 2rem;">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div style="text-align: ${lang === 'ar' ? 'right' : 'left'};">
                            <p style="font-weight: 900; margin: 0; font-size: 1.1rem; color: #1e293b;"><span id="toast-name">محمد</span> ${t.from} <span id="toast-city">الجزائر</span></p>
                            <p style="color: #64748b; margin: 0; font-size: 0.9rem; font-weight: 600;">${t.purchased}</p>
                        </div>
                    </div>
                `;

                // Re-attach listeners for newly added elements
                let qty = 1;
                const qtyDisplay = document.getElementById('qty-display');
                const qtyInput = document.getElementById('qty-input');
                const totalPriceElem = document.getElementById('total-price');

                document.getElementById('qty-plus').onclick = () => { qty++; updateUI(); };
                document.getElementById('qty-minus').onclick = () => { if (qty > 1) { qty--; updateUI(); } };

                function updateUI() {
                    qtyDisplay.innerText = qty;
                    qtyInput.value = qty;
                    totalPriceElem.innerText = `DA ${product.price * qty}`;
                }

                // Abandoned Cart Capture Logic
                const form = document.getElementById('order-form');
                const phoneInput = form.querySelector('[name="phone"]');
                const nameInput = form.querySelector('[name="fullName"]');
                const citySelect = form.querySelector('[name="city"]');

                let abandonTimeout;
                const captureAbandon = () => {
                    clearTimeout(abandonTimeout);
                    abandonTimeout = setTimeout(async () => {
                        const phone = phoneInput.value;
                        if (phone.length >= 8) { // Capture only if it looks like a real number
                            try {
                                await fetch('/api/abandoned', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        phone: phone,
                                        fullName: nameInput.value,
                                        city: citySelect.value,
                                        productId: productId
                                    })
                                });
                            } catch (e) { /* silent fail */ }
                        }
                    }, 2000); // 2 second debounce
                };

                phoneInput.oninput = captureAbandon;
                nameInput.oninput = captureAbandon;
                citySelect.onchange = captureAbandon;


                // Load Related Products
                // loadRelatedProducts(lang);
            } catch (err) {
                document.getElementById('product-detail').innerHTML = `<p style="text-align:center;">${err.message}</p>`;
            }
        }

        async function loadRelatedProducts(lang) {
            try {
                const response = await fetch('/api/products');
                const products = await response.json();
                const t = translations[lang];

                document.getElementById('related-title').innerText = t.relatedTitle;
                document.getElementById('related-title').style.textAlign = lang === 'ar' ? 'right' : 'left';
                document.getElementById('related-products-grid').style.direction = translations[lang].dir;

                // Simple "related" logic: take first 4 items excluding current
                const related = products.filter(p => p.id !== productId).slice(0, 4);

                document.getElementById('related-products-grid').innerHTML = related.map(p => `
                    <div class="product-card">
                        <img src="${p.image}" alt="${p.title}">
                        <div class="product-info">
                            <h3>${p.title}</h3>
                            <p class="price">${p.price} DA</p>
                            <a href="product.php?id=${p.id}" class="btn-order" style="text-decoration:none;">${t.viewProduct}</a>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                console.error("Failed to load related products:", err);
            }
        }

        // Shared Logic

        const names_ar = ['أمين', 'سارة', 'كمال', 'ياسين', 'إيمان', 'عمر'];
        const cities_ar = ['الجزائر', 'وهران', 'البليدة', 'قسنطينة', 'سطيف', 'تلمسان'];
        const names_fr = ['Amine', 'Sarah', 'Kamel', 'Yacine', 'Imane', 'Omar'];
        const cities_fr = ['Alger', 'Oran', 'Blida', 'Constantine', 'Setif', 'Tlemcen'];

        setInterval(() => {
            const toast = document.getElementById('purchase-toast');
            if (toast) {
                const names = currentLang === 'ar' ? names_ar : names_fr;
                const cities = currentLang === 'ar' ? cities_ar : cities_fr;
                document.getElementById('toast-name').innerText = names[Math.floor(Math.random() * names.length)];
                document.getElementById('toast-city').innerText = cities[Math.floor(Math.random() * cities.length)];
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 4000);
            }
        }, 12000);

        document.getElementById('lang-switcher').addEventListener('click', (e) => {
            if (e.target.dataset.lang) {
                loadProductDetail(e.target.dataset.lang);
            }
        });

        loadProductDetail('ar');
        // Track page view
        fetch('/api/stats.php', { method: 'POST' }).catch(() => { });

        // Sticky Button Logic
        const stickyBtn = document.createElement('button');
        stickyBtn.className = 'sticky-order-btn';
        stickyBtn.id = 'sticky-order-trigger';
        stickyBtn.innerHTML = ` اضغط هنا للطلب <i class="fas fa-hand-pointer"></i>`;
        document.body.appendChild(stickyBtn);

        window.addEventListener('scroll', () => {
            const form = document.querySelector('.cod-form');
            if (form) {
                const rect = form.getBoundingClientRect();
                // Show button if form is not in viewport
                if (rect.top > window.innerHeight || rect.bottom < 0) {
                    stickyBtn.classList.add('show');
                } else {
                    stickyBtn.classList.remove('show');
                }
            }
        });

        stickyBtn.onclick = () => {
            const form = document.querySelector('.cod-form');
            if (form) {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        };

        // Form Submission Logic
        document.addEventListener('submit', async (e) => {
            if (e.target.id === 'order-form') {
                e.preventDefault();
                const t = translations[currentLang];
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());
                data.productId = productId;
                data.totalPrice = parseInt(document.getElementById('total-price').innerText);

                const btn = e.target.querySelector('button');
                btn.disabled = true;
                btn.innerText = t.submitting;

                try {
                    const res = await fetch('/api/orders', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    if (res.ok) {
                        window.location.href = `thanks.php?lang=${currentLang}`;
                    } else { throw new Error('Failed'); }
                } catch (err) {
                    alert(t.orderError);
                    btn.disabled = false;
                    btn.innerText = t.submitBtn;
                }
            }
        });
    </script>
</body>

</html>