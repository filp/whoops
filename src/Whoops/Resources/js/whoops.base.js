Zepto(function($) {
  // a jQuery.getScript() equivalent to asyncronously load javascript files
  // credits to http://stackoverflow.com/a/8812950/1597388
  var getScript = function(src, func) {
    var script = document.createElement('script');
    script.async = 'async';
    script.src = src;
    if (func) {
      script.onload = func;
    }
    document.getElementsByTagName('head')[0].appendChild( script );
  };

  // load prettify asyncronously to speedup page rendering
  getScript('//cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.js', function() {
    prettyPrint();
  });

  var $frameContainer = $('.frames-container');
  var $container      = $('.details-container');
  var $activeLine     = $frameContainer.find('.frame.active');
  var $activeFrame    = $container.find('.frame-code.active');
  var $ajaxEditors    = $('.editor-link[data-ajax]');
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
	  var $clipHelpEl = $("#help-clipboard");

	  // show the button, when swf could be loaded successfully from CDN
	  clip.on("load", function() {
		  $clipEl.show();
		  $clipHelpEl.show();
	  });
  }
  $(document).on('click', '.Whoops-hider', function () {
	  var $container = $('.Whoops.container');
	  if (!$container.hasClass('hidden')) {
		  $container.addClass('hidden');
		  $(this)
			  .addClass('closed')
			  .prop('title', 'return to Whoops')
		  ;
	  } else {
		  $container.removeClass('hidden');
		  $(this)
			  .removeClass('closed')
			  .prop('title', 'view raw script output')
		  ;
	  }
  });
  $(document).on('keydown', function(e) {
	  if(e.ctrlKey) {
		  // CTRL+Arrow-UP/Arrow-Down support:
		  // 1) select the next/prev element 
		  // 2) make sure the newly selected element is within the view-scope
		  // 3) focus the (right) container, so arrow-up/down (without ctrl) scroll the details
		  if (e.which === 38 /* arrow up */) {
			  $activeLine.prev('.frame').click();
			  $activeLine[0].scrollIntoView();
			  $container.focus();
			  e.preventDefault();
		  } else if (e.which === 40 /* arrow down */) {
			  $activeLine.next('.frame').click();
			  $activeLine[0].scrollIntoView();
			  $container.focus();
			  e.preventDefault();
		  }
	  } 
  });
  
  // Avoid to quit the page with some protocol (e.g. IntelliJ Platform REST API)
  $ajaxEditors.on('click', function(e){
    e.preventDefault();
    $.get(this.href);
  });
});
