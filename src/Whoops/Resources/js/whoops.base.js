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

  var $frameContainer = $('.frames-container');
  var $container      = $('.details-container');
  var $activeLine     = $frameContainer.find('.frame.active');
  var $activeFrame    = $container.find('.frame-code.active');
  var $ajaxEditors    = $('.editor-link[data-ajax]');
  var headerHeight    = $('header').height();

  // load prettify asyncronously to speed up page rendering
  getScript('//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.js', function () {
    renderCurrentCodeblock();
  });

  /*
   * add prettyprint classes to our current active codeblock
   * run prettyPrint() to highlight the active code
   * scroll to the line when prettyprint is done
   * highlight the current line
   */
  var renderCurrentCodeblock = function(id) {

    // remove previous codeblocks so we only render the active one
    $('.code-block').removeClass('prettyprint');

    // pass the id in when we can for speed
    if (typeof(id) === 'undefined' || typeof(id) === 'object') {
      var id = /frame\-line\-([\d]*)/.exec($activeLine.attr('id'))[1];
    }

    $('#frame-code-linenums-' + id).addClass('prettyprint');
    $('#frame-code-args-' + id).addClass('prettyprint');

    prettyPrint(highlightCurrentLine);

  }

  /*
   * Highlight the active and neighboring lines for the current frame
   * Adjust the offset to make sure that line is veritcally centered
   */

  var highlightCurrentLine = function() {
    var activeLineNumber = +($activeLine.find('.frame-line').text());
    var $lines           = $activeFrame.find('.linenums li');
    var firstLine        = +($lines.first().val());

    var $offset = $($lines[activeLineNumber - firstLine - 10]);
    if ($offset.length > 0) {
      $offset[0].scrollIntoView();
    }

    $($lines[activeLineNumber - firstLine - 1]).addClass('current');
    $($lines[activeLineNumber - firstLine]).addClass('current active');
    $($lines[activeLineNumber - firstLine + 1]).addClass('current');

    $container.scrollTop(0);

  }

  /*
   * click handler for loading codeblocks 
   */

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

      renderCurrentCodeblock(id);

    }

  });

  var clipboard = new Clipboard('.clipboard');
  var showTooltip = function(elem, msg) {
    elem.setAttribute('class', 'clipboard tooltipped tooltipped-s');
    elem.setAttribute('aria-label', msg);
  };

  clipboard.on('success', function(e) {
      e.clearSelection();

      showTooltip(e.trigger, 'Copied!');
  });

  clipboard.on('error', function(e) {
      showTooltip(e.trigger, fallbackMessage(e.action));
  });

  var btn = document.querySelector('.clipboard');

  btn.addEventListener('mouseleave', function(e) {
    e.currentTarget.setAttribute('class', 'clipboard');
    e.currentTarget.removeAttribute('aria-label');
  });

  function fallbackMessage(action) {
    var actionMsg = '';
    var actionKey = (action === 'cut' ? 'X' : 'C');

    if (/Mac/i.test(navigator.userAgent)) {
        actionMsg = 'Press ⌘-' + actionKey + ' to ' + action;
    } else {
        actionMsg = 'Press Ctrl-' + actionKey + ' to ' + action;
    }

    return actionMsg;
  }

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

  // Symfony VarDumper: Close the by default expanded objects
  $('.sf-dump-expanded')
    .removeClass('sf-dump-expanded')
    .addClass('sf-dump-compact');
  $('.sf-dump-toggle span').html('&#9654;');
});
