<?php
	require_once 'engine/init.php';
?>
<!doctype html>

<html lang=<?= $init_class->get_from_config("configuration", "lang") ?>>
<head>
  <?= $init_class->construct_charset() ?>

  <title><?= $init_class->get_from_config("name", "title") ?></title>

  <?= $init_class->construct_metatags() ?>
  <?= $init_class->construct_bootstrap_css() ?>
  <?= $init_class->construct_favicon() ?>

  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
  <![endif]-->

  <?= $init_class->construct_tempalte("head") ?>
</head>

<body>
	<div class="container">
		<?= $init_class->construct_tempalte("body") ?>
		<?= $init_class->construct_tempalte("footer") ?>

	</div>
  <?= $init_class->construct_bootstrap_js() ?>
  <?= $init_class->construct_tempalte("scripts") ?>
</body>
</html>