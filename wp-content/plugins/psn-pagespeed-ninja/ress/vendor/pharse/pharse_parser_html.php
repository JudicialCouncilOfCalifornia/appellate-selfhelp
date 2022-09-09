<?php
/**
 * @author RESSIO Team
 * @package Pharse
 * @link https://github.com/ressio/pharse
 *
 * FORKED FROM
 * @author Niels A.D.
 * @package Ganon
 * @link http://code.google.com/p/ganon/
 *
 * @license http://dev.perl.org/licenses/artistic.html Artistic License
 */

include_once 'pharse_tokenizer.php';
include_once 'pharse_node_html.php';
include_once 'pharse_selector_html.php';

/**
 * Parses a HTML document
 *
 * Functionality can be extended by overriding functions or adjusting the tag map.
 * Document may contain small errors, the parser will try to recover and resume parsing.
 */
class HTML_Parser_Base extends Tokenizer_Base
{

    /**
     * Tag open token, used for "<"
     */
    const TOK_TAG_OPEN = 100;
    /**
     * Tag close token, used for ">"
     */
    const TOK_TAG_CLOSE = 101;
    /**
     * Forward slash token, used for "/"
     */
    const TOK_SLASH_FORWARD = 103;
    /**
     * Backslash token, used for "\"
     */
    const TOK_SLASH_BACKWARD = 104;
    /**
     * String token, used for attribute values (" and ')
     */
    const TOK_STRING = 104;
    /**
     * Equals token, used for "="
     */
    const TOK_EQUALS = 105;

    /**
     * Sets HTML identifiers, tags/attributes are considered identifiers
     * @see Tokenizer_Base::$identifiers
     * @access private
     */
    protected $identifiers = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890:-_!?%';

    /**
     * Status of the parser (tagname, closing tag, etc)
     * @var array
     */
    protected $status = array();

    /**
     * Map characters to match their tokens
     * @see Tokenizer_Base::$custom_char_map
     * @access private
     */
    protected $custom_char_map = array(
        '<' => self::TOK_TAG_OPEN,
        '>' => self::TOK_TAG_CLOSE,
        "'" => 'parse_string',
        '"' => 'parse_string',
        '/' => self::TOK_SLASH_FORWARD,
        '\\' => self::TOK_SLASH_BACKWARD,
        '=' => self::TOK_EQUALS
    );

    /**
     * Stop characters in attributes (whitespace, <, >)
     * @see parse_attributes
     * @var array
     */
    protected $stopchar = array();

    public function __construct($doc = '', $pos = 0)
    {
        parent::__construct($doc, $pos);
        $this->parse_all();
    }

    /**
     * Callback functions for certain tags
     * @var array (TAG_NAME => FUNCTION_NAME)
     * @internal Function should be a method in the class
     * @internal Tagname should be lowercase and is everything after <, e.g. "?php" or "!doctype"
     * @access private
     */
    private $tag_map = array(
        '!doctype' => 'parse_doctype',
        '?' => 'parse_php',
        '?php' => 'parse_php',
        '%' => 'parse_asp',
        'style' => 'parse_style',
        'script' => 'parse_script',
        'svg' => 'parse_svg'
    );

    /**
     * Parse a HTML string (attributes)
     * @internal Gets called with ' and "
     * @return int
     */
    protected function parse_string()
    {
        if ($this->next_pos($this->doc[$this->pos], false) !== self::TOK_UNKNOWN) {
            --$this->pos;
        }
        return self::TOK_STRING;
    }

    /**
     * Parse text between tags
     * @internal Gets called between tags, uses {@link $status}[last_pos]
     * @internal Stores text in {@link $status}[text]
     */
    protected function parse_text()
    {
        $start_pos = $this->status['last_pos'] + 1;
        $len = $this->pos - $start_pos;
        $this->status['text'] = (($len > 0) ? substr($this->doc, $start_pos, $len) : '');
    }

    /**
     * Parse comment tags
     * @internal Gets called with HTML comments ("<!--")
     * @internal Stores text in {@link $status}[comment]
     * @return bool
     */
    protected function parse_comment()
    {
        $this->pos += 3;
        if ($this->next_pos('-->', false) !== self::TOK_UNKNOWN) {
            $this->status['comment'] = $this->getTokenString(1, -1);
            --$this->pos;
        } else {
            $this->status['comment'] = $this->getTokenString(1, -1);
            $this->pos += 2;
        }
        $this->status['last_pos'] = $this->pos;

        return true;
    }

    /**
     * Parse doctype tag
     * @internal Gets called with doctype ("<!doctype")
     * @internal Stores text in {@link $status}[dtd]
     * @return bool
     */
    protected function parse_doctype()
    {
        $start = $this->pos;
        if ($this->next_search('[>', false) === self::TOK_UNKNOWN) {
            if ($this->doc[$this->pos] === '['
                && (($this->next_pos(']', false) !== self::TOK_UNKNOWN) || ($this->next_pos('>', false) !== self::TOK_UNKNOWN))
            ) {
                $this->addError('Invalid doctype');
                return false;
            }

            $this->token_start = $start;
            $this->status['dtd'] = $this->getTokenString(2, -1);
            $this->status['last_pos'] = $this->pos;
            return true;
        }
        $this->addError('Invalid doctype');
        return false;
    }

    /**
     * Parse cdata tag
     * @internal Gets called with cdata ("<![cdata")
     * @internal Stores text in {@link $status}[cdata]
     * @return bool
     */
    protected function parse_cdata()
    {
        if ($this->next_pos(']]>', false) === self::TOK_UNKNOWN) {
            $this->status['cdata'] = $this->getTokenString(9, -1);
            $this->status['last_pos'] = $this->pos + 2;
            return true;
        }
        $this->addError('Invalid cdata tag');
        return false;
    }

    /**
     * Parse php tags
     * @internal Gets called with php tags ("<?php")
     * @return bool
     */
    protected function parse_php()
    {
        $start = $this->pos;
        if ($this->next_pos('?>', false) !== self::TOK_UNKNOWN) {
            $this->pos -= 2; //End of file
        }

        $len = $this->pos - 1 - $start;
        $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');
        $this->status['last_pos'] = ++$this->pos;
        return true;
    }

    /**
     * Parse asp tags
     * @internal Gets called with asp tags ("<%")
     * @return bool
     */
    protected function parse_asp()
    {
        $start = $this->pos;
        if ($this->next_pos('%>', false) !== self::TOK_UNKNOWN) {
            $this->pos -= 2; //End of file
        }

        $len = $this->pos - 1 - $start;
        $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');
        $this->status['last_pos'] = ++$this->pos;
        return true;
    }

    /**
     * Parse style tags
     * @internal Gets called with php tags ("<style>")
     * @return bool
     */
    protected function parse_style()
    {
        if ($this->parse_attributes() && ($this->token === self::TOK_TAG_CLOSE) && ($start = $this->pos) && ($this->next_pos('</style>', false) === self::TOK_UNKNOWN)) {
            $len = $this->pos - 1 - $start;
            $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');

            $this->pos += 7;
            $this->status['last_pos'] = $this->pos;
            return true;
        }
        $this->addError('No end for style tag found');
        return false;
    }

    /**
     * Parse script tags
     * @internal Gets called with php tags ("<script>")
     * @return bool
     */
    protected function parse_script()
    {
        if ($this->parse_attributes() && ($this->token === self::TOK_TAG_CLOSE) && ($start = $this->pos) && ($this->next_pos('</script>', false) === self::TOK_UNKNOWN)) {
            $len = $this->pos - 1 - $start;
            $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');

            $this->pos += 8;
            $this->status['last_pos'] = $this->pos;
            return true;
        }
        $this->addError('No end for script tag found');
        return false;
    }

    /**
     * Parse svg tags
     * @return bool
     */
    protected function parse_svg()
    {
        if ($this->parse_attributes() && ($this->token === self::TOK_TAG_CLOSE) && ($start = $this->pos) && ($this->next_pos('</svg>', false) === self::TOK_UNKNOWN)) {
            $len = $this->pos - 1 - $start;
            $this->status['text'] = (($len > 0) ? substr($this->doc, $start + 1, $len) : '');

            $this->pos += 5;
            $this->status['last_pos'] = $this->pos;
            return true;
        }
        $this->addError('No end for svg tag found');
        return false;
    }

    /**
     * Parse conditional tags (+ all conditional tags inside)
     * @internal Gets called with IE conditionals ("<![if]" and "<!--[if]")
     * @internal Stores condition in {@link $status}[tag_condition]
     * @return bool
     */
    protected function parse_conditional()
    {
        if ($this->status['closing_tag']) {
            $this->pos += 8;
        } else {
            $this->pos += ($this->status['comment'] ? 5 : 3);
            if ($this->next_pos(']', false) !== self::TOK_UNKNOWN) {
                $this->addError('"]" not found in conditional tag');
                return false;
            }
            $this->status['tag_condition'] = $this->getTokenString(0, -1);
        }

        if ($this->next_no_whitespace() !== self::TOK_TAG_CLOSE) {
            $this->addError('No ">" tag found 2 for conditional tag');
            return false;
        }

        if ($this->status['comment']) {
            $this->status['last_pos'] = $this->pos;
            if ($this->next_pos('<![endif]-->', false) !== self::TOK_UNKNOWN) {
                $this->addError('No ending tag found for conditional tag');
                $this->pos = $this->size - 1;

                $len = $this->pos - 1 - $this->status['last_pos'];
                $this->status['text'] = (($len > 0) ? substr($this->doc, $this->status['last_pos'] + 1, $len) : '');
            } else {
                $len = $this->pos - 1 - $this->status['last_pos'];
                $this->status['text'] = (($len > 0) ? substr($this->doc, $this->status['last_pos'] + 1, $len) : '');
                $this->pos += 11;
            }
        }

        $this->status['last_pos'] = $this->pos;
        return true;
    }

    /**
     * Parse attributes (names + value)
     * @internal Stores attributes in {@link $status}[attributes] (array(ATTR => VAL))
     * @return bool
     */
    protected function parse_attributes()
    {
        $attributes = array();

        while ($this->next_no_whitespace() === self::TOK_IDENTIFIER) {
            $attr = $this->getTokenString();
            if (($attr === '?') || ($attr === '%')) {
                //Probably closing tags
                break;
            }

            if ($this->next_no_whitespace() === self::TOK_EQUALS) {
                if ($this->next_no_whitespace() === self::TOK_STRING) {
                    $val = $this->getTokenString(1, -1);
                } else {
                    while ((++$this->pos < $this->size) && !isset($this->stopchar[$this->doc[$this->pos]])) {
                    }
                    --$this->pos;
                    $val = $this->getTokenString();

                    if (trim($val) === '') {
                        $this->addError('Invalid attribute value');
                        $this->status['attributes'] = $attributes;
                        return false;
                    }
                }
            } else {
//                $val = $attr;
                $val = false;
                $this->pos = ($this->token_start ? $this->token_start : $this->pos) - 1;
            }

            $attributes[$attr] = $val;
        }

        $this->status['attributes'] = $attributes;
        return true;
    }

    /**
     * Default callback for tags
     * @internal Gets called after the tagname (<html*ENTERS_HERE* attribute="value">)
     * @return bool
     */
    protected function parse_tag_default()
    {
        if ($this->status['closing_tag']) {
            $this->status['attributes'] = array();
            $this->next_no_whitespace();
        } elseif (!$this->parse_attributes()) {
            return false;
        }

        if ($this->token !== self::TOK_TAG_CLOSE) {
            if ($this->token === self::TOK_SLASH_FORWARD) {
                $this->status['self_close'] = true;
                $this->next();
            } elseif ((($this->status['tag_name'][0] === '?') && ($this->doc[$this->pos] === '?'))
                || (($this->status['tag_name'][0] === '%') && ($this->doc[$this->pos] === '%'))
            ) {
                $this->status['self_close'] = true;
                $this->pos++;

                $char = $this->doc[$this->pos];
                if (isset($this->char_map[$char]) && (!is_string($this->char_map[$char]))) {
                    $this->token = $this->char_map[$char];
                } else {
                    $this->token = self::TOK_UNKNOWN;
                }
            }/* else {
                $this->status['self_close'] = false;
            }*/
        }

        if ($this->token !== self::TOK_TAG_CLOSE) {
            $this->addError('Expected ">", but found "' . $this->getTokenString() . '"');
            if ($this->next_pos('>', false) !== self::TOK_UNKNOWN) {
                $this->addError('No ">" tag found for "' . $this->status['tag_name'] . '" tag');
                return false;
            }
        }

        return true;
    }

    /**
     * Parse tag
     * @internal Gets called after opening tag (<*ENTERS_HERE*html attribute="value">)
     * @internal Stores information about the tag in {@link $status} (comment, closing_tag, tag_name)
     * @return bool
     */
    protected function parse_tag()
    {
        $start = $this->pos;
        $this->status['self_close'] = false;
        $this->parse_text();

        $next = (($start + 1) < $this->size) ? $this->doc[$start + 1] : '';
        if ($next === '!') {
            $this->status['closing_tag'] = false;

            if (substr($this->doc, $this->pos + 2, 2) === '--') {
                $this->status['comment'] = true;

                if (($this->doc[$this->pos + 4] === '[') && (substr($this->doc_lc, $this->pos + 5, 2) === 'if')) {
                    return $this->parse_conditional();
                }
                return $this->parse_comment();
            }

            $this->status['comment'] = false;

            if ($this->doc[$this->pos + 2] === '[') {
                if (substr($this->doc_lc, $this->pos + 3, 2) === 'if') {
                    return $this->parse_conditional();
                }
                if (substr($this->doc_lc, $this->pos + 3, 5) === 'endif') {
                    $this->status['closing_tag'] = true;
                    return $this->parse_conditional();
                }
                if (substr($this->doc_lc, $this->pos + 3, 5) === 'cdata') {
                    return $this->parse_cdata();
                }
            }
        } elseif ($next === '/') {
            $this->status['closing_tag'] = true;
            ++$this->pos;
        } else {
            $this->status['closing_tag'] = false;
        }

        if ($this->next() !== self::TOK_IDENTIFIER) {
            $this->addError('Tagname expected');
            //if ($this->next_pos('>', false) === self::TOK_UNKNOWN) {
            $this->status['last_pos'] = $start - 1;
            return true;
            //} else {
            //    return false;
            //}
        }

        $tag = $this->getTokenString();
        $this->status['tag_name'] = $tag;
        $tag = strtolower($tag);

        if (isset($this->tag_map[$tag])) {
            $res = $this->{$this->tag_map[$tag]}();
        } else {
            $res = $this->parse_tag_default();
        }

        $this->status['last_pos'] = $this->pos;
        return $res;
    }

    /**
     * Parse full document
     * @return bool
     */
    protected function parse_all()
    {
        $this->errors = array();
        $this->status['last_pos'] = -1;

        $this->stopchar = $this->whitespace;
        $this->stopchar['<'] = true;
        $this->stopchar['>'] = true;

        if (($this->token === self::TOK_TAG_OPEN) || ($this->next_pos('<', false) === self::TOK_UNKNOWN)) {
            do {
                if (!$this->parse_tag()) {
                    return false;
                }
            } while ($this->next_pos('<') !== self::TOK_NULL);
        }

        $this->pos = $this->size;
        $this->parse_text();

        return true;
    }
}

/**
 * Parses a HTML document into a HTML DOM
 */
class HTML_Parser extends HTML_Parser_Base
{

    /**
     * Root object
     * @internal If string, then it will create a new instance as root
     * @var HTML_Node
     */
    public $root = 'HTML_Node';

    /**
     * Current parsing hierarchy
     * @internal Root is always at index 0, current tag is at the end of the array
     * @var HTML_Node[]
     * @access private
     */
    public $hierarchy = array();

    /**
     * Tags that don't need closing tags
     * @var array
     * @access private
     */
    protected $tags_selfclose = array(
        'area' => true,
        'base' => true,
        'basefont' => true, // deprecated
        'bgsound' => true, // deprecated
        'br' => true,
        'col' => true,
        'command' => true,
        'embed' => true,
        'frame' => true, // deprecated
        'hr' => true,
        'img' => true,
        'input' => true,
        'ins' => true, // deprecated
        'isindex' => true, // deprecated
        'keygen' => true,
        'link' => true,
        'meta' => true,
        'param' => true,
        'plaintext' => true, // deprecated
        'source' => true,
        'track' => true,
        'wbr' => true
    );

    /**
     * Class constructor
     * @param string $doc Document to be tokenized
     * @param int $pos Position to start parsing
     * @param HTML_Node $root Root node, null to auto create
     */
    public function __construct($doc = '', $pos = 0, $root = null)
    {
        if ($root === null) {
            $root = new $this->root('~root~', null);
        }
        $this->root = $root;

        parent::__construct($doc, $pos);
    }

    public function __destruct()
    {
        unset($this->root);
    }

    /**
     * Class magic invoke method, performs {@link select()}
     * @param string $query
     * @return HTML_Node|HTML_Node[]
     * @access private
     */
    public function __invoke($query = '*')
    {
        return $this->select($query);
    }

    /**
     * Class magic toString method, performs {@link HTML_Node::toString()}
     * @return string
     * @access private
     */
    public function __toString()
    {
        return $this->root->getInnerText();
    }

    /**
     * Performs a css select query on the root node
     * @param string $query
     * @param int|bool $index
     * @param bool $recursive
     * @param bool $check_self
     * @return HTML_Node|HTML_Node[]
     * @see HTML_Node::select()
     */
    public function select($query = '*', $index = false, $recursive = true, $check_self = false)
    {
        return $this->root->select($query, $index, $recursive, $check_self);
    }

    /**
     * Updates the current hierarchy status and checks for
     * correct opening/closing of tags
     * @param bool|null $self_close Is current tag self closing? Null to use {@link tags_selfclose}
     * @internal This is were most of the nodes get added
     * @access private
     */
    protected function parse_hierarchy($self_close = null)
    {
        if ($self_close === null) {
            $this->status['self_close'] = ($self_close = isset($this->tags_selfclose[strtolower($this->status['tag_name'])]));
        }

        if ($self_close) {
            if ($this->status['closing_tag']) {

                /** @var HTML_Node[] $c */
                $c = $this->hierarchy[count($this->hierarchy) - 1]->children;
                $found = false;
                for ($count = count($c), $i = $count - 1; $i >= 0; $i--) {
                    if (strcasecmp($c[$i]->tag, $this->status['tag_name']) === 0) {
                        for ($ii = $i + 1; $ii < $count; $ii++) {
                            $index = null; //Needs to be passed by ref
                            $c[$i + 1]->changeParent($c[$i], $index);
                        }
                        $c[$i]->self_close = false;

                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $this->addError('Closing tag "' . $this->status['tag_name'] . '" which is not open');
                }

            } elseif ($this->status['tag_name'][0] === '?') {
                $index = null; //Needs to be passed by ref
                $this->hierarchy[count($this->hierarchy) - 1]->addXML($this->status['tag_name'], '', $this->status['attributes'], $index);
            } elseif ($this->status['tag_name'][0] === '%') {
                $index = null; //Needs to be passed by ref
                $this->hierarchy[count($this->hierarchy) - 1]->addASP($this->status['tag_name'], '', $this->status['attributes'], $index);
            } else {
                $index = null; //Needs to be passed by ref
                $this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);
            }
        } elseif ($this->status['closing_tag']) {
            $found = false;
            for ($count = count($this->hierarchy), $i = $count - 1; $i >= 0; $i--) {
                if (strcasecmp($this->hierarchy[$i]->tag, $this->status['tag_name']) === 0) {

                    for ($ii = ($count - $i - 1); $ii >= 0; $ii--) {
                        $e = array_pop($this->hierarchy);
                        if ($ii > 0) {
                            $this->addError('Closing tag "' . $this->status['tag_name'] . '" while "' . $e->tag . '" is not closed yet');
                        }
                    }

                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->addError('Closing tag "' . $this->status['tag_name'] . '" which is not open');
            }

        } else {
            $index = null; //Needs to be passed by ref
            $this->hierarchy[] = $this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);
        }
    }

    protected function parse_cdata()
    {
        if (!parent::parse_cdata()) {
            return false;
        }

        $index = null; //Needs to be passed by ref
        $this->hierarchy[count($this->hierarchy) - 1]->addCDATA($this->status['cdata'], $index);
        return true;
    }

    protected function parse_comment()
    {
        if (!parent::parse_comment()) {
            return false;
        }

        $index = null; //Needs to be passed by ref
        $this->hierarchy[count($this->hierarchy) - 1]->addComment($this->status['comment'], $index);
        return true;
    }

    protected function parse_conditional()
    {
        if (!parent::parse_conditional()) {
            return false;
        }

        if ($this->status['comment']) {
            $index = null; //Needs to be passed by ref
            $e = $this->hierarchy[count($this->hierarchy) - 1]->addConditional($this->status['tag_condition'], true, $index);
            if ($this->status['text'] !== '') {
                $index = null; //Needs to be passed by ref
                $e->addText($this->status['text'], $index);
            }
        } else {
            if ($this->status['closing_tag']) {
                $this->parse_hierarchy(false);
            } else {
                $index = null; //Needs to be passed by ref
                $this->hierarchy[] = $this->hierarchy[count($this->hierarchy) - 1]->addConditional($this->status['tag_condition'], false, $index);
            }
        }

        return true;
    }

    protected function parse_doctype()
    {
        if (!parent::parse_doctype()) {
            return false;
        }

        $index = null; //Needs to be passed by ref
        $this->hierarchy[count($this->hierarchy) - 1]->addDoctype($this->status['dtd'], $index);
        return true;
    }

    protected function parse_php()
    {
        if (!parent::parse_php()) {
            return false;
        }

        $index = null; //Needs to be passed by ref
        $this->hierarchy[count($this->hierarchy) - 1]->addXML('php', $this->status['text'], array(), $index);
        return true;
    }

    protected function parse_asp()
    {
        if (!parent::parse_asp()) {
            return false;
        }

        $index = null; //Needs to be passed by ref
        $this->hierarchy[count($this->hierarchy) - 1]->addASP('', $this->status['text'], array(), $index);
        return true;
    }

    protected function parse_script()
    {
        if (!parent::parse_script()) {
            return false;
        }

        $index = null; //Needs to be passed by ref
        $e = $this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);
        if ($this->status['text'] !== '') {
            $index = null; //Needs to be passed by ref
            $e->addText($this->status['text'], $index);
        }
        return true;
    }

    protected function parse_svg()
    {
        if (!parent::parse_svg()) {
            return false;
        }

        $index = null; //Needs to be passed by ref
        $e = $this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);
        if ($this->status['text'] !== '') {
            $index = null; //Needs to be passed by ref
            $e->addText($this->status['text'], $index);
        }
        return true;
    }

    protected function parse_style()
    {
        if (!parent::parse_style()) {
            return false;
        }

        $index = null; //Needs to be passed by ref
        $e = $this->hierarchy[count($this->hierarchy) - 1]->addChild($this->status, $index);
        if ($this->status['text'] !== '') {
            $index = null; //Needs to be passed by ref
            $e->addText($this->status['text'], $index);
        }
        return true;
    }

    protected function parse_tag_default()
    {
        if (!parent::parse_tag_default()) {
            return false;
        }

        $this->parse_hierarchy($this->status['self_close'] ? true : null);
        return true;
    }

    protected function parse_text()
    {
        parent::parse_text();
        if ($this->status['text'] !== '') {
            $index = null; //Needs to be passed by ref
            $this->hierarchy[count($this->hierarchy) - 1]->addText($this->status['text'], $index);
        }
    }

    public function parse_all()
    {
        $this->hierarchy = array($this->root);
        return parent::parse_all() ? $this->root : false;
    }
}

/**
 * HTML5 specific parser (adds support for omittable closing tags)
 */
class HTML_Parser_HTML5 extends HTML_Parser
{

    /**
     * Tags with ommitable closing tags
     * @var array array('tag2' => 'tag1') will close tag1 if following (not child) tag is tag2
     * @access private
     */
    public $tags_optional_close = array(
        //Current tag => Previous tag
        'address' => array('p' => true),
        'article' => array('p' => true),
        'aside' => array('p' => true),
        'blockquote' => array('p' => true),
        'body' => array('head' => true),
        'dd' => array('dd' => true, 'dt' => true),
        'dir' => array('p' => true),
        'div' => array('p' => true),
        'dl' => array('p' => true),
        'dt' => array('dd' => true, 'dt' => true),
        'fieldset' => array('p' => true),
        'footer' => array('p' => true),
        'form' => array('p' => true),
        'h1' => array('p' => true),
        'h2' => array('p' => true),
        'h3' => array('p' => true),
        'h4' => array('p' => true),
        'h5' => array('p' => true),
        'h6' => array('p' => true),
        'header' => array('p' => true),
        'hgroup' => array('p' => true),
        'hr' => array('p' => true),
        'li' => array('li' => true),
        'main' => array('p' => true),
        'menu' => array('p' => true),
        'nav' => array('p' => true),
        'ol' => array('p' => true),
        'optgroup' => array('optgroup' => true, 'option' => true),
        'option' => array('option'),
        'p' => array('p' => true),
        'pre' => array('p' => true),
        'rb' => array('rb' => true, 'rp' => true, 'rt' => true, 'rtc' => true),
        'rp' => array('rb' => true, 'rp' => true, 'rt' => true, 'rtc' => true),
        'rt' => array('rb' => true, 'rp' => true, 'rt' => true, 'rtc' => true),
        'rtc' => array('rb' => true, 'rp' => true, 'rt' => true, 'rtc' => true),
        'section' => array('p' => true),
        'table' => array('p' => true),
        'tbody' => array('tbody' => true, 'tfoot' => true, 'thead' => true),
        'td' => array('td' => true, 'th' => true),
        'tfoot' => array('tbody' => true, 'thead' => true),
        'th' => array('td' => true, 'th' => true),
        'tr' => array('tr' => true),
        'ul' => array('p' => true)
    );

    /**
     * @param bool $self_close
     */
    protected function parse_hierarchy($self_close = null)
    {
        $tag_curr = strtolower($this->status['tag_name']);
        if ($self_close === null) {
            $this->status['self_close'] = ($self_close = isset($this->tags_selfclose[$tag_curr]));
        }

        if (!($self_close || $this->status['closing_tag'])) {
            $tag_prev = strtolower($this->hierarchy[count($this->hierarchy) - 1]->tag);
            if (isset($this->tags_optional_close[$tag_curr][$tag_prev])) {
                array_pop($this->hierarchy);
            }
        }

        parent::parse_hierarchy($self_close);
    }
}
