<?php
/**
* Author Markus Baker: http://www.lastcraft.com
* Version adapted from Simple Test: http://sourceforge.net/projects/simpletest/
* @author Marcus Baker
* @package JPSpan
* @subpackage Lexer
* @version $Id: Lexer.php,v 1.1 2004/11/10 16:04:47 harryf Exp $
*/
/**#@+
 * lexer mode constant
 */
define("JPSPAN_LEXER_ENTER", 1);
define("JPSPAN_LEXER_MATCHED", 2);
define("JPSPAN_LEXER_UNMATCHED", 3);
define("JPSPAN_LEXER_EXIT", 4);
define("JPSPAN_LEXER_SPECIAL", 5);
/**#@-*/

/**
 *    Compounded regular expression. Any of
 *    the contained patterns could match and
 *    when one does it's label is returned.
 *    @package JPSpan
 *    @subpackage Lexer
 */
class JPSpan_LexerParallelRegex {
	var $_patterns;
	var $_labels;
	var $_regex;
	var $_case;
	
	/**
	 *    Constructor. Starts with no patterns.
	 *    @param boolean $case    True for case sensitive, false
	 *                            for insensitive.
	 *    @access public
	 */
	function JPSpan_LexerParallelRegex($case) {
		$this->_case = $case;
		$this->_patterns = array();
		$this->_labels = array();
		$this->_regex = null;
	}
	
	/**
	 *    Adds a pattern with an optional label.
	 *    @param string $pattern      Perl style regex, but ( and )
	 *                                lose the usual meaning.
	 *    @param string $label        Label of regex to be returned
	 *                                on a match.
	 *    @access public
	 */
	function addPattern($pattern, $label = true) {
		$count = count($this->_patterns);
		$this->_patterns[$count] = $pattern;
		$this->_labels[$count] = $label;
		$this->_regex = null;
	}
	
	/**
	 *    Attempts to match all patterns at once against
	 *    a string.
	 *    @param string $subject      String to match against.
	 *    @param string $match        First matched portion of
	 *                                subject.
	 *    @return boolean             True on success.
	 *    @access public
	 */
	function match($subject, &$match) {
		if (count($this->_patterns) == 0) {
			return false;
		}
		if (! preg_match($this->_getCompoundedRegex(), $subject, $matches)) {
			$match = "";
			return false;
		}
		$match = $matches[0];
		for ($i = 1; $i < count($matches); $i++) {
			if ($matches[$i]) {
				return $this->_labels[$i - 1];
			}
		}
		return true;
	}
	
	/**
	 *    Compounds the patterns into a single
	 *    regular expression separated with the
	 *    "or" operator. Caches the regex.
	 *    Will automatically escape (, ) and / tokens.
	 *    @param array $patterns    List of patterns in order.
	 *    @access private
	 */
	function _getCompoundedRegex() {
		if ($this->_regex == null) {
			for ($i = 0; $i < count($this->_patterns); $i++) {
				$this->_patterns[$i] = '(' . str_replace(
						array('/', '(', ')'),
						array('\/', '\(', '\)'),
						$this->_patterns[$i]) . ')';
			}
			$this->_regex = "/" . implode("|", $this->_patterns) . "/" . $this->_getPerlMatchingFlags();
		}
		return $this->_regex;
	}
	
	/**
	 *    Accessor for perl regex mode flags to use.
	 *    @return string       Perl regex flags.
	 *    @access private
	 */
	function _getPerlMatchingFlags() {
		return ($this->_case ? "msS" : "msSi");
	}
}

/**
 *    States for a stack machine.
 *    @package JPSpan
 *    @subpackage Lexer
 */
class JPSpan_LexerStateStack {
	var $_stack;
	
	/**
	 *    Constructor. Starts in named state.
	 *    @param string $start        Starting state name.
	 *    @access public
	 */
	function JPSpan_LexerStateStack($start) {
		$this->_stack = array($start);
	}
	
	/**
	 *    Accessor for current state.
	 *    @return string       State.
	 *    @access public
	 */
	function getCurrent() {
		return $this->_stack[count($this->_stack) - 1];
	}
	
	/**
	 *    Adds a state to the stack and sets it
	 *    to be the current state.
	 *    @param string $state        New state.
	 *    @access public
	 */
	function enter($state) {
		array_push($this->_stack, $state);
	}
	
	/**
	 *    Leaves the current state and reverts
	 *    to the previous one.
	 *    @return boolean    False if we drop off
	 *                       the bottom of the list.
	 *    @access public
	 */
	function leave() {
		if (count($this->_stack) == 1) {
			return false;
		}
		array_pop($this->_stack);
		return true;
	}
}

/**
 *    Accepts text and breaks it into tokens.
 *    Some optimisation to make the sure the
 *    content is only scanned by the PHP regex
 *    parser once. Lexer modes must not start
 *    with leading underscores.
 *    @package JPSpan
 *    @subpackage Lexer
 */
class JPSpan_Lexer {
	var $_regexes;
	var $_parser;
	var $_mode;
	var $_mode_handlers;
	var $_case;
	
	/**
	 *    Sets up the lexer in case insensitive matching
	 *    by default.
	 *    @param JPSpan_Parser $parser  Handling strategy by
	 *                                    reference.
	 *    @param string $start            Starting handler.
	 *    @param boolean $case            True for case sensitive.
	 *    @access public
	 */
	function JPSpan_Lexer(&$parser, $start = "accept", $case = false) {
		$this->_case = $case;
		$this->_regexes = array();
		$this->_parser = &$parser;
		$this->_mode = &new JPSpan_LexerStateStack($start);
		$this->_mode_handlers = array();
	}
	
	/**
	 *    Adds a token search pattern for a particular
	 *    parsing mode. The pattern does not change the
	 *    current mode.
	 *    @param string $pattern      Perl style regex, but ( and )
	 *                                lose the usual meaning.
	 *    @param string $mode         Should only apply this
	 *                                pattern when dealing with
	 *                                this type of input.
	 *    @access public
	 */
	function addPattern($pattern, $mode = "accept") {
		if (! isset($this->_regexes[$mode])) {
			$this->_regexes[$mode] = new JPSpan_LexerParallelRegex($this->_case);
		}
		$this->_regexes[$mode]->addPattern($pattern);
	}
	
	/**
	 *    Adds a pattern that will enter a new parsing
	 *    mode. Useful for entering parenthesis, strings,
	 *    tags, etc.
	 *    @param string $pattern      Perl style regex, but ( and )
	 *                                lose the usual meaning.
	 *    @param string $mode         Should only apply this
	 *                                pattern when dealing with
	 *                                this type of input.
	 *    @param string $new_mode     Change parsing to this new
	 *                                nested mode.
	 *    @access public
	 */
	function addEntryPattern($pattern, $mode, $new_mode) {
		if (! isset($this->_regexes[$mode])) {
			$this->_regexes[$mode] = new JPSpan_LexerParallelRegex($this->_case);
		}
		$this->_regexes[$mode]->addPattern($pattern, $new_mode);
	}
	
	/**
	 *    Adds a pattern that will exit the current mode
	 *    and re-enter the previous one.
	 *    @param string $pattern      Perl style regex, but ( and )
	 *                                lose the usual meaning.
	 *    @param string $mode         Mode to leave.
	 *    @access public
	 */
	function addExitPattern($pattern, $mode) {
		if (! isset($this->_regexes[$mode])) {
			$this->_regexes[$mode] = new JPSpan_LexerParallelRegex($this->_case);
		}
		$this->_regexes[$mode]->addPattern($pattern, "__exit");
	}
	
	/**
	 *    Adds a pattern that has a special mode. Acts as an entry
	 *    and exit pattern in one go, effectively calling a special
	 *    parser handler for this token only.
	 *    @param string $pattern      Perl style regex, but ( and )
	 *                                lose the usual meaning.
	 *    @param string $mode         Should only apply this
	 *                                pattern when dealing with
	 *                                this type of input.
	 *    @param string $special      Use this mode for this one token.
	 *    @access public
	 */
	function addSpecialPattern($pattern, $mode, $special) {
		if (! isset($this->_regexes[$mode])) {
			$this->_regexes[$mode] = new JPSpan_LexerParallelRegex($this->_case);
		}
		$this->_regexes[$mode]->addPattern($pattern, "_$special");
	}
	
	/**
	 *    Adds a mapping from a mode to another handler.
	 *    @param string $mode        Mode to be remapped.
	 *    @param string $handler     New target handler.
	 *    @access public
	 */
	function mapHandler($mode, $handler) {
		$this->_mode_handlers[$mode] = $handler;
	}
	
	/**
	 *    Splits the page text into tokens. Will fail
	 *    if the handlers report an error or if no
	 *    content is consumed. If successful then each
	 *    unparsed and parsed token invokes a call to the
	 *    held listener.
	 *    @param string $raw        Raw HTML text.
	 *    @return boolean           True on success, else false.
	 *    @access public
	 */
	function parse($raw) {
		if (! isset($this->_parser)) {
			return false;
		}
		$length = strlen($raw);
		while (is_array($parsed = $this->_reduce($raw))) {
			list($unmatched, $matched, $mode) = $parsed;
			if (! $this->_dispatchTokens($unmatched, $matched, $mode)) {
				return false;
			}
			if (strlen($raw) == $length) {
				return false;
			}
			$length = strlen($raw);
		}
		if (!$parsed) {
			return false;
		}
		return $this->_invokeParser($raw, JPSPAN_LEXER_UNMATCHED);
	}
	
	/**
	 *    Sends the matched token and any leading unmatched
	 *    text to the parser changing the lexer to a new
	 *    mode if one is listed.
	 *    @param string $unmatched    Unmatched leading portion.
	 *    @param string $matched      Actual token match.
	 *    @param string $mode         Mode after match. A boolean
	 *                                false mode causes no change.
	 *    @return boolean             False if there was any error
	 *                                from the parser.
	 *    @access private
	 */
	function _dispatchTokens($unmatched, $matched, $mode = false) {
		if (! $this->_invokeParser($unmatched, JPSPAN_LEXER_UNMATCHED)) {
			return false;
		}
		if ($this->_isModeEnd($mode)) {
			if (! $this->_invokeParser($matched, JPSPAN_LEXER_EXIT)) {
				return false;
			}
			return $this->_mode->leave();
		}
		if ($this->_isSpecialMode($mode)) {
			$this->_mode->enter($this->_decodeSpecial($mode));
			if (! $this->_invokeParser($matched, JPSPAN_LEXER_SPECIAL)) {
				return false;
			}
			return $this->_mode->leave();
		}
		if (is_string($mode)) {
			$this->_mode->enter($mode);
			return $this->_invokeParser($matched, JPSPAN_LEXER_ENTER);
		}
		return $this->_invokeParser($matched, JPSPAN_LEXER_MATCHED);
	}
	
	/**
	 *    Tests to see if the new mode is actually to leave
	 *    the current mode and pop an item from the matching
	 *    mode stack.
	 *    @param string $mode    Mode to test.
	 *    @return boolean        True if this is the exit mode.
	 *    @access private
	 */
	function _isModeEnd($mode) {
		return ($mode === "__exit");
	}
	
	/**
	 *    Test to see if the mode is one where this mode
	 *    is entered for this token only and automatically
	 *    leaves immediately afterwoods.
	 *    @param string $mode    Mode to test.
	 *    @return boolean        True if this is the exit mode.
	 *    @access private
	 */
	function _isSpecialMode($mode) {
		return (strncmp($mode, "_", 1) == 0);
	}
	
	/**
	 *    Strips the magic underscore marking single token
	 *    modes.
	 *    @param string $mode    Mode to decode.
	 *    @return string         Underlying mode name.
	 *    @access private
	 */
	function _decodeSpecial($mode) {
		return substr($mode, 1);
	}
	
	/**
	 *    Calls the parser method named after the current
	 *    mode. Empty content will be ignored. The lexer
	 *    has a parser handler for each mode in the lexer.
	 *    @param string $content        Text parsed.
	 *    @param boolean $is_match      Token is recognised rather
	 *                                  than unparsed data.
	 *    @access private
	 */
	function _invokeParser($content, $is_match) {
		if (($content === "") || ($content === false)) {
			return true;
		}
		$handler = $this->_mode->getCurrent();
		if (isset($this->_mode_handlers[$handler])) {
			$handler = $this->_mode_handlers[$handler];
		}
		return $this->_parser->$handler($content, $is_match);
	}
	
	/**
	 *    Tries to match a chunk of text and if successful
	 *    removes the recognised chunk and any leading
	 *    unparsed data. Empty strings will not be matched.
	 *    @param string $raw         The subject to parse. This is the
	 *                               content that will be eaten.
	 *    @return array              Three item list of unparsed
	 *                               content followed by the
	 *                               recognised token and finally the
	 *                               action the parser is to take.
	 *                               True if no match, false if there
	 *                               is a parsing error.
	 *    @access private
	 */
	function _reduce(&$raw) {
		if (! isset($this->_regexes[$this->_mode->getCurrent()])) {
			return false;
		}
		if ($raw === "") {
			return true;
		}
		if ($action = $this->_regexes[$this->_mode->getCurrent()]->match($raw, $match)) {
			$unparsed_character_count = strpos($raw, $match);
			$unparsed = substr($raw, 0, $unparsed_character_count);
			$raw = substr($raw, $unparsed_character_count + strlen($match));
			return array($unparsed, $match, $action);
		}
		return true;
	}
}
?>