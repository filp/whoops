Zepto(function($) {
  prettyPrint();

  var $frameContainer = $('.frames-container');
  var $container      = $('.details-container');
  var $activeLine     = $frameContainer.find('.frame.active');
  var $activeFrame    = $container.find('.frame-code.active');
  var headerHeight    = $('header').height();

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

  $frameContainer.on('click', '.frame', function() {
    var $this  = $(this);
    var id     = /frame\-line\-([\d]*)/.exec($this.attr('id'))[1];
    var $codeFrame = $('#frame-code-' + id);

    if ($codeFrame) {
      $activeLine.removeClass('active');
      $activeFrame.removeClass('active');

      $this.addClass('active');
      $codeFrame.addClass('active');

      $activeLine  = $this;
      $activeFrame = $codeFrame;

      highlightCurrentLine();

      $container.scrollTop(headerHeight);
    }
  });
  
  if (typeof ZeroClipboard !== "undefined") {
	  ZeroClipboard.config({
		  moviePath: '//ajax.cdnjs.com/ajax/libs/zeroclipboard/1.3.5/ZeroClipboard.swf',
	  });

	  var clipEl = document.getElementById("copy-button");
	  var clip = new ZeroClipboard( clipEl );
	  var $clipEl = $(clipEl);

	  // show the button, when swf could be loaded successfully from CDN
	  clip.on("load", function() {
		  $clipEl.show();
	  });
  }
});
