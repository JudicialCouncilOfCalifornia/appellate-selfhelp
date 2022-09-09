/*! Lazy Load XT v2.0.0 2019-05-12
 * http://ressio.github.io/lazy-load-xt
 * (C) 2013-2017 RESS.io
 * Licensed under MIT */

(function () {
    var _lazyLoadXT = window.lazyLoadXT;

    _lazyLoadXT.selector += ',video,iframe[data-src]';
    _lazyLoadXT.videoPoster = 'data-poster';

    _lazyLoadXT.onEvent(document, 'lazyshow', function (e) {
        var el = e.target;
        if (el.tagName !== 'VIDEO') {
            return;
        }
        var srcAttr = _lazyLoadXT.srcAttr,
            isFuncSrcAttr = _lazyLoadXT.isFunction(srcAttr),
            changed = false;

        var poster = el.getAttribute(_lazyLoadXT.videoPoster);
        if (poster) {
            el.setAttribute('poster', poster);
        }

        var children = el.childNodes;
        for (var i = 0; i < children.length; i++) {
            var child = children[i],
                tagName = child.tagName;
            if (tagName !== 'SOURCE' && tagName !== 'TRACK') {
                continue;
            }
            var src = isFuncSrcAttr ? srcAttr(child) : child.getAttribute(srcAttr);
            if (src) {
                child.setAttribute('src', src);
                changed = true;
            }
        }

        // reload video
        if (changed) {
            el.load();
        }
    });

})();
