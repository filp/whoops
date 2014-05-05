<?php
/**
* Template file for Whoops's pretty error output.
* Check the $v global variable (stdClass) for what's available
* to work with.
* @var stdClass $v
* @var callable $e
* @var callable $slug
*/
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $tpl->escape($page_title) ?></title>

    <style><?php echo $stylesheet ?></style>
  </head>
  <body class="development">

	<div class="details-container cf">
	  <header>
		<?php $tpl->render($header) ?>
	  </header>
	</div>

	<div class="frames-container cf <?php echo (!$has_frames ? 'empty' : '') ?>">
	  <?php $tpl->render($frame_list) ?>
	</div>


  </body>
</html>
