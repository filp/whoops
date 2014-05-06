(function (document) {
    prettyPrint();

    var frameContainer = document.querySelector('.frames-container'),
        container = document.querySelector('.details-container'),
        activeLine = frameContainer.querySelector('.frame.active'),
        activeFrame = document.querySelector('.frame-code.active'),
        headerHeight = Math.round(document.querySelector('header').getBoundingClientRect().height);

    function init() {
        // Highlight the active for the first frame:
        highlightCurrentLine();

        var tabs = frameContainer.querySelectorAll('.frame');
        for (var i = 0; i < tabs.length; ++i) {
            _addEvent(tabs[i], 'click', onFrameClick);
        }
    }

    function highlightCurrentLine() {
        // Highlight the active and neighboring lines for this frame:
        var activeLineNumber = +(activeLine.querySelector('.frame-line').textContent),
            lines = activeFrame.querySelectorAll('.linenums li'),
            firstLine = +(lines[0].value);

        _addClass(lines[activeLineNumber - firstLine - 1], 'current');
        _addClass(lines[activeLineNumber - firstLine], 'current active');
        _addClass(lines[activeLineNumber - firstLine + 1], 'current')
    }

    function onFrameClick() {
        var id = /frame\-line\-([\d]*)/.exec(this.id)[1];
        var codeFrame = document.getElementById('frame-code-' + id);

        if (codeFrame) {
            _removeClass(activeLine, 'active');
            _removeClass(activeFrame, 'active');

            _addClass(this, 'active');
            _addClass(codeFrame, 'active');

            activeLine = this;
            activeFrame = codeFrame;

            highlightCurrentLine();

            _scrollTo(container, headerHeight);
        }
    }

    function _scrollTo(element, value) {
        if ('scrollTop' in element) {
            element.scrollTop = value
        } else {
            element.scrollTo(element.scrollX, value)
        }
    }

    //http://www.bigbold.com/snippets/posts/show/2630
    function _addClass(objElement, strClass) {
        if (!objElement.className) return objElement.className = strClass;

        if ((new RegExp('(^|\s)' + strClass + '(\s|$)')).exec(objElement.className)) return;

        var arrList = objElement.className.split(' ');
        arrList[arrList.length] = strClass;
        objElement.className = arrList.join(' ');
    }

    //http://www.bigbold.com/snippets/posts/show/2630
    function _removeClass(objElement, strClass) {
        if (objElement.className) {
            var arrList = objElement.className.split(' '), strClassUpper = strClass.toUpperCase();
            for (var i = 0; i < arrList.length; i++) {
                if (arrList[i].toUpperCase() == strClassUpper) {
                    arrList.splice(i, 1);
                    i--;
                }
            }
            objElement.className = arrList.join(' ');
        }
    }

    //http://ejohn.org/projects/flexible-javascript-events/
    function _addEvent(obj, type, fn) {
        if (obj.attachEvent) {
            obj["e" + type + fn] = fn;
            obj[type + fn] = function () {
                obj["e" + type + fn](window.event);
            };
            obj.attachEvent("on" + type, obj[type + fn]);
        } else {
            obj.addEventListener(type, fn, false);
        }
    }

    init();
})(document);
