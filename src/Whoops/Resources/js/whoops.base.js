$(function() {
  prettyPrint();

  var $frameLines  = $('[id^="frame-line-"]');
  var $activeLine  = $('.frames-container .active');
  var $activeFrame = $('.active[id^="frame-code-"]').show();
  var $container   = $('.details-container');
  var headerHeight = $('header').css('height');

  var highlightCurrentLine = function() {
    // Highlight the active and neighboring lines for this frame:
    var activeLineNumber = +($activeLine.find('.frame-line').text());
    var $lines           = $activeFrame.find('.linenums li');
    var firstLine        = +($lines.first().val());

    $($lines[activeLineNumber - firstLine - 1]).addClass('current');
    $($lines[activeLineNumber - firstLine]).addClass('current active');
    $($lines[activeLineNumber - firstLine + 1]).addClass('current');
  }

  // Highlight the active for the first frame:
  highlightCurrentLine();

  $frameLines.click(function() {
    var $this  = $(this);
    var id     = /frame\-line\-([\d]*)/.exec($this.attr('id'))[1];
    var $codeFrame = $('#frame-code-' + id);

    if($codeFrame) {
      $activeLine.removeClass('active');
      $activeFrame.removeClass('active');

      $this.addClass('active');
      $codeFrame.addClass('active');

      $activeLine  = $this;
      $activeFrame = $codeFrame;

      highlightCurrentLine();

      $container.animate({ scrollTop: headerHeight }, "fast");
    }
  });
});
