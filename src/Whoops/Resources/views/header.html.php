<div class="exception">
  <h3 class="exc-title">
    <?php foreach($name as $i => $nameSection): ?>
      <?php if($i == count($name) - 1): ?>
        <span class="exc-title-primary"><?php echo $tpl->escape($nameSection) ?></span>
      <?php else: ?>
        <?php echo $tpl->escape($nameSection) . ' \\' ?>
      <?php endif ?>
    <?php endforeach ?>
    <?php if($code): ?>
      <span title="Exception Code">(<?php echo $tpl->escape($code) ?>)</span>
    <?php endif ?>
    <button id="copy-button" class="clipboard" data-clipboard-target="plain-exception" title="copy exception into clipboard"></button>
    <span id="plain-exception"><?php echo $tpl->escape($plain_exception) ?></span>
  </h3>
  <p class="exc-message">
    <?php echo $tpl->escape($message) ?>
  </p>
</div>
