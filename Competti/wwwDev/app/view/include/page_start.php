<?php

$pageTitle = isset($pageTitle) ? $pageTitle : 'COMPETTI';
$pageCss   = isset($pageCss) ? $pageCss : ['pages.css'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - COMPETTI</title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/navigation.css">
    <link rel="stylesheet" href="/css/footer.css">
<?php foreach ($pageCss as $hoja): ?>
    <link rel="stylesheet" href="/css/<?= e($hoja) ?>">
<?php endforeach; ?>
</head>

<body>

    <?php require_once VIEW_PATH . '/include/nav.php'; ?>
