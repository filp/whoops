<?php
/**
* Layout template file for Whoops's pretty error output.
*/
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $tpl->escape($page_title) ?></title>

    <style><?php echo $stylesheet ?></style>
  </head>
  <body>

    <div class="container">

      <div class="stack-container">
        <div class="frames-container cf <?php echo (!$v->hasFrames ? 'empty' : '') ?>">
          <?php $tpl->render($frame_list, array( "v" => $v ) ) ?>
        </div>
        <div class="details-container cf">
          <header>
            <?php $tpl->render($header, array( "v" => $v )) ?>
          </header>
          <?php $tpl->render($frame_code, array( "v" => $v )) ?>
          <?php $tpl->render($env_details, array( "v" => $v)) ?>
        </div>
      </div>
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script><?php echo $javascript ?></script>
  </body>
</html>
