/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 */
(function (document) {

    ress_loadGooglefont = function (fontUrl, testString) {
        var fonts = unescape(fontUrl.match(/=([^&]*)/)[1]).split('|'),
            fontsList = [],
            i, j;
        for (i = 0; i < fonts.length; i++) {
            var splitted = fonts[i].split(':'),
                weights = [splitted[0].replace(/\+/g, ' ')],
                variants = (splitted[1] || '400').split(',');
            for (j = 0; j < variants.length; j++) {
                weights.push(variants[j].replace(/^(regular|normal)/, '400').replace(/^b(old)?/, '700').replace(/italic$/, 'i').replace(/^i/, '400i'));
            }
            fontsList.push(weights);
        }
        loadFont(fontUrl, fontsList, testString);
    };

    var loadFont = ress_loadFont = function (fontUrl, fontsList, testString) {
        testString = testString || 'BES bswy 0';

        function loadFont() {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = fontUrl;
            document.head.appendChild(link);
        }

        if (!document.addEventListener) {
            return loadFont();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var fonts_to_load = 0,
                prev_width = [],
                counter = 100,
                iframe = document.createElement('iframe'),
                style,
                iframeDoc,
                i,
                j,
                elems,
                variants,
                weight;

            style = iframe.style;
            style.position = 'absolute';
            style.left = '-9999px';
            document.body.appendChild(iframe);

            elems = '<link rel="stylesheet" href="' + fontUrl + '">';
            for (i = 0; i < fontsList.length; i++) {
                for (j = 1; j < fontsList[i].length; j++) {
                    elems += '<span style="font-size:999px;white-space:nowrap">' + testString + '</span><br>';
                }
            }

            iframeDoc = iframe.contentWindow.document;
            iframeDoc.open();
            iframeDoc.write(elems);
            iframeDoc.close();

            elems = iframeDoc.getElementsByTagName('span');

            for (i = 0; i < fontsList.length; i++) {
                variants = fontsList[i];
                for (j = 1; j < variants.length; j++) {
                    weight = variants[j].split('i');
                    prev_width.push(elems[fonts_to_load].offsetWidth);
                    style = elems[fonts_to_load++].style;
                    style.fontFamily = '"' + variants[0] + '"';
                    style.fontWeight = weight[0];
                    if (weight.length > 1) {
                        style.fontStyle = 'italic';
                    }
                }
            }

            (function checkLoaded() {
                for (i = 0; i < prev_width.length; i++) {
                    if (prev_width[i] !== false && elems[i].offsetWidth !== prev_width[i]) {
                        prev_width[i] = false;
                        fonts_to_load--;
                        if (fonts_to_load === 0) {
                            setTimeout(function () {
                                iframe.parentNode.removeChild(iframe);
                            }, 1000);
                            return loadFont();
                        }
                    }
                }
                if (--counter > 0) {
                    // stop checks after specified number of attempts
                    setTimeout(checkLoaded, 100);
                }
            })();
        });
    };
})(document);