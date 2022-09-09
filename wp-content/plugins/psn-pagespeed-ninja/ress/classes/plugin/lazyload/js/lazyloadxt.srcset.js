/*! Lazy Load XT v2.0.0 2019-05-12
 * http://ressio.github.io/lazy-load-xt
 * (C) 2013-2019 RESSIO
 * Licensed under MIT */

(function (window, document, undefined) {
    var _lazyLoadXT = window.lazyLoadXT,
        documentElement = document.documentElement,
        srcsetSupport = (function () {
            return 'srcset' in (new Image());
        })(),
        reUrl = /^\s*(\S+)/,
        reWidth = /\S\s+(\d+)w/,
        reDpr = /\S\s+([\d\.]+)x/,
        infty = [0, Infinity],
        one = [0, 1],
        srcsetOptions = {
            srcsetAttr: 'data-srcset',
            srcsetExtended: true,
            srcsetBaseAttr: 'data-srcset-base',
            srcsetExtAttr: 'data-srcset-ext',
            srcsetSizesAttr: 'data-srcset-size'
        },
        viewport = {
            w: 0,
            x: 0
        },
        property,
        limit;

    for (property in srcsetOptions) {
        if (_lazyLoadXT[property] === undefined) {
            _lazyLoadXT[property] = srcsetOptions[property];
        }
    }
    _lazyLoadXT.selector += ',img[' + _lazyLoadXT.srcsetAttr + ']';

    function grep(array, callback) {
        var ret = [],
            i = 0;
        for (; i < array.length; i++) {
            if (callback(array[i])) {
                ret.push(array[i]);
            }
        }
        return ret;
    }

    function map(array, callback) {
        var value,
            ret = [],
            i = 0;
        for (; i < array.length; i++) {
            value = callback(array[i]);
            if (value !== null) {
                ret.push(value);
            }
        }
        return ret;
    }

    function mathFilter(array, action) {
        return Math[action].apply(null, map(array, function (item) {
            return item[property];
        }));
    }

    function compareMax(item) {
        return item[property] >= viewport[property] || item[property] === limit;
    }

    function compareMin(item) {
        return item[property] === limit;
    }

    function splitSrcset(srcset) {
        return srcset.replace(/^\s+|\s+$/g, '').replace(/(\s+[\d\.]+[wx]),\s*|\s*,\s+/g, '$1 @,@ ').split(' @,@ ');
    }

    function parseSrcset(el) {
        var srcset = el.getAttribute(_lazyLoadXT.srcsetAttr);

        if (!srcset) {
            return false;
        }

        var list = map(splitSrcset(srcset), function (item) {
            return {
                url: reUrl.exec(item)[1],
                w: parseFloat((reWidth.exec(item) || infty)[1]),
                x: parseFloat((reDpr.exec(item) || one)[1])
            };
        });

        if (!list.length) {
            return false;
        }

        viewport = {
            w: window.innerWidth || documentElement.clientWidth,
            x: window.devicePixelRatio || 1
        };

        if (el.getAttribute(_lazyLoadXT.srcsetSizesAttr) === 'auto') {
            var save = el.width;
            el.width = viewport.w;
            viewport.w = el.scrollWidth;
            el.width = save;
        }

        viewport.w *= viewport.x;

        var wx,
            src;

        for (wx in viewport) {
            property = wx;
            limit = mathFilter(list, 'max');
            list = grep(list, compareMax);
        }

        for (wx in viewport) {
            property = wx;
            limit = mathFilter(list, 'min');
            list = grep(list, compareMin);
        }

        src = list[0].url;

        if (_lazyLoadXT.srcsetExtended) {
            src = (el.getAttribute(_lazyLoadXT.srcsetBaseAttr) || '') + src + (el.getAttribute(_lazyLoadXT.srcsetExtAttr) || '');
        }

        return src;
    }

    _lazyLoadXT.onEvent(document, 'lazyshow', function (e) {
        var el = e.target;
        if (el.tagName !== 'IMG') {
            return;
        }
        var srcset = el.getAttribute(_lazyLoadXT.srcsetAttr);

        if (srcset) {
            if (srcsetSupport) {
                if (_lazyLoadXT.srcsetExtended) {
                    var srcsetBaseAttr = el.getAttribute(_lazyLoadXT.srcsetBaseAttr) || '',
                        srcsetExtAttr = el.getAttribute(_lazyLoadXT.srcsetExtAttr) || '';
                    srcset = splitSrcset(srcset);
                    for (var i = 0; i < srcset.length; i++) {
                        var item = srcset[i],
                            j = item.indexOf(' ');
                        if (j < 0) {
                            j = item.length;
                        }
                        srcset[i] = srcsetBaseAttr + item.substr(0, j) + srcsetExtAttr + item.substr(j);
                    }
                    srcset = srcset.join(', ');
                }
                el.setAttribute('srcset', srcset);
            } else {
                el.lazyLoadXT.srcAttr = parseSrcset;
            }
        }
    });

})(window, document);
