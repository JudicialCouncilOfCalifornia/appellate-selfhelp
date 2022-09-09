/*! Lazy Load XT v2.0.0 2019-05-12
 * http://ressio.github.io/lazy-load-xt
 * (C) 2013-2019 RESSIO
 * Licensed under MIT */

(function (window, document, undefined) {
    // options
    var lazyLoadXT = 'lazyLoadXT',
        dataLazied = lazyLoadXT + '_lazied',
        load_error = 'load error',
        classLazyHidden = 'lazy-hidden',
        docElement = document.documentElement || document.body,
        //  force load all images in Opera Mini and some mobile browsers without scroll event or getBoundingClientRect()
        forceLoad = (window.onscroll === undefined || !!window.operamini || !docElement.getBoundingClientRect),
        options = {
            autoInit: true, // auto initialize in ready()
            preload: true, // preload images out of visible area
            selector: 'img[data-src]', // selector for lazyloading elements
            blankImage: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
                      //'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg"/>'
            throttle: 99, // interval (ms) for changes check
            forceLoad: forceLoad, // force auto load all images

            loadEvent: 'pageshow', // check AJAX-loaded content in jQueryMobile
            updateEvent: 'load orientationchange resize scroll touchmove focus', // page-modified events
            forceEvent: 'lazyloadall', // force loading of all elements

            //onstart: null,
            oninit: {removeClass: 'lazy'}, // init handler
            onshow: {addClass: classLazyHidden}, // start loading handler
            onload: {removeClass: classLazyHidden, addClass: 'lazy-loaded'}, // load success handler
            onerror: {removeClass: classLazyHidden}, // error handler
            //oncomplete: null, // complete handler

            //scrollContainer: undefined,
            checkDuplicates: true
        },
        elementOptions = {
            srcAttr: 'data-src',
            edgeX: 0,
            edgeY: 0,
            visibleOnly: true
        },
        elements = [],
        topLazy = 0,
        /*
         waitingMode=0 : no setTimeout
         waitingMode=1 : setTimeout, no deferred events
         waitingMode=2 : setTimeout, deferred events
         */
        waitingMode = 0,
        numLoading = 0;


    /**
     * @param {object} target
     * @param {...object} [obj]
     * @returns {*}
     */
    function extend(target) {
        var i = 1, options, name;
        for (; i < arguments.length; i++) {
            options = arguments[i];
            for (name in options) {
                target[name] = options[name];
            }
        }
        return target;
    }


    /**
     * Return options.prop if obj.prop is undefined, otherwise return obj.prop
     * @param {*} obj
     * @param {*} prop
     * @returns *
     */
    function getOrDef(obj, prop) {
        return obj[prop] === undefined ? options[prop] : obj[prop];
    }


    /**
     * @returns {number}
     */
    function scrollTop() {
        var scroll = window.pageYOffset;
        return (scroll === undefined) ? docElement.scrollTop : scroll;
    }


    /**
     * @param {*} obj
     * @returns {boolean}
     */
    function isFunction(obj) {
        return (typeof obj === 'function');
    }


    /**
     * @param {*} obj
     * @returns {boolean}
     */
    function isString(obj) {
        return (typeof obj === 'string');
    }


    /**
     * @param {Node} a
     * @param {Node} b
     * @returns {boolean}
     */
    function contains(a, b) {
        if (b) {
            while ((b = b.parentNode)) {
                if (b === a) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Add event handler
     * @param {(string|EventTarget)} selector
     * @param {string} events
     * @param {Function} handler
     */
    function onEvent(selector, events, handler) {
        selector = isString(selector) ? document.querySelectorAll(selector) : [selector];
        events = events.split(' ');
        for (var i = 0, j; i < selector.length; i++) {
            for (j = 0; j < events.length; j++) {
                selector[i].addEventListener(events[j], handler, {capture: true, passive: true});
            }
        }
    }


    /**
     * Remove event handler
     * @param {EventTarget} el
     * @param {string} events
     * @param {Function} handler
     */
    function offEvent(el, events, handler) {
        events = events.split(' ');
        for (var i = 0; i < events.length; i++) {
            el.removeEventListener(events[i], handler);
        }
    }


    /**
     * Add or remove classes
     * @param {Element} el
     * @param {string} classes
     * @param {boolean} [remove]
     */
    function changeClass(el, classes, remove) {
        if (!classes) {
            return;
        }
        var i = 0,
            origValue = ' ' + el.className.replace(/[\t\r\n\f]/g, ' ') + ' ',
            value = origValue,
            c;
        classes = classes.match(/\S+/g) || [];
        for (; i < classes.length; i++) {
            c = classes[i] + ' ';
            if (remove) {
                value = value.replace(' ' + c, ' ');
            } else {
                if (value.indexOf(' ' + c) < 0) {
                    value += c;
                }
            }
        }
        if (origValue !== value) {
            el.className = value.slice(1, -1);
        }
    }


    /**
     * Process function/object event handler
     * @param {string} event suffix
     * @param {EventTarget} el
     */
    function triggerEvent(event, el) {
        var handler = options['on' + event];
        if (handler) {
            if (isFunction(handler)) {
                handler.call(el);
            } else {
                changeClass(el, handler.addClass);
                changeClass(el, handler.removeClass, true);
            }
        }

        handler = document.createEvent('Event');
        handler.initEvent('lazy' + event, true, true);
        el.dispatchEvent(handler);

        // queue next check as images may be resized after loading of actual file
        queueCheckLazyElements();
    }


    /**
     * Trigger onload/onerror handler
     * @param {Event} e
     */
    function triggerLoadOrError(e) {
        var target = e.currentTarget;
        numLoading--;
        if (options.preload && numLoading === 0) {
            checkLazyElements(1);
        }
        offEvent(target, load_error, triggerLoadOrError);
        triggerEvent(e.type, target);
    }


    /**
     * Load visible elements
     * @param {int} [force] loading of all elements
     */
    function checkLazyElements(force) {
        if (!elements.length) {
            return;
        }

        force = force || options.forceLoad;

        topLazy = Infinity;

        var viewportTop = scrollTop(),
            viewportHeight = window.innerHeight || docElement.clientHeight,
            viewportWidth = window.innerWidth || docElement.clientWidth,
            i,
            length;

        for (i = 0, length = elements.length; i < length; i++) {
            var el = elements[i],
                objData = el[lazyLoadXT],
                removeNode = false,
                visible = (force < 0) || (force > numLoading) || el[dataLazied] < 0,
                topEdge;

            // remove items that are not in DOM
            if (!contains(docElement, el)) {
                removeNode = true;
            } else if (visible || !objData.visibleOnly || el.offsetWidth || el.offsetHeight) {

                if (!visible) {
                    var elPos = el.getBoundingClientRect(),
                        edgeX = objData.edgeX,
                        edgeY = objData.edgeY;

                    topEdge = (elPos.top + viewportTop - edgeY) - viewportHeight;

                    visible = (topEdge <= viewportTop && elPos.bottom > -edgeY &&
                        elPos.left <= viewportWidth + edgeX && elPos.right > -edgeX);
                }

                if (visible) {
                    numLoading++;
                    onEvent(el, load_error, triggerLoadOrError);

                    triggerEvent('show', el);

                    var srcAttr = objData.srcAttr,
                        src = isFunction(srcAttr) ? srcAttr(el) : el.getAttribute(srcAttr);

                    if (src) {
                        el.src = src;
                    }

                    removeNode = true;
                } else {
                    if (topEdge < topLazy) {
                        topLazy = topEdge;
                    }
                }
            }

            if (removeNode) {
                el[dataLazied] = 0;
                elements.splice(i--, 1);
                length--;
            }
        }

        if (!length) {
            triggerEvent('complete', docElement);
        }
    }


    /**
     * Run check of lazy elements after timeout
     */
    function timeoutLazyElements() {
        if (waitingMode > 1) {
            waitingMode = 1;
            checkLazyElements();
            setTimeout(timeoutLazyElements, options.throttle);
        } else {
            waitingMode = 0;
        }
    }


    /**
     * Queue check of lazy elements because of event e
     * @param {Event} [e]
     */
    function queueCheckLazyElements(e) {
        if (!elements.length) {
            return;
        }

        // fast check for scroll event without new visible elements
        if (e && e.type === 'scroll' && e.currentTarget === window) {
            if (topLazy >= scrollTop()) {
                return;
            }
        }

        if (!waitingMode) {
            setTimeout(timeoutLazyElements, 0);
        }
        waitingMode = 2;
    }


    /**
     * Add new elements to lazy-load list:
     * window.lazyLoadXT()
     *
     * @param {object|string} [overrides] override global options
     */
    function lazyLoadXT_handler(overrides) {
        overrides = isString(overrides) ? {selector: overrides} : (overrides || {});

        var blankImage = getOrDef(overrides, 'blankImage'),
            checkDuplicates = getOrDef(overrides, 'checkDuplicates'),
            scrollContainer = getOrDef(overrides, 'scrollContainer'),
            forceShow = getOrDef(overrides, 'show'),
            elementOptionsOverrides = {},
            selector = getOrDef(overrides, 'selector'),
            elems = isString(selector) ? document.querySelectorAll(selector) : [selector],
            i,
            el,
            duplicate;

        if (scrollContainer) {
            onEvent(scrollContainer, 'scroll', queueCheckLazyElements);
        }

        for (i in elementOptions) {
            elementOptionsOverrides[i] = getOrDef(overrides, i);
        }

        for (i = 0; i < elems.length; i++) {
            el = elems[i];
            duplicate = checkDuplicates && el[dataLazied];
            el[dataLazied] = forceShow ? -1 : 1;

            // prevent duplicates
            if (!duplicate) {
                if (blankImage && el.tagName === 'IMG' && !el.src) {
                    el.src = blankImage;
                }

                // clone elementOptionsOverrides object
                el[lazyLoadXT] = extend({}, elementOptionsOverrides);

                triggerEvent('init', el);

                elements.push(el);
            }
        }
        queueCheckLazyElements();
    }


    /**
     * Initialize list of hidden elements
     */
    function initLazyElements() {
        lazyLoadXT_handler();
    }


    /**
     * Loading of all elements
     */
    function forceLoadAll() {
        checkLazyElements(-1);
    }


    /**
     * Initialization
     */
    function ready() {
        triggerEvent('start', window);

        onEvent(window, options.updateEvent, queueCheckLazyElements);
        onEvent(window, options.forceEvent, forceLoadAll);

        onEvent(document, options.updateEvent, queueCheckLazyElements);

        if (options.autoInit) {
            onEvent(window, options.loadEvent, initLazyElements);
            initLazyElements(); // standard initialization
        }
    }


    extend(options, elementOptions, window[lazyLoadXT]);
    window[lazyLoadXT] = lazyLoadXT_handler;
    options = extend(window[lazyLoadXT], options);
    extend(window[lazyLoadXT], {
        extend: extend,
        isFunction: isFunction,
        onEvent: onEvent,
        offEvent: offEvent,
        triggerEvent: triggerEvent,
        check: queueCheckLazyElements
    });

    if (document.readyState !== 'loading') {
        setTimeout(ready, 0);
    } else {
        document.addEventListener('DOMContentLoaded', ready);
    }

})(window, document);