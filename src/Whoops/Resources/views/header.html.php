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
      <li>
        <a target="_blank" href="https://google.com/search?q=<?php echo urlencode(implode('\\', $name).' '.$message) ?>" title="Search for help on Google.">
          <!-- Google icon by Alfredo H, from https://www.iconfinder.com/alfredoh -->
          <!-- Creative Commons (Attribution 3.0 Unported) -->
          <!-- http://creativecommons.org/licenses/by/3.0/ -->
          <svg class="google" height="16" viewBox="0 0 512 512" width="16" xmlns="http://www.w3.org/2000/svg">
            <path d="M457.732 216.625c2.628 14.04 4.063 28.743 4.063 44.098C461.795 380.688 381.48 466 260.205 466c-116.024 0-210-93.977-210-210s93.976-210 210-210c56.703 0 104.076 20.867 140.44 54.73l-59.205 59.197v-.135c-22.046-21.002-50-31.762-81.236-31.762-69.297 0-125.604 58.537-125.604 127.84 0 69.29 56.306 127.97 125.604 127.97 62.87 0 105.653-35.966 114.46-85.313h-114.46v-81.902h197.528z"/>
          </svg>
        </a>
      </li>
      <li>
        <a target="_blank" href="https://stackoverflow.com/search?q=<?php echo urlencode(implode('\\', $name).' '.$message) ?>" title="Search for help on Stack Overflow.">
          <!-- Stack Overflow icon by Picons.me, from https://www.iconfinder.com/Picons -->
          <!-- Free for commercial use -->
          <svg class="stackoverflow" height="16" viewBox="-1163 1657.697 56.693 56.693" width="16" xmlns="http://www.w3.org/2000/svg">
            <path d="M-1126.04 1689.533l-16.577-9.778 2.088-3.54 16.578 9.778zM-1127.386 1694.635l-18.586-4.996 1.068-3.97 18.586 4.995zM-1127.824 1700.137l-19.165-1.767.378-4.093 19.165 1.767zM-1147.263 1701.293h19.247v4.11h-19.247z"/>
            <path d="M-1121.458 1710.947s0 .96-.032.96v.016h-30.796s-.96 0-.96-.016h-.032v-20.03h3.288v16.805h25.244v-16.804h3.288v19.07zM-1130.667 1667.04l10.844 15.903-3.396 2.316-10.843-15.903zM-1118.313 1663.044l3.29 18.963-4.05.703-3.29-18.963z"/>
          </svg>
        </a>
      </li>
    </ul>

    <span id="plain-exception"><?php echo $tpl->escape($plain_exception) ?></span>
    <button id="copy-button" class="clipboard" data-clipboard-text="<?php echo $tpl->escape($plain_exception) ?>" title="Copy exception details to clipboard">
      COPY
    </button>
  </div>
</div>
