<div class="exception">
  <div class="exc-title">
    <?php foreach ($name as $i => $nameSection): ?>
      <?php if ($i == count($name) - 1): ?>
        <span class="exc-title-primary"><?php echo $tpl->escape($nameSection) ?></span>
      <?php else: ?>
        <?php echo $tpl->escape($nameSection) . ' \\' ?>
      <?php endif ?>
    <?php endforeach ?>
    <?php if ($code): ?>
      <span title="Exception Code">(<?php echo $tpl->escape($code) ?>)</span>
    <?php endif ?>
  </div>

  <div class="exc-message">
    <?php if (!empty($message)): ?>
      <span><?php echo $tpl->escape($message) ?></span>
    <?php else: ?>
      <span class="exc-message-empty-notice">No message</span>
    <?php endif ?>

    <ul class="search-for-help">
      <li><a target="_blank" href="https://google.com/search?q=<?php echo urlencode(implode('\\', $name).' '.$message) ?>" title="Search for help on Google."><i class="google"></i></a></li>
      <li><a target="_blank" href="https://stackoverflow.com/search?q=<?php echo urlencode(implode('\\', $name).' '.$message) ?>" title="Search for help on Stack Overflow."><i class="stackoverflow"></i></a></li>
    </ul>

    <span id="plain-exception"><?php echo $tpl->escape($plain_exception) ?></span>
    <button id="copy-button" class="clipboard" data-clipboard-text="<?php echo $tpl->escape($plain_exception) ?>" title="Copy exception details to clipabord">
      COPY
    </button>
  </div>
</div>
