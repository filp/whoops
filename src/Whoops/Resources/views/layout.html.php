<?php
/**
* Layout template file for Whoops's pretty error output.
*/
?>
<!DOCTYPE html><?php echo $preface; ?>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $tpl->escape($page_title) ?></title>

    <style><?php echo $stylesheet ?></style>
  </head>
  <body>

    <div class="Whoops container">
      <div class="stack-container">
        <div class="panel left-panel cf <?php echo (!$has_frames ? 'empty' : '') ?>">
          <header>
            <?php $tpl->render($header) ?>
          </header>

          <div class="frames-description <?php echo $has_frames_tabs ? 'frames-description-application' : '' ?>">
            <?php if ($has_frames_tabs): ?>
              <?php if ($active_frames_tab == 'application'): ?>
                <a href="#" id="application-frames-tab" class="frames-tab frames-tab-active">
                  Application frames (<?php echo $frames->countIsApplication() ?>)
                </a>
              <?php else: ?>
                <span href="#" id="application-frames-tab" class="frames-tab">
                  Application frames (<?php echo $frames->countIsApplication() ?>)
                </span>
              <?php endif; ?>
              <a href="#" id="all-frames-tab" class="frames-tab <?php echo $active_frames_tab == 'all' ? 'frames-tab-active' : '' ?>">
                All frames (<?php echo count($frames) ?>)
              </a>
            <?php else: ?>
              <span>
                  Stack frames (<?php echo count($frames) ?>)
              </span>
            <?php endif; ?>
          </div>

          <div class="frames-container <?php echo $active_frames_tab == 'application' ? 'frames-container-application' : '' ?>">
            <?php $tpl->render($frame_list) ?>
          </div>
        </div>
        <div class="panel details-container cf">
          <?php $tpl->render($frame_code) ?>
          <?php $tpl->render($env_details) ?>
        </div>
      </div>
    </div>

    <script><?php echo $zepto ?></script>
    <script><?php echo $clipboard ?></script>
    <script><?php echo $javascript ?></script>
  </body>
</html>
