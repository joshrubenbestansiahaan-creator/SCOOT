<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>SCOOT</title>
    
    <!-- Script Theme Preference (Dark/Light/Night) -->
    <script>
    (function(){
        try {
            var m = localStorage.scoot_theme || "light";
            if (m === "auto") m = matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            document.documentElement.dataset.theme = m;
        } catch(e) {}
    })();
    </script>
    
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#B8E5C8">
    <link rel="apple-touch-icon" href="assets/img/icon-192.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/vendor/leaflet/leaflet.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body></body>