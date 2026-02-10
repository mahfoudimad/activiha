<?php
function getTrackingScripts($db, $pageType = 'page') {
    $marketing = $db->get('marketing');
    if (!$marketing) return '';

    $scripts = "<!-- Marketing Tracking Scripts -->\n";

    // Google Search Console
    if (!empty($marketing['googleSearchConsole'])) {
        $gsc = $marketing['googleSearchConsole'];
        if (strpos($gsc, '<meta') === false) {
            $scripts .= '<meta name="google-site-verification" content="' . htmlspecialchars($gsc) . '" />' . "\n";
        } else {
            $scripts .= $gsc . "\n";
        }
    }

    // Google Analytics
    if (!empty($marketing['googleAnalytics'])) {
        $gaId = $marketing['googleAnalytics'];
        $scripts .= "<!-- Google Analytics -->\n";
        $scripts .= "<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . htmlspecialchars($gaId) . "\"></script>\n";
        $scripts .= "<script>\n  window.dataLayer = window.dataLayer || [];\n  function gtag(){dataLayer.push(arguments);}\n  gtag('js', new Date());\n  gtag('config', '" . htmlspecialchars($gaId) . "');\n</script>\n";
    }

    // Facebook Pixel
    if (!empty($marketing['fbPixel'])) {
        $fbId = $marketing['fbPixel'];
        $scripts .= "<!-- Facebook Pixel -->\n";
        $scripts .= "<script>\n!function(f,b,e,v,n,t,s)\n{if(f.fbq)return;n=f.fbq=function(){n.callMethod?\nn.callMethod.apply(n,arguments):n.queue.push(arguments)};\nif(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';\nn.queue=[];t=b.createElement(e);t.async=!0;\nt.src=v;s=b.getElementsByTagName(e)[0];\ns.parentNode.insertBefore(t,s)}(window, document,'script',\n'https://connect.facebook.net/en_US/fbevents.js');\nfbq('init', '" . htmlspecialchars($fbId) . "');\nfbq('track', 'PageView');\n";
        
        if ($pageType === 'purchase') {
            $scripts .= "fbq('track', 'Purchase', {currency: 'DZD', value: 0.00});\n";
        }
        
        $scripts .= "</script>\n";
    }

    // TikTok Pixel
    if (!empty($marketing['tiktokPixel'])) {
        $ttId = $marketing['tiktokPixel'];
        $scripts .= "<!-- TikTok Pixel -->\n";
        $scripts .= "<script>\n!function (w, d, t) {\n  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=[\"page\",\"track\",\"identify\",\"instances\",\"debug\",\"on\",\"off\",\"once\",\"ready\",\"alias\",\"group\",\"enableCookie\",\"setCookie\"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i=\"https://analytics.tiktok.com/i18n/pixel/events.js\";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n;var o=d.createElement(\"script\");o.type=\"text/javascript\",o.async=!0,o.src=i+\"?sdkid=\"+e+\"&lib=\"+t;var a=d.getElementsByTagName(\"script\")[0];a.parentNode.insertBefore(o,a)};\n  ttq.load('" . htmlspecialchars($ttId) . "');\n  ttq.page();\n}(window, document, 'ttq');\n</script>\n";
    }

    return $scripts;
}
