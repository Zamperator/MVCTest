<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $this->pageTitle; ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->renderSection('header'); ?>
</head>
<body>
<header>
    <nav>...</nav>
</header>

<main>
    <?= $this->renderBody(); ?>
</main>

<footer>
    <?php $this->renderSection('footer'); ?>
</footer>
</body>
</html>