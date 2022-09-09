(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/*global window */

var css_ast = require('css');
var css_mediaquery = require('css-mediaquery');

var defOptions = {
    viewports: [
        {width: 1440, height: 900},
        {width: 1280, height: 800},
        {width: 800, height: 1280},
        {width: 768, height: 1024},
        {width: 1024, height: 768},
        {width: 360, height: 640},
        {width: 640, height: 360},
        {width: 384, height: 568},
        {width: 568, height: 384},
        {width: 320, height: 568},
        {width: 480, height: 320}
    ]
};

window.getATFCSS = function (url, options, callback) {
    if (callback === undefined) {
        callback = options;
        options = {};
    }
    for (var key in defOptions) {
        if (!(key in options)) {
            options[key] = defOptions[key];
        }
    }

    var win, doc, reCurrentHost, height;

    var iframe = document.createElement('iframe');
    iframe.frameBorder = '0';
    iframe.sandbox = 'allow-same-origin';
    iframe.scrolling = 'no';
    iframe.seamless = 'seamless';
    iframe.style = 'position:fixed;visibility:hidden;z-index:-9999;overflow:hidden;left:-9999px;';
    iframe.onload = function () {
        win = iframe.contentWindow;
        doc = win.document;
        reCurrentHost = new RegExp('^(https?:)?//' + win.location.host.replace('.', '\.') + '/');
        try {
            // get css styles
            var css = getStyles();

            // @todo hide scrollbar to better emulate mobile layout ???

            var i;

            // parse styles
            for (i = 0; i < css.length; i++) {
                if (css[i]) {
                    try {
                        css[i] = css_ast.parse(css[i], {silent: true});
                    } catch (e) {
                        console.log(e);
                        css[i] = '';
                    }
                }
            }

            // remove invisible rules
            for (i = 0; i < css.length; i++) {
                if (css[i]) {
                    try {
                        filterVisibleRules(css[i].stylesheet);
                    } catch (e) {
                        console.log(e);
                    }
                }
            }

            var usedSelectors = {};

            // get visible elements selectors
            for (var index = 0; index < options.viewports.length; index++) {
                iframe.width = options.viewports[index].width;
                iframe.height = height = options.viewports[index].height;
                // @todo wait redraw ???
                for (i = 0; i < css.length; i++) {
                    if (css[i]) {
                        try {
                            getAboveTheFoldSelectors(css[i].stylesheet, usedSelectors);
                        } catch (e) {
                            console.log(e);
                        }
                    }
                }
            }

            // remove unused rules and collect used fonts
            var usedFonts = {};
            for (i = 0; i < css.length; i++) {
                if (css[i]) {
                    try {
                        keepAboveTheFoldOnly(css[i].stylesheet, usedSelectors, usedFonts);
                    } catch (e) {
                        console.log(e);
                    }
                }
            }

            // remove unused fontfaces
            for (i = 0; i < css.length; i++) {
                if (css[i]) {
                    try {
                        removeUnusedFontface(css[i].stylesheet, usedFonts);
                    } catch (e) {
                        console.log(e);
                    }
                }
            }

            // stringify
            for (i = 0; i < css.length; i++) {
                if (css[i]) {
                    try {
                        css[i] = css_ast.stringify(css[i], {compress: true});
                    } catch (e) {
                        css[i] = '';
                    }
                }
            }

            css = css.join('');
            callback(css);
        } catch (e) {
            callback();
        }
        iframe.parentNode.removeChild(iframe);
    };
    iframe.onerror = function () {
        callback();
    };
    iframe.src = url;
    document.documentElement.appendChild(iframe);

    var AdblockRegex = /^[^{]*(?:img|iframe)\[src=[^{]+\{display:\s*none\s*!important;?\}$/;
    function getStyles() {
        var elements = doc.querySelectorAll('link[rel=stylesheet][href],style');
        var css = [];
        var i, element;
        for (i = 0; i < elements.length; i++) {
            element = elements[i];
            if (element.type === '' || element.type === 'text/css') {
                if (element.tagName === 'LINK') {
                    css[i] = {type: 'link', href: element.href, media: element.media};
                } else {
                    css[i] = {type: 'style', text: element.textContent, media: element.media};
                }
            }
        }
        var baseURI = doc.baseURI;
        for (i = 0; i < css.length; i++) {
            element = css[i];
            var cssContent;
            if (element.type === 'link') {
                cssContent = loadCss(element.href, element.media);
            } else {
                cssContent = relocateCss(element.text, baseURI);
                // remove adblock (@todo make it optional)
                if (AdblockRegex.test(cssContent)) {
                    cssContent = '';
                } else {
                    cssContent = embedImports(cssContent);
                    cssContent = wrapMedia(cssContent, element.media);
                }
            }
            css[i] = cssContent;
        }
        return css;
    }

    function loadCss(url, media) {
        var cssContent;
        try {
            var xhr = new XMLHttpRequest();
            // @todo synchronous requests are used (but actually resources should be in cache)
            xhr.open('GET', url, false);
            xhr.send(null);
            cssContent = xhr.responseText;
        } catch (e) {
            return '';
        }
        cssContent = relocateCss(cssContent, url);
        cssContent = embedImports(cssContent);
        if (media) {
            cssContent = wrapMedia(cssContent, media);
        }
        return cssContent;
    }

    function wrapMedia(css, media) {
        if (media && media !== 'all') {
            return '@media ' + media + ' {' + css + '}';
        }
        return css;
    }

    var reUrl = /url\(('([^']*)'|"([^"]*)"|([^\)]*))\)/g;
    function relocateCss(css, base) {
        return css.replace(reUrl, function () {
            var url = arguments[2] || arguments[3] || arguments[4];
            url = resolve(url, base);
            return "url('" + url + "')";
        });
    }

    function resolve(url, base_url) {
        if (url.substr(0, 5) === 'data:') {
            return url;
        }
        // @todo check for ^https?://
        var old_base = doc.getElementsByTagName('base')[0]
            , old_href = old_base && old_base.href
            , doc_head = doc.head || doc.getElementsByTagName('head')[0]
            , our_base = old_base || doc_head.appendChild(doc.createElement('base'))
            , resolver = doc.createElement('a')
            , resolved_url
            ;
        our_base.href = base_url;
        resolver.href = url;
        resolved_url = resolver.href;
        if (old_base) {
            old_base.href = old_href;
        } else {
            doc_head.removeChild(our_base);
        }
        resolved_url = resolved_url.replace(reCurrentHost, '/');
        return resolved_url;
    }

    var reImportUrl = /@import\s+url\(('([^']*)'|"([^"]*)"|([^\)]*))\);/g;
    function embedImports(css) {
        return css.replace(reImportUrl, function () {
            var url = arguments[2] || arguments[3] || arguments[4];
            return loadCss(url);
        });
    }


    var reSkipSelector = /::(-moz-)?selection/;
    var reSkipProperty = /.*(animation|transition).*|cursor|filter|pointer-events|(-webkit-)?tap-highlight-color|.*user-select/;
    function filterVisibleRules(stylesheet) {
        var i, j;
        for (i = stylesheet.rules.length - 1; i >= 0; i--) {
            var rule = stylesheet.rules[i];
            switch (rule.type) {
                case 'import':
                case 'namespace':
                case 'font-face':
                    // keep as is
                    break;
                case 'rule':
                    var selectors = rule.selectors;
                    for (j = selectors.length - 1; j >= 0; j--) {
                        var selector = selectors[j];
                        if (reSkipSelector.test(selector)) {
                            stylesheet.rules[i].selectors.splice(j, 1);
                        }
                    }
                    if (!stylesheet.rules[i].selectors.length) {
                        stylesheet.rules.splice(i, 1);
                    } else {
                        var declarations = rule.declarations;
                        if (declarations && 'length' in declarations) {
                            for (j = declarations.length - 1; j >= 0; j--) {
                                var declaration = declarations[j];
                                if (declaration.type !== 'declaration') {
                                    stylesheet.rules[i].declarations.splice(j, 1);
                                } else {
                                    var property = declaration.property;
                                    if (reSkipProperty.test(property)) {
                                        stylesheet.rules[i].declarations.splice(j, 1);
                                    }
                                }
                            }
                            if (!stylesheet.rules[i].declarations.length) {
                                stylesheet.rules.splice(i, 1);
                            }
                        } else {
                            stylesheet.rules.splice(i, 1);
                        }
                    }
                    break;
                case 'media':
                    try {
                        // @todo check dimensions
                        if (!css_mediaquery.parse(rule.media).some(function (query) { return query.inverse ^ (query.type === 'all' || query.type === 'screen'); })) {
                            stylesheet.rules.splice(i, 1);
                            break;
                        }
                    } catch(e) {
                        console.log(e);
                    }
                    filterVisibleRules(rule);
                    if (!stylesheet.rules[i].rules.length) {
                        stylesheet.rules.splice(i, 1);
                    }
                    break;
                case 'document':
                case 'supports':
                    filterVisibleRules(rule);
                    if (!stylesheet.rules[i].rules.length) {
                        stylesheet.rules.splice(i, 1);
                    }
                    break;
                default:
                    stylesheet.rules.splice(i, 1);
            }
        }
    }

    function getAboveTheFoldSelectors(stylesheet, usedSelectors) {
        for (var i = 0; i < stylesheet.rules.length; i++) {
            var rule = stylesheet.rules[i];
            switch (rule.type) {
                case 'rule':
                    var selectors = rule.selectors;
                    for (var j = 0; j < selectors.length; j++) {
                        var selector = selectors[j];
                        if (!usedSelectors[selector] && isAboveTheFold(selector)) {
                            usedSelectors[selector] = true;
                        }
                    }
                    break;
                case 'media':
                case 'document':
                case 'supports':
                    getAboveTheFoldSelectors(rule, usedSelectors);
                    break;
                default:
                    break;
            }
        }
    }

    function keepAboveTheFoldOnly(stylesheet, usedSelectors, usedFonts) {
        var i, j;
        for (i = stylesheet.rules.length - 1; i >= 0; i--) {
            var rule = stylesheet.rules[i];
            switch (rule.type) {
                case 'import':
                case 'namespace':
                case 'font-face':
                    // keep as is
                    break;
                case 'rule':
                    var selectors = rule.selectors;
                    for (j = selectors.length - 1; j >= 0; j--) {
                        var selector = selectors[j];
                        if (!usedSelectors[selector]) {
                            stylesheet.rules[i].selectors.splice(j, 1);
                        }
                    }
                    if (!stylesheet.rules[i].selectors.length) {
                        stylesheet.rules.splice(i, 1);
                    } else {
                        var declarations = rule.declarations;
                        for (j = declarations.length - 1; j >= 0; j--) {
                            var declaration = declarations[j];
                            if (declaration.type !== 'declaration') {
                                stylesheet.rules[i].declarations.splice(j, 1);
                            } else {
                                var property = declaration.property;
                                var value = declaration.value;
                                switch (property) {
                                    case 'font':
                                        var indexFontFamily = value.length;
                                        var pos;
                                        pos = value.indexOf("'");
                                        if (pos >= 0 && pos < indexFontFamily) {
                                            indexFontFamily = pos;
                                        }
                                        pos = value.indexOf('"');
                                        if (pos >= 0 && pos < indexFontFamily) {
                                            indexFontFamily = pos;
                                        }
                                        pos = value.substr(0, value.indexOf(',')).lastIndexOf(' ') + 1;
                                        if (pos < indexFontFamily) {
                                            indexFontFamily = pos;
                                        }
                                        populateFontList(value.substr(indexFontFamily), usedFonts);
                                        break;
                                    case 'font-family':
                                        populateFontList(value, usedFonts);
                                        break;
                                    case 'background':
                                    case 'background-url':
                                        // @todo optionally remove too long base64-encoded images, fonts, etc.
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                        if (!stylesheet.rules[i].declarations.length) {
                            stylesheet.rules.splice(i, 1);
                        }
                    }
                    break;
                case 'media':
                case 'document':
                case 'supports':
                    keepAboveTheFoldOnly(rule, usedSelectors, usedFonts);
                    if (!stylesheet.rules[i].rules.length) {
                        stylesheet.rules.splice(i, 1);
                    }
                    break;
                default:
                    stylesheet.rules.splice(i, 1);
            }
        }
    }

    function isAboveTheFold(selector) {
        if (selector === '*' || /^@/.test(selector)) {
            return true;
        }
        if (selector.indexOf(':') > -1) {
            selector = selector.replace(/(:?:before|:?:after)*/g, '');
            if (selector.replace(/:[:]?([a-zA-Z0-9\-_])*/g, '').trim().length === 0) {
                return true;
            }
            selector = selector.replace(/:?:-[a-z-]*/g, '');
        }

        var elements;
        try {
            elements = doc.querySelectorAll(selector);
        } catch (e) {
            return false;
        }

        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            var originalClearStyle = element.style.clear || '';
            element.style.clear = 'none';
            var boundingRect = element.getBoundingClientRect();
            element.style.clear = originalClearStyle;
            var aboveFold = boundingRect.top < height;
            if (aboveFold) {
                return true;
            }
        }
        return false;
    }

    function trimQuotes(s) {
        return s.replace(/^(\s|'|")+|(\s|'|")+$/gm, '');
    }

    function populateFontList(strFonts, usedFonts) {
        // @todo implement better splitting to allow commas in quotes font names
        var fonts = strFonts.split(',');
        for (var i = 0; i < fonts.length; i++) {
            var fontFamily = trimQuotes(fonts[i]);
            usedFonts[fontFamily] = true;
        }
    }

    function removeUnusedFontface(stylesheet, usedFonts) {
        for (var i = stylesheet.rules.length - 1; i >= 0; i--) {
            var rule = stylesheet.rules[i];
            switch (rule.type) {
                case 'font-face':
                    var bUsedFontFamily = false;
                    var declarations = rule.declarations;
                    for (var j = 0; j < declarations.length; j++) {
                        var declaration = declarations[j];
                        if (declaration.property === 'font-family') {
                            var fontFamily = trimQuotes(declaration.value);
                            if (usedFonts[fontFamily]) {
                                bUsedFontFamily = true;
                            }
                            break;
                        }
                    }
                    if (!bUsedFontFamily) {
                        stylesheet.rules.splice(i, 1);
                    }
                    break;
                case 'media':
                case 'document':
                case 'supports':
                    removeUnusedFontface(rule, usedFonts);
                    if (!stylesheet.rules[i].rules.length) {
                        stylesheet.rules.splice(i, 1);
                    }
                    break;
                default:
                    break;
            }
        }
    }
};


},{"css":3,"css-mediaquery":2}],2:[function(require,module,exports){
/*
Copyright (c) 2014, Yahoo! Inc. All rights reserved.
Copyrights licensed under the New BSD License.
See the accompanying LICENSE file for terms.
*/

'use strict';

exports.match = matchQuery;
exports.parse = parseQuery;

// -----------------------------------------------------------------------------

var RE_MEDIA_QUERY     = /(?:(only|not)?\s*([^\s\(\)]+)(?:\s*and)?\s*)?(.+)?/i,
    RE_MQ_EXPRESSION   = /\(\s*([^\s\:\)]+)\s*(?:\:\s*([^\s\)]+))?\s*\)/,
    RE_MQ_FEATURE      = /^(?:(min|max)-)?(.+)/,
    RE_LENGTH_UNIT     = /(em|rem|px|cm|mm|in|pt|pc)?$/,
    RE_RESOLUTION_UNIT = /(dpi|dpcm|dppx)?$/;

function matchQuery(mediaQuery, values) {
    return parseQuery(mediaQuery).some(function (query) {
        var inverse = query.inverse;

        // Either the parsed or specified `type` is "all", or the types must be
        // equal for a match.
        var typeMatch = query.type === 'all' || values.type === query.type;

        // Quit early when `type` doesn't match, but take "not" into account.
        if ((typeMatch && inverse) || !(typeMatch || inverse)) {
            return false;
        }

        var expressionsMatch = query.expressions.every(function (expression) {
            var feature  = expression.feature,
                modifier = expression.modifier,
                expValue = expression.value,
                value    = values[feature];

            // Missing or falsy values don't match.
            if (!value) { return false; }

            switch (feature) {
                case 'orientation':
                case 'scan':
                    return value.toLowerCase() === expValue.toLowerCase();

                case 'width':
                case 'height':
                case 'device-width':
                case 'device-height':
                    expValue = toPx(expValue);
                    value    = toPx(value);
                    break;

                case 'resolution':
                    expValue = toDpi(expValue);
                    value    = toDpi(value);
                    break;

                case 'aspect-ratio':
                case 'device-aspect-ratio':
                case /* Deprecated */ 'device-pixel-ratio':
                    expValue = toDecimal(expValue);
                    value    = toDecimal(value);
                    break;

                case 'grid':
                case 'color':
                case 'color-index':
                case 'monochrome':
                    expValue = parseInt(expValue, 10) || 1;
                    value    = parseInt(value, 10) || 0;
                    break;
            }

            switch (modifier) {
                case 'min': return value >= expValue;
                case 'max': return value <= expValue;
                default   : return value === expValue;
            }
        });

        return (expressionsMatch && !inverse) || (!expressionsMatch && inverse);
    });
}

function parseQuery(mediaQuery) {
    return mediaQuery.split(',').map(function (query) {
        query = query.trim();

        var captures    = query.match(RE_MEDIA_QUERY),
            modifier    = captures[1],
            type        = captures[2],
            expressions = captures[3] || '',
            parsed      = {};

        parsed.inverse = !!modifier && modifier.toLowerCase() === 'not';
        parsed.type    = type ? type.toLowerCase() : 'all';

        // Split expressions into a list.
        expressions = expressions.match(/\([^\)]+\)/g) || [];

        parsed.expressions = expressions.map(function (expression) {
            var captures = expression.match(RE_MQ_EXPRESSION),
                feature  = captures[1].toLowerCase().match(RE_MQ_FEATURE);

            return {
                modifier: feature[1],
                feature : feature[2],
                value   : captures[2]
            };
        });

        return parsed;
    });
}

// -- Utilities ----------------------------------------------------------------

function toDecimal(ratio) {
    var decimal = Number(ratio),
        numbers;

    if (!decimal) {
        numbers = ratio.match(/^(\d+)\s*\/\s*(\d+)$/);
        decimal = numbers[1] / numbers[2];
    }

    return decimal;
}

function toDpi(resolution) {
    var value = parseFloat(resolution),
        units = String(resolution).match(RE_RESOLUTION_UNIT)[1];

    switch (units) {
        case 'dpcm': return value / 2.54;
        case 'dppx': return value * 96;
        default    : return value;
    }
}

function toPx(length) {
    var value = parseFloat(length),
        units = String(length).match(RE_LENGTH_UNIT)[1];

    switch (units) {
        case 'em' : return value * 16;
        case 'rem': return value * 16;
        case 'cm' : return value * 96 / 2.54;
        case 'mm' : return value * 96 / 2.54 / 10;
        case 'in' : return value * 96;
        case 'pt' : return value * 72;
        case 'pc' : return value * 72 / 12;
        default   : return value;
    }
}

},{}],3:[function(require,module,exports){
exports.parse = require('./lib/parse');
exports.stringify = require('./lib/stringify');

},{"./lib/parse":4,"./lib/stringify":8}],4:[function(require,module,exports){
// http://www.w3.org/TR/CSS21/grammar.html
// https://github.com/visionmedia/css-parse/pull/49#issuecomment-30088027
var commentre = /\/\*[^*]*\*+([^/*][^*]*\*+)*\//g

module.exports = function(css, options){
  options = options || {};

  /**
   * Positional.
   */

  var lineno = 1;
  var column = 1;

  /**
   * Update lineno and column based on `str`.
   */

  function updatePosition(str) {
    var lines = str.match(/\n/g);
    if (lines) lineno += lines.length;
    var i = str.lastIndexOf('\n');
    column = ~i ? str.length - i : column + str.length;
  }

  /**
   * Mark position and patch `node.position`.
   */

  function position() {
    var start = { line: lineno, column: column };
    return function(node){
      node.position = new Position(start);
      whitespace();
      return node;
    };
  }

  /**
   * Store position information for a node
   */

  function Position(start) {
    this.start = start;
    this.end = { line: lineno, column: column };
    this.source = options.source;
  }

  /**
   * Non-enumerable source string
   */

  Position.prototype.content = css;

  /**
   * Error `msg`.
   */

  var errorsList = [];

  function error(msg) {
    var err = new Error(options.source + ':' + lineno + ':' + column + ': ' + msg);
    err.reason = msg;
    err.filename = options.source;
    err.line = lineno;
    err.column = column;
    err.source = css;

    if (options.silent) {
      errorsList.push(err);
    } else {
      throw err;
    }
  }

  /**
   * Parse stylesheet.
   */

  function stylesheet() {
    var rulesList = rules();

    return {
      type: 'stylesheet',
      stylesheet: {
        rules: rulesList,
        parsingErrors: errorsList
      }
    };
  }

  /**
   * Opening brace.
   */

  function open() {
    return match(/^{\s*/);
  }

  /**
   * Closing brace.
   */

  function close() {
    return match(/^}/);
  }

  /**
   * Parse ruleset.
   */

  function rules() {
    var node;
    var rules = [];
    whitespace();
    comments(rules);
    while (css.length && css.charAt(0) != '}' && (node = atrule() || rule())) {
      if (node !== false) {
        rules.push(node);
        comments(rules);
      }
    }
    return rules;
  }

  /**
   * Match `re` and return captures.
   */

  function match(re) {
    var m = re.exec(css);
    if (!m) return;
    var str = m[0];
    updatePosition(str);
    css = css.slice(str.length);
    return m;
  }

  /**
   * Parse whitespace.
   */

  function whitespace() {
    match(/^\s*/);
  }

  /**
   * Parse comments;
   */

  function comments(rules) {
    var c;
    rules = rules || [];
    while (c = comment()) {
      if (c !== false) {
        rules.push(c);
      }
    }
    return rules;
  }

  /**
   * Parse comment.
   */

  function comment() {
    var pos = position();
    if ('/' != css.charAt(0) || '*' != css.charAt(1)) return;

    var i = 2;
    while ("" != css.charAt(i) && ('*' != css.charAt(i) || '/' != css.charAt(i + 1))) ++i;
    i += 2;

    if ("" === css.charAt(i-1)) {
      return error('End of comment missing');
    }

    var str = css.slice(2, i - 2);
    column += 2;
    updatePosition(str);
    css = css.slice(i);
    column += 2;

    return pos({
      type: 'comment',
      comment: str
    });
  }

  /**
   * Parse selector.
   */

  function selector() {
    var m = match(/^([^{]+)/);
    if (!m) return;
    /* @fix Remove all comments from selectors
     * http://ostermiller.org/findcomment.html */
    return trim(m[0])
      .replace(/\/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*\/+/g, '')
      .replace(/"(?:\\"|[^"])*"|'(?:\\'|[^'])*'/g, function(m) {
        return m.replace(/,/g, '\u200C');
      })
      .split(/\s*(?![^(]*\)),\s*/)
      .map(function(s) {
        return s.replace(/\u200C/g, ',');
      });
  }

  /**
   * Parse declaration.
   */

  function declaration() {
    var pos = position();

    // prop
    var prop = match(/^(\*?[-#\/\*\\\w]+(\[[0-9a-z_-]+\])?)\s*/);
    if (!prop) return;
    prop = trim(prop[0]);

    // :
    if (!match(/^:\s*/)) return error("property missing ':'");

    // val
    var val = match(/^((?:'(?:\\'|.)*?'|"(?:\\"|.)*?"|\([^\)]*?\)|[^};])+)/);

    var ret = pos({
      type: 'declaration',
      property: prop.replace(commentre, ''),
      value: val ? trim(val[0]).replace(commentre, '') : ''
    });

    // ;
    match(/^[;\s]*/);

    return ret;
  }

  /**
   * Parse declarations.
   */

  function declarations() {
    var decls = [];

    if (!open()) return error("missing '{'");
    comments(decls);

    // declarations
    var decl;
    while (decl = declaration()) {
      if (decl !== false) {
        decls.push(decl);
        comments(decls);
      }
    }

    if (!close()) return error("missing '}'");
    return decls;
  }

  /**
   * Parse keyframe.
   */

  function keyframe() {
    var m;
    var vals = [];
    var pos = position();

    while (m = match(/^((\d+\.\d+|\.\d+|\d+)%?|[a-z]+)\s*/)) {
      vals.push(m[1]);
      match(/^,\s*/);
    }

    if (!vals.length) return;

    return pos({
      type: 'keyframe',
      values: vals,
      declarations: declarations()
    });
  }

  /**
   * Parse keyframes.
   */

  function atkeyframes() {
    var pos = position();
    var m = match(/^@([-\w]+)?keyframes\s*/);

    if (!m) return;
    var vendor = m[1];

    // identifier
    var m = match(/^([-\w]+)\s*/);
    if (!m) return error("@keyframes missing name");
    var name = m[1];

    if (!open()) return error("@keyframes missing '{'");

    var frame;
    var frames = comments();
    while (frame = keyframe()) {
      frames.push(frame);
      frames = frames.concat(comments());
    }

    if (!close()) return error("@keyframes missing '}'");

    return pos({
      type: 'keyframes',
      name: name,
      vendor: vendor,
      keyframes: frames
    });
  }

  /**
   * Parse supports.
   */

  function atsupports() {
    var pos = position();
    var m = match(/^@supports *([^{]+)/);

    if (!m) return;
    var supports = trim(m[1]);

    if (!open()) return error("@supports missing '{'");

    var style = comments().concat(rules());

    if (!close()) return error("@supports missing '}'");

    return pos({
      type: 'supports',
      supports: supports,
      rules: style
    });
  }

  /**
   * Parse host.
   */

  function athost() {
    var pos = position();
    var m = match(/^@host\s*/);

    if (!m) return;

    if (!open()) return error("@host missing '{'");

    var style = comments().concat(rules());

    if (!close()) return error("@host missing '}'");

    return pos({
      type: 'host',
      rules: style
    });
  }

  /**
   * Parse media.
   */

  function atmedia() {
    var pos = position();
    var m = match(/^@media *([^{]+)/);

    if (!m) return;
    var media = trim(m[1]);

    if (!open()) return error("@media missing '{'");

    var style = comments().concat(rules());

    if (!close()) return error("@media missing '}'");

    return pos({
      type: 'media',
      media: media,
      rules: style
    });
  }


  /**
   * Parse custom-media.
   */

  function atcustommedia() {
    var pos = position();
    var m = match(/^@custom-media\s+(--[^\s]+)\s*([^{;]+);/);
    if (!m) return;

    return pos({
      type: 'custom-media',
      name: trim(m[1]),
      media: trim(m[2])
    });
  }

  /**
   * Parse paged media.
   */

  function atpage() {
    var pos = position();
    var m = match(/^@page */);
    if (!m) return;

    var sel = selector() || [];

    if (!open()) return error("@page missing '{'");
    var decls = comments();

    // declarations
    var decl;
    while (decl = declaration()) {
      decls.push(decl);
      decls = decls.concat(comments());
    }

    if (!close()) return error("@page missing '}'");

    return pos({
      type: 'page',
      selectors: sel,
      declarations: decls
    });
  }

  /**
   * Parse document.
   */

  function atdocument() {
    var pos = position();
    var m = match(/^@([-\w]+)?document *([^{]+)/);
    if (!m) return;

    var vendor = trim(m[1]);
    var doc = trim(m[2]);

    if (!open()) return error("@document missing '{'");

    var style = comments().concat(rules());

    if (!close()) return error("@document missing '}'");

    return pos({
      type: 'document',
      document: doc,
      vendor: vendor,
      rules: style
    });
  }

  /**
   * Parse font-face.
   */

  function atfontface() {
    var pos = position();
    var m = match(/^@font-face\s*/);
    if (!m) return;

    if (!open()) return error("@font-face missing '{'");
    var decls = comments();

    // declarations
    var decl;
    while (decl = declaration()) {
      decls.push(decl);
      decls = decls.concat(comments());
    }

    if (!close()) return error("@font-face missing '}'");

    return pos({
      type: 'font-face',
      declarations: decls
    });
  }

  /**
   * Parse import
   */

  var atimport = _compileAtrule('import');

  /**
   * Parse charset
   */

  var atcharset = _compileAtrule('charset');

  /**
   * Parse namespace
   */

  var atnamespace = _compileAtrule('namespace');

  /**
   * Parse non-block at-rules
   */


  function _compileAtrule(name) {
    var re = new RegExp('^@' + name + '\\s*([^;]+);');
    return function() {
      var pos = position();
      var m = match(re);
      if (!m) return;
      var ret = { type: name };
      ret[name] = m[1].trim();
      return pos(ret);
    }
  }

  /**
   * Parse at rule.
   */

  function atrule() {
    if (css[0] != '@') return;

    return atkeyframes()
      || atmedia()
      || atcustommedia()
      || atsupports()
      || atimport()
      || atcharset()
      || atnamespace()
      || atdocument()
      || atpage()
      || athost()
      || atfontface();
  }

  /**
   * Parse rule.
   */

  function rule() {
    var pos = position();
    var sel = selector();

    if (!sel) return error('selector missing');
    comments();

    return pos({
      type: 'rule',
      selectors: sel,
      declarations: declarations()
    });
  }

  return addParent(stylesheet());
};

/**
 * Trim `str`.
 */

function trim(str) {
  return str ? str.replace(/^\s+|\s+$/g, '') : '';
}

/**
 * Adds non-enumerable parent node reference to each node.
 */

function addParent(obj, parent) {
  var isNode = obj && typeof obj.type === 'string';
  var childParent = isNode ? obj : parent;

  for (var k in obj) {
    var value = obj[k];
    if (Array.isArray(value)) {
      value.forEach(function(v) { addParent(v, childParent); });
    } else if (value && typeof value === 'object') {
      addParent(value, childParent);
    }
  }

  if (isNode) {
    Object.defineProperty(obj, 'parent', {
      configurable: true,
      writable: true,
      enumerable: false,
      value: parent || null
    });
  }

  return obj;
}

},{}],5:[function(require,module,exports){

/**
 * Expose `Compiler`.
 */

module.exports = Compiler;

/**
 * Initialize a compiler.
 *
 * @param {Type} name
 * @return {Type}
 * @api public
 */

function Compiler(opts) {
  this.options = opts || {};
}

/**
 * Emit `str`
 */

Compiler.prototype.emit = function(str) {
  return str;
};

/**
 * Visit `node`.
 */

Compiler.prototype.visit = function(node){
  return this[node.type](node);
};

/**
 * Map visit over array of `nodes`, optionally using a `delim`
 */

Compiler.prototype.mapVisit = function(nodes, delim){
  var buf = '';
  delim = delim || '';

  for (var i = 0, length = nodes.length; i < length; i++) {
    buf += this.visit(nodes[i]);
    if (delim && i < length - 1) buf += this.emit(delim);
  }

  return buf;
};

},{}],6:[function(require,module,exports){

/**
 * Module dependencies.
 */

var Base = require('./compiler');
var inherits = require('inherits');

/**
 * Expose compiler.
 */

module.exports = Compiler;

/**
 * Initialize a new `Compiler`.
 */

function Compiler(options) {
  Base.call(this, options);
}

/**
 * Inherit from `Base.prototype`.
 */

inherits(Compiler, Base);

/**
 * Compile `node`.
 */

Compiler.prototype.compile = function(node){
  return node.stylesheet
    .rules.map(this.visit, this)
    .join('');
};

/**
 * Visit comment node.
 */

Compiler.prototype.comment = function(node){
  return this.emit('', node.position);
};

/**
 * Visit import node.
 */

Compiler.prototype.import = function(node){
  return this.emit('@import ' + node.import + ';', node.position);
};

/**
 * Visit media node.
 */

Compiler.prototype.media = function(node){
  return this.emit('@media ' + node.media, node.position)
    + this.emit('{')
    + this.mapVisit(node.rules)
    + this.emit('}');
};

/**
 * Visit document node.
 */

Compiler.prototype.document = function(node){
  var doc = '@' + (node.vendor || '') + 'document ' + node.document;

  return this.emit(doc, node.position)
    + this.emit('{')
    + this.mapVisit(node.rules)
    + this.emit('}');
};

/**
 * Visit charset node.
 */

Compiler.prototype.charset = function(node){
  return this.emit('@charset ' + node.charset + ';', node.position);
};

/**
 * Visit namespace node.
 */

Compiler.prototype.namespace = function(node){
  return this.emit('@namespace ' + node.namespace + ';', node.position);
};

/**
 * Visit supports node.
 */

Compiler.prototype.supports = function(node){
  return this.emit('@supports ' + node.supports, node.position)
    + this.emit('{')
    + this.mapVisit(node.rules)
    + this.emit('}');
};

/**
 * Visit keyframes node.
 */

Compiler.prototype.keyframes = function(node){
  return this.emit('@'
    + (node.vendor || '')
    + 'keyframes '
    + node.name, node.position)
    + this.emit('{')
    + this.mapVisit(node.keyframes)
    + this.emit('}');
};

/**
 * Visit keyframe node.
 */

Compiler.prototype.keyframe = function(node){
  var decls = node.declarations;

  return this.emit(node.values.join(','), node.position)
    + this.emit('{')
    + this.mapVisit(decls)
    + this.emit('}');
};

/**
 * Visit page node.
 */

Compiler.prototype.page = function(node){
  var sel = node.selectors.length
    ? node.selectors.join(', ')
    : '';

  return this.emit('@page ' + sel, node.position)
    + this.emit('{')
    + this.mapVisit(node.declarations)
    + this.emit('}');
};

/**
 * Visit font-face node.
 */

Compiler.prototype['font-face'] = function(node){
  return this.emit('@font-face', node.position)
    + this.emit('{')
    + this.mapVisit(node.declarations)
    + this.emit('}');
};

/**
 * Visit host node.
 */

Compiler.prototype.host = function(node){
  return this.emit('@host', node.position)
    + this.emit('{')
    + this.mapVisit(node.rules)
    + this.emit('}');
};

/**
 * Visit custom-media node.
 */

Compiler.prototype['custom-media'] = function(node){
  return this.emit('@custom-media ' + node.name + ' ' + node.media + ';', node.position);
};

/**
 * Visit rule node.
 */

Compiler.prototype.rule = function(node){
  var decls = node.declarations;
  if (!decls.length) return '';

  return this.emit(node.selectors.join(','), node.position)
    + this.emit('{')
    + this.mapVisit(decls)
    + this.emit('}');
};

/**
 * Visit declaration node.
 */

Compiler.prototype.declaration = function(node){
  return this.emit(node.property + ':' + node.value, node.position) + this.emit(';');
};


},{"./compiler":5,"inherits":9}],7:[function(require,module,exports){

/**
 * Module dependencies.
 */

var Base = require('./compiler');
var inherits = require('inherits');

/**
 * Expose compiler.
 */

module.exports = Compiler;

/**
 * Initialize a new `Compiler`.
 */

function Compiler(options) {
  options = options || {};
  Base.call(this, options);
  this.indentation = options.indent;
}

/**
 * Inherit from `Base.prototype`.
 */

inherits(Compiler, Base);

/**
 * Compile `node`.
 */

Compiler.prototype.compile = function(node){
  return this.stylesheet(node);
};

/**
 * Visit stylesheet node.
 */

Compiler.prototype.stylesheet = function(node){
  return this.mapVisit(node.stylesheet.rules, '\n\n');
};

/**
 * Visit comment node.
 */

Compiler.prototype.comment = function(node){
  return this.emit(this.indent() + '/*' + node.comment + '*/', node.position);
};

/**
 * Visit import node.
 */

Compiler.prototype.import = function(node){
  return this.emit('@import ' + node.import + ';', node.position);
};

/**
 * Visit media node.
 */

Compiler.prototype.media = function(node){
  return this.emit('@media ' + node.media, node.position)
    + this.emit(
        ' {\n'
        + this.indent(1))
    + this.mapVisit(node.rules, '\n\n')
    + this.emit(
        this.indent(-1)
        + '\n}');
};

/**
 * Visit document node.
 */

Compiler.prototype.document = function(node){
  var doc = '@' + (node.vendor || '') + 'document ' + node.document;

  return this.emit(doc, node.position)
    + this.emit(
        ' '
      + ' {\n'
      + this.indent(1))
    + this.mapVisit(node.rules, '\n\n')
    + this.emit(
        this.indent(-1)
        + '\n}');
};

/**
 * Visit charset node.
 */

Compiler.prototype.charset = function(node){
  return this.emit('@charset ' + node.charset + ';', node.position);
};

/**
 * Visit namespace node.
 */

Compiler.prototype.namespace = function(node){
  return this.emit('@namespace ' + node.namespace + ';', node.position);
};

/**
 * Visit supports node.
 */

Compiler.prototype.supports = function(node){
  return this.emit('@supports ' + node.supports, node.position)
    + this.emit(
      ' {\n'
      + this.indent(1))
    + this.mapVisit(node.rules, '\n\n')
    + this.emit(
        this.indent(-1)
        + '\n}');
};

/**
 * Visit keyframes node.
 */

Compiler.prototype.keyframes = function(node){
  return this.emit('@' + (node.vendor || '') + 'keyframes ' + node.name, node.position)
    + this.emit(
      ' {\n'
      + this.indent(1))
    + this.mapVisit(node.keyframes, '\n')
    + this.emit(
        this.indent(-1)
        + '}');
};

/**
 * Visit keyframe node.
 */

Compiler.prototype.keyframe = function(node){
  var decls = node.declarations;

  return this.emit(this.indent())
    + this.emit(node.values.join(', '), node.position)
    + this.emit(
      ' {\n'
      + this.indent(1))
    + this.mapVisit(decls, '\n')
    + this.emit(
      this.indent(-1)
      + '\n'
      + this.indent() + '}\n');
};

/**
 * Visit page node.
 */

Compiler.prototype.page = function(node){
  var sel = node.selectors.length
    ? node.selectors.join(', ') + ' '
    : '';

  return this.emit('@page ' + sel, node.position)
    + this.emit('{\n')
    + this.emit(this.indent(1))
    + this.mapVisit(node.declarations, '\n')
    + this.emit(this.indent(-1))
    + this.emit('\n}');
};

/**
 * Visit font-face node.
 */

Compiler.prototype['font-face'] = function(node){
  return this.emit('@font-face ', node.position)
    + this.emit('{\n')
    + this.emit(this.indent(1))
    + this.mapVisit(node.declarations, '\n')
    + this.emit(this.indent(-1))
    + this.emit('\n}');
};

/**
 * Visit host node.
 */

Compiler.prototype.host = function(node){
  return this.emit('@host', node.position)
    + this.emit(
        ' {\n'
        + this.indent(1))
    + this.mapVisit(node.rules, '\n\n')
    + this.emit(
        this.indent(-1)
        + '\n}');
};

/**
 * Visit custom-media node.
 */

Compiler.prototype['custom-media'] = function(node){
  return this.emit('@custom-media ' + node.name + ' ' + node.media + ';', node.position);
};

/**
 * Visit rule node.
 */

Compiler.prototype.rule = function(node){
  var indent = this.indent();
  var decls = node.declarations;
  if (!decls.length) return '';

  return this.emit(node.selectors.map(function(s){ return indent + s }).join(',\n'), node.position)
    + this.emit(' {\n')
    + this.emit(this.indent(1))
    + this.mapVisit(decls, '\n')
    + this.emit(this.indent(-1))
    + this.emit('\n' + this.indent() + '}');
};

/**
 * Visit declaration node.
 */

Compiler.prototype.declaration = function(node){
  return this.emit(this.indent())
    + this.emit(node.property + ': ' + node.value, node.position)
    + this.emit(';');
};

/**
 * Increase, decrease or return current indentation.
 */

Compiler.prototype.indent = function(level) {
  this.level = this.level || 1;

  if (null != level) {
    this.level += level;
    return '';
  }

  return Array(this.level).join(this.indentation || '  ');
};

},{"./compiler":5,"inherits":9}],8:[function(require,module,exports){

/**
 * Module dependencies.
 */

var Compressed = require('./compress');
var Identity = require('./identity');

/**
 * Stringfy the given AST `node`.
 *
 * Options:
 *
 *  - `compress` space-optimized output
 *  - `sourcemap` return an object with `.code` and `.map`
 *
 * @param {Object} node
 * @param {Object} [options]
 * @return {String}
 * @api public
 */

module.exports = function(node, options){
  options = options || {};

  var compiler = options.compress
    ? new Compressed(options)
    : new Identity(options);

  // source maps
/*  if (options.sourcemap) {
    var sourcemaps = require('./source-map-support');
    sourcemaps(compiler);

    var code = compiler.compile(node);
    compiler.applySourceMaps();

    var map = options.sourcemap === 'generator'
      ? compiler.map
      : compiler.map.toJSON();

    return { code: code, map: map };
  }*/

  var code = compiler.compile(node);
  return code;
};

},{"./compress":6,"./identity":7}],9:[function(require,module,exports){
if (typeof Object.create === 'function') {
  // implementation from standard node.js 'util' module
  module.exports = function inherits(ctor, superCtor) {
    ctor.super_ = superCtor
    ctor.prototype = Object.create(superCtor.prototype, {
      constructor: {
        value: ctor,
        enumerable: false,
        writable: true,
        configurable: true
      }
    });
  };
} else {
  // old school shim for old browsers
  module.exports = function inherits(ctor, superCtor) {
    ctor.super_ = superCtor
    var TempCtor = function () {}
    TempCtor.prototype = superCtor.prototype
    ctor.prototype = new TempCtor()
    ctor.prototype.constructor = ctor
  }
}

},{}]},{},[1]);
