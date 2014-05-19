<?php /* List file names & line numbers for all stack frames;
         clicking these links/buttons will display the code view
         for that particular frame */ ?>
<button id="copy-button" data-clipboard-target="plain-stacktrace" title="click to copy the exception and stacktrace into the clipboard">
copy into clipboard
</button>
<span id="plain-stacktrace"><?php 
foreach($name as $i => $nameSection):
  if($i == count($name) - 1):
    echo $tpl->escape($nameSection);
  else: 
    echo $tpl->escape($nameSection) . '\\';
  endif;
endforeach;

echo ' thrown with message "';
echo $tpl->escape($message);
echo '"'."\n\n";

echo "Stacktrace:\n";
foreach($frames as $i => $frame):
    echo "#". (count($frames) - $i - 1). " ";
    echo $tpl->escape($frame->getClass() ?: '');
    echo ($frame->getClass() && $frame->getFunction()) ? ":" : "";
    echo $tpl->escape($frame->getFunction() ?: '');
    echo ' in '; 
    echo ($frame->getFile() ?: '<#unknown>');
    echo ':';
    echo (int) $frame->getLine(). "\n";
endforeach; ?></span>

<?php foreach($frames as $i => $frame): ?>
  <div class="frame <?php echo ($i == 0 ? 'active' : '') ?>" id="frame-line-<?php echo $i ?>">
      <div class="frame-method-info">
        <span class="frame-index"><?php echo (count($frames) - $i - 1) ?>.</span>
        <span class="frame-class"><?php echo $tpl->escape($frame->getClass() ?: '') ?></span>
        <span class="frame-function"><?php echo $tpl->escape($frame->getFunction() ?: '') ?></span>
      </div>

    <span class="frame-file">
      <?php echo ($frame->getFile(true) ?: '<#unknown>') ?><!--
   --><span class="frame-line"><?php echo (int) $frame->getLine() ?></span>
    </span>
  </div>
<?php endforeach ?>
