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

  <div class="help">
    <span title="show help">?</button>

    <div id="help-overlay">
      <div id="help-framestack">Callstack information; navigate with mouse or keyboard using <kbd>Ctrl+&uparrow;</kbd> or <kbd>Ctrl+&downarrow;</kbd></div>
      <div id="help-clipboard">Copy-to-clipboard button</div>
      <div id="help-exc-message">Exception message and its type</div>
      <div id="help-code">Code snippet where the error was thrown</div>
      <div id="help-request">Server state information</div>
      <div id="help-appinfo">Application provided context information</div>
    </div>
  </div>

  <button id="copy-button" class="clipboard" data-clipboard-target="plain-exception" title="copy exception into clipboard"></button>
  <span id="plain-exception"><?php echo $tpl->escape($plain_exception) ?></span>

  <p class="exc-message">
    <?php if (!empty($message)): ?>
      <?php echo $tpl->escape($message) ?>
    <?php else: ?>
      <span class="exc-message-empty-notice">No message</span>
    <?php endif ?>
  </p>
</div>
