<?php
include 'header.php';
?>
<div id="wrapper" class="flex glass">
  <aside class="panel flex">
    <div>
      <a href="./" title="<?= t('backtoindex') ?>"><i class="icon"><?= icon('home') ?></i></a>
    </div>
    <div class="right">
<?php
if (!empty($_SESSION['loggedin']))
  include 'form_logout.php';
?>
      <select class="inline" id="locale">
      </select>
    </div>
  </aside>
  <main class="panel glass" id="">
    <h1><?= $title ?></h1>

<?= $htmlContent ?>

  </main>
</div>
<?php
include 'footer.php';

