<?php $this->layout = '~/Views/layout.php'; ?>

<div id="xyz">
    Profile of (<?= $this->getVar('id') ?>) <?= $this->getVar('username') ?>
</div>