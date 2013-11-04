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
    <title><?php echo $e($v->title) ?></title>

    <style><?php echo $v->pageStyle ?></style>
  </head>
  <body>

	<h2><?php echo implode('\\', $v->name); ?></h2>
	<?php echo $v->message; ?>


	<div class="data-table-container" id="data-tables">
	  <?php foreach($v->tables as $label => $data): ?>
		<div class="data-table" id="sg-<?php echo $e($slug($label)) ?>">
		  <label><?php echo $e($label) ?></label>
		  <?php if(!empty($data)): ?>
			  <table class="data-table">
				<thead>
				  <tr>
					<td class="data-table-k">Key</td>
					<td class="data-table-v">Value</td>
				  </tr>
				</thead>
			  <?php foreach($data as $k => $value): ?>
				<tr>
				  <td><?php echo $e($k) ?></td>
				  <td><?php echo $e(print_r($value, true)) ?></td>
				</tr>
			  <?php endforeach ?>
			  </table>
		  <?php else: ?>
			<span class="empty">empty</span>
		  <?php endif ?>
		</div>
	  <?php endforeach ?>
	</div>

  </body>
</html>
