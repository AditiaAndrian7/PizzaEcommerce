<?php
session_start();

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slice Heaven</title>
  <link rel="icon" type="image/png" href="/assets/img/ui/icon.jpg">

  <link rel="stylesheet" href="style.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap"
    rel="stylesheet"
  >
</head>
<body>
<?php require_once __DIR__ . '/routes/auto-cancelled.php'; ?>
<?php include 'layout/header/header.php'; ?>

<section class="auto-slider">
  <div class="slider-bg"></div>
  <div class="slide-container"></div>
  <div class="text-container">
    <div class="title">Product Terlaris</div>
    <h1 class="product-title"></h1>
    <p class="product-desc"></p>
    <a href="/products/products.php" class="cta-button">Beli Sekarang</a>
  </div>
</section>

<?php include 'layout/footer/footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
