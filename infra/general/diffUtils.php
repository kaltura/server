<?php
/**
 *  FINE granularity DIFF Util
 *
 *  @package infra
 *  @subpackage utils
 */

/**
 *
 * Computes a set of instructions to convert the content of
 * one string into another.
 *
 * Copyright (c) 2011 Raymond Hill (http://raymondhill.net/blog/?p=441)
 *
 * Licensed under The MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @copyright Copyright 2011 (c) Raymond Hill (http://raymondhill.net/blog/?p=441)
 * @link http://www.raymondhill.net/finediff/
 * @version 0.6
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)

 * Usage (simplest):
 *
 *   include 'finediff.php';
 *
 *   // for the stock stack, granularity values are:
 *   // FineDiff::$paragraphGranularity = paragraph/line level
 *   // FineDiff::$sentenceGranularity = sentence level
 *   // FineDiff::$wordGranularity = word level
 *   // FineDiff::$characterGranularity = character level [default]
 *
 *   $opcodes = FineDiff::getDiffOpcodes($from_text, $to_text [, $granularityStack = null] );
 *   // store opcodes for later use...
 *
 *   ...
 *
 *   // restore $to_text from $from_text + $opcodes
 *   include 'finediff.php';
 *   $to_text = FineDiff::renderToTextFromOpcodes($from_text, $opcodes);
 *
 *  // render an html view of the diffs
 *  FineDiffHTML::renderDiffToHTMLFromOpcodes($to_text, $opcodes);
 *
 */

/**
 * Persisted opcodes (string) are a sequence of atomic opcode.
 * A single opcode can be one of the following:
 *   c | c{n} | d | d{n} | i:{c} | i{length}:{s}
 *   'c'        = copy one character from source
 *   'c{n}'     = copy n characters from source
 *   'd'        = skip one character from source
 *   'd{n}'     = skip n characters from source
 *   'i:{c}     = insert character 'c'
 *   'i{n}:{s}' = insert string s, which is of length n
 *
 */

class FineDiff
{

	/**------------------------------------------------------------------------
	 *
	 * Declare attributes
	 *
	 */
	public $granularityStack;
	protected $edits;
	protected $from_text;
	public $encoding;
	public $greed;


	/**
	 * Constructor
	 * ...
	 * The $granularityStack allows FineDiff to be configurable so that
	 * a particular stack tailored to the specific content of a document can
	 * be passed.
	 */
	public function __construct($from_text = '', $to_text = '', $granularityStack = null, $greed = 4, $encoding = null)
	{
		// setup stack for generic text documents by default
		$this->granularityStack = $granularityStack ? $granularityStack : static::$characterGranularity;
		$this->edits = array();
		$this->from_text = $from_text;
		$this->greed = $greed;
		$this->encoding = $encoding;
		if ($encoding === null)
		{
			$this->encoding = mb_internal_encoding();
		}
		$this->doDiff($from_text, $to_text);
	}

	public function getOps()
	{
		return $this->edits;
	}

	public function getOpcodes()
	{
		$opcodes = array();
		foreach ($this->edits as $edit)
		{
			$opcodes[] = $this->getOpcode($edit);
		}
		return implode('', $opcodes);
	}

	public function getOpcode($edit)
	{
		$to_len = $edit->getToLen();
		$from_len = $edit->getFromLen();

		if ($edit instanceof FineDiffCopyOp)
		{
			if ($to_len === 1)
			{
				return 'c';
			}
			return 'c' . $to_len;
		}
		else if ($edit instanceof FineDiffDeleteOp)
		{
			if ($from_len === 1)
			{
				return 'd';
			}
			return 'd' . $from_len;
		}
		else if ($edit instanceof FineDiffInsertOp)
		{
			if ($to_len === 1)
			{
				return 'i:' . $edit->getText();
			}
			return 'i' . $to_len . ':' . $edit->getText();
		}
		else /* if ( $edit instanceof FineDiffReplaceOp ) */
		{
			if ($from_len === 1)
			{
				$del_opcode = 'd';
			}
			else
			{
				$del_opcode = 'd' . $from_len;
			}

			if ($to_len === 1)
			{
				return $del_opcode . 'i:' . $edit->getText() . '';
			}
			return $del_opcode . 'i' . $to_len . ':' . $edit->getText();
		}
	}

	/**------------------------------------------------------------------------
	 * Return an opcodes string describing the diff between a "From" and a
	 * "To" string
	 */
	public static function getDiffOpcodes($from, $to, $granularities = null, $greed = 4, $encoding = null)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}
		$diff = new FineDiff($from, $to, $granularities, $greed, $encoding);
		return $diff->getOpcodes();
	}

	/**------------------------------------------------------------------------
	 * Return an iterable collection of diff ops from an opcodes string
	 */
	public static function getDiffOpsFromOpcodes($opcodes, $encoding = null)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}
		$diffops = new FineDiffOps();
		static::renderFromOpcodes(null, $opcodes, array($diffops, 'appendOpcode'), $encoding);
		return $diffops->edits;
	}

	/**------------------------------------------------------------------------
	 * Re-create the "To" string from the "From" string and an "Opcodes" string
	 */
	public static function renderToTextFromOpcodes($from, $opcodes, $encoding = null)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}
		ob_start();
		static::renderFromOpcodes($from, $opcodes, 'FineDiff::renderToTextFromOpcode', $encoding);
		return ob_get_clean();
	}

	/**------------------------------------------------------------------------
	 * Generic opcodes parser, user must supply callback for handling
	 * single opcode
	 */
	public static function renderFromOpcodes($from, $opcodes, $callback, $encoding = null, $textToEntities = true)
	{
		if (!is_callable($callback))
		{
			return;
		}
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}
		$opcodes_len = mb_strlen($opcodes, $encoding);
		$from_offset = $opcodes_offset = 0;
		while ($opcodes_offset < $opcodes_len)
		{
			$opcode = mb_substr($opcodes, $opcodes_offset, 1, $encoding);
			$opcodes_offset++;
			$n = intval(mb_substr($opcodes, $opcodes_offset, NULL, $encoding));
			if ($n)
			{
				$opcodes_offset += mb_strlen(strval($n), $encoding);
			}
			else
			{
				$n = 1;
			}
			if ($opcode === 'c')
			{ // copy n characters from source
				call_user_func($callback, 'c', $from, $from_offset, $n, $encoding, $textToEntities);
				$from_offset += $n;
			}
			else if ($opcode === 'd')
			{ // delete n characters from source
				call_user_func($callback, 'd', $from, $from_offset, $n, $encoding, $textToEntities);
				$from_offset += $n;
			}
			else /* if ( $opcode === 'i' ) */
			{ // insert n characters from opcodes
				call_user_func($callback, 'i', $opcodes, $opcodes_offset + 1, $n, $encoding, $textToEntities);
				$opcodes_offset += 1 + $n;
			}
		}
	}

	/**
	 * Stock granularity stacks and delimiters
	 */

	const paragraphDelimiters = "\n\r";
	public static $paragraphGranularity = array(
		self::paragraphDelimiters
	);
	const sentenceDelimiters = ".\n\r";
	public static $sentenceGranularity = array(
		self::paragraphDelimiters,
		self::sentenceDelimiters
	);
	const wordDelimiters = " \t.\n\r";
	public static $wordGranularity = array(
		self::paragraphDelimiters,
		self::sentenceDelimiters,
		self::wordDelimiters
	);
	const characterDelimiters = "";
	public static $characterGranularity = array(
		self::paragraphDelimiters,
		self::sentenceDelimiters,
		self::wordDelimiters,
		self::characterDelimiters
	);

	public static $textStack = array(
		".",
		" \t.\n\r",
		""
	);

	/**------------------------------------------------------------------------
	 *
	 * Private section
	 *
	 */

	/**
	 * Entry point to compute the diff.
	 */
	protected function doDiff($from_text, $to_text)
	{
		$this->last_edit = false;
		$this->stackpointer = 0;
		$this->from_text = $from_text;
		$this->from_offset = 0;
		// can't diff without at least one granularity specifier
		if (empty($this->granularityStack))
		{
			return;
		}
		$this->_processGranularity($from_text, $to_text);
	}

	/**
	 * This is the recursive function which is responsible for
	 * handling/increasing granularity.
	 *
	 * Incrementally increasing the granularity is key to compute the
	 * overall diff in a very efficient way.
	 */
	protected function _processGranularity($from_segment, $to_segment)
	{
		$delimiters = $this->granularityStack[$this->stackpointer++];
		$has_next_stage = $this->stackpointer < count($this->granularityStack);
		foreach (static::doFragmentDiff($from_segment, $to_segment, $delimiters, $this->greed, $this->encoding) as $fragment_edit)
		{
			// increase granularity
			if ($fragment_edit instanceof FineDiffReplaceOp && $has_next_stage)
			{
				$this->_processGranularity(
					mb_substr($this->from_text, $this->from_offset, $fragment_edit->getFromLen(), $this->encoding),
					$fragment_edit->getText()
				);
			}
			// fuse copy ops whenever possible
			else if ($fragment_edit instanceof FineDiffCopyOp && $this->last_edit instanceof FineDiffCopyOp)
			{
				$this->edits[count($this->edits) - 1]->increase($fragment_edit->getFromLen());
				$this->from_offset += $fragment_edit->getFromLen();
			}
			else
			{
				/* $fragment_edit instanceof FineDiffCopyOp */
				/* $fragment_edit instanceof FineDiffDeleteOp */
				/* $fragment_edit instanceof FineDiffInsertOp */
				$this->edits[] = $this->last_edit = $fragment_edit;
				$this->from_offset += $fragment_edit->getFromLen();
			}
		}
		$this->stackpointer--;
	}

	/**
	 * This is the core algorithm which actually perform the diff itself,
	 * fragmenting the strings as per specified delimiters.
	 *
	 * This function is naturally recursive, however for performance purpose
	 * a local job queue is used instead of outright recursivity.
	 */
	protected static function doFragmentDiff($from_text, $to_text, $delimiters, $greed = 4, $encoding = null)
	{
		// Empty delimiter means character-level diffing.
		// In such case, use code path optimized for character-level
		// diffing.
		if (empty($delimiters))
		{
			return static::doCharDiff($from_text, $to_text, $greed, $encoding);
		}

		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}

		$result = array();

		// fragment-level diffing
		$from_text_len = mb_strlen($from_text, $encoding);
		$to_text_len = mb_strlen($to_text, $encoding);
		$from_fragments = static::extractFragments($from_text, $delimiters, $encoding);
		$to_fragments = static::extractFragments($to_text, $delimiters, $encoding);

		$jobs = array(array(0, $from_text_len, 0, $to_text_len));

		$cached_array_keys = array();

		while ($job = array_pop($jobs))
		{

			// get the segments which must be diff'ed
			list($from_segment_start, $from_segment_end, $to_segment_start, $to_segment_end) = $job;

			// catch easy cases first
			$from_segment_length = $from_segment_end - $from_segment_start;
			$to_segment_length = $to_segment_end - $to_segment_start;
			if (!$from_segment_length || !$to_segment_length)
			{
				if ($from_segment_length)
				{
					$result[$from_segment_start * 4] = new FineDiffDeleteOp($from_segment_length);
				}
				else if ($to_segment_length)
				{
					$result[$from_segment_start * 4 + 1] = new FineDiffInsertOp(mb_substr($to_text, $to_segment_start, $to_segment_length), $encoding);
				}
				continue;
			}

			// find longest copy operation for the current segments
			$best_copy_length = 0;

			$from_base_fragment_index = $from_segment_start;

			$cached_array_keys_for_current_segment = array();

			while ($from_base_fragment_index < $from_segment_end)
			{
				$from_base_fragment = $from_fragments[$from_base_fragment_index];
				$from_base_fragment_length = mb_strlen($from_base_fragment, $encoding);
				// performance boost: cache array keys
				if (!isset($cached_array_keys_for_current_segment[$from_base_fragment]))
				{
					if (!isset($cached_array_keys[$from_base_fragment]))
					{
						$to_all_fragment_indices = $cached_array_keys[$from_base_fragment] = array_keys($to_fragments, $from_base_fragment, true);
					}
					else
					{
						$to_all_fragment_indices = $cached_array_keys[$from_base_fragment];
					}
					// get only indices which falls within current segment
					if ($to_segment_start > 0 || $to_segment_end < $to_text_len)
					{
						$to_fragment_indices = array();
						foreach ($to_all_fragment_indices as $to_fragment_index)
						{
							if ($to_fragment_index < $to_segment_start)
							{
								continue;
							}
							if ($to_fragment_index >= $to_segment_end)
							{
								break;
							}
							$to_fragment_indices[] = $to_fragment_index;
						}
						$cached_array_keys_for_current_segment[$from_base_fragment] = $to_fragment_indices;
					}
					else
					{
						$to_fragment_indices = $to_all_fragment_indices;
					}
				}
				else
				{
					$to_fragment_indices = $cached_array_keys_for_current_segment[$from_base_fragment];
				}
				// iterate through collected indices
				foreach ($to_fragment_indices as $to_base_fragment_index)
				{
					$fragment_index_offset = $from_base_fragment_length;
					// iterate until no more match
					for (; ;)
					{
						$fragment_from_index = $from_base_fragment_index + $fragment_index_offset;
						if ($fragment_from_index >= $from_segment_end)
						{
							break;
						}
						$fragment_to_index = $to_base_fragment_index + $fragment_index_offset;
						if ($fragment_to_index >= $to_segment_end)
						{
							break;
						}
						if ($from_fragments[$fragment_from_index] !== $to_fragments[$fragment_to_index])
						{
							break;
						}
						$fragment_length = mb_strlen($from_fragments[$fragment_from_index], $encoding);
						$fragment_index_offset += $fragment_length;
					}
					if ($fragment_index_offset > $best_copy_length)
					{
						// if the matching string is just made up of delimiters then don't count it as a match. This prevents an
						// excessive number of whitespaces being seen as matches and therefore breaking up a long replace segment
						// to no useful purpose.
						if ($fragment_index_offset > $from_base_fragment_length || static::mb_strspn($from_base_fragment, $delimiters, 0, null, $encoding) === 0)
						{
							$best_copy_length = $fragment_index_offset;
							$best_from_start = $from_base_fragment_index;
							$best_to_start = $to_base_fragment_index;
						}
					}
				}
				$from_base_fragment_index += $from_base_fragment_length;
				// If match is larger than half segment size, no point trying to find better
				// TODO: Really?
				if ($best_copy_length >= $from_segment_length / 2)
				{
					break;
				}
				// no point to keep looking if what is left is less than
				// current best match
				if ($from_base_fragment_index + $best_copy_length >= $from_segment_end)
				{
					break;
				}
			}

			if ($best_copy_length)
			{
				$jobs[] = array($from_segment_start, $best_from_start, $to_segment_start, $best_to_start);
				$result[$best_from_start * 4 + 2] = new FineDiffCopyOp($best_copy_length);
				$jobs[] = array($best_from_start + $best_copy_length, $from_segment_end, $best_to_start + $best_copy_length, $to_segment_end);
			}
			else
			{
				$result[$from_segment_start * 4] = new FineDiffReplaceOp($from_segment_length, mb_substr($to_text, $to_segment_start, $to_segment_length, $encoding), $encoding);
			}
		}

		ksort($result, SORT_NUMERIC);
		return array_values($result);
	}

	/**
	 * Perform a character-level diff.
	 *
	 * The algorithm is quite similar to doFragmentDiff(), except that
	 * the code path is optimized for character-level diff -- strpos() is
	 * used to find out the longest common subequence of characters.
	 *
	 * We try to find a match using the longest possible subsequence, which
	 * is at most the length of the shortest of the two strings, then incrementally
	 * reduce the size until a match is found.
	 *
	 * I still need to study more the performance of this function. It
	 * appears that for long strings, the generic doFragmentDiff() is more
	 * performant. For word-sized strings, doCharDiff() is somewhat more
	 * performant.
	 */
	protected static function doCharDiff($from_text, $to_text, $greed = 4, $encoding = null)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}

		$result = array();
		$jobs = array(array(0, mb_strlen($from_text, $encoding), 0, mb_strlen($to_text, $encoding)));
		while ($job = array_pop($jobs))
		{
			// get the segments which must be diff'ed
			list($from_segment_start, $from_segment_end, $to_segment_start, $to_segment_end) = $job;
			$from_segment_len = $from_segment_end - $from_segment_start;
			$to_segment_len = $to_segment_end - $to_segment_start;

			// catch easy cases first
			if (!$from_segment_len || !$to_segment_len)
			{
				if ($from_segment_len)
				{
					$result[$from_segment_start * 4 + 0] = new FineDiffDeleteOp($from_segment_len);
				}
				else if ($to_segment_len)
				{
					$result[$from_segment_start * 4 + 1] = new FineDiffInsertOp(mb_substr($to_text, $to_segment_start, $to_segment_len, $encoding), $encoding);
				}
				continue;
			}
			if ($from_segment_len >= $to_segment_len)
			{
				$copy_len = $to_segment_len;
				while ($copy_len)
				{
					$to_copy_start = $to_segment_start;
					$to_copy_start_max = $to_segment_end - $copy_len;
					while ($to_copy_start <= $to_copy_start_max)
					{
						$from_copy_start = strpos(mb_substr($from_text, $from_segment_start, $from_segment_len, $encoding), mb_substr($to_text, $to_copy_start, $copy_len, $encoding));
						if ($from_copy_start !== false)
						{
							$from_copy_start += $from_segment_start;
							break 2;
						}
						$to_copy_start++;
					}
					$copy_len--;
				}
			}
			else
			{
				$copy_len = $from_segment_len;
				while ($copy_len)
				{
					$from_copy_start = $from_segment_start;
					$from_copy_start_max = $from_segment_end - $copy_len;
					while ($from_copy_start <= $from_copy_start_max)
					{
						$to_copy_start = strpos(mb_substr($to_text, $to_segment_start, $to_segment_len, $encoding), mb_substr($from_text, $from_copy_start, $copy_len, $encoding));
						if ($to_copy_start !== false)
						{
							$to_copy_start += $to_segment_start;
							break 2;
						}
						$from_copy_start++;
					}
					$copy_len--;
				}
			}
			// match found
			if ($copy_len >= $greed)
			{
				$jobs[] = array($from_segment_start, $from_copy_start, $to_segment_start, $to_copy_start);
				$result[$from_copy_start * 4 + 2] = new FineDiffCopyOp($copy_len);
				$jobs[] = array($from_copy_start + $copy_len, $from_segment_end, $to_copy_start + $copy_len, $to_segment_end);
			}
			// no match,  so delete all, insert all
			else
			{
				$result[$from_segment_start * 4] = new FineDiffReplaceOp($from_segment_len, mb_substr($to_text, $to_segment_start, $to_segment_len, $encoding), $encoding);
			}
		}
		ksort($result, SORT_NUMERIC);
		return array_values($result);
	}

	/**
	 * Efficiently fragment the text into an array according to
	 * specified delimiters.
	 * No delimiters means fragment into single character.
	 * The array indices are the offset of the fragments into
	 * the input string.
	 * A sentinel empty fragment is always added at the end.
	 * Careful: No check is performed as to the validity of the
	 * delimiters.
	 */
	protected static function extractFragments($text, $delimiters, $encoding = null)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}

		// special case: split into characters
		if (empty($delimiters))
		{
			$chars = static::mb_str_split($text, 1);
			$chars[] = '';
			return $chars;
		}
		$fragments = array();
		$offset = 0;
		$split = preg_split('/([' . preg_quote($delimiters, '/') . ']+)/u', $text, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach ($split as $m)
		{
			$fragments[$offset] = $m;
			$offset += mb_strlen($m, $encoding);
		}
		$fragments[$offset] = '';
		return $fragments;
	}

	/**
	 * Stock opcode renderers
	 */
	protected static function renderToTextFromOpcode($opcode, $from, $from_offset, $from_len, $encoding = null)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}

		if ($opcode === 'c' || $opcode === 'i')
		{
			echo mb_substr($from, $from_offset, $from_len, $encoding);
		}
	}

	protected static function mb_str_split($str, $split_length = 1)
	{
		return preg_split('/(?=(.{' . (int)$split_length . '})*$)/us', $str);
	}

	protected static function mb_strspn($str, $delimiters, $start = 0, $length = null, $encoding = null)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}
		if ($start || $length)
		{
			$str = mb_substr($str, $start, $length ? $length : mb_strlen($str, $encoding) - $start, $encoding);
		}
		if (preg_match('/^[' . preg_quote($delimiters, '/') . ']+/', $str, $m))
		{
			return mb_strlen($m[0], $encoding);
		}
		else
		{
			return 0;
		}
	}
}


class FineDiffReplaceOp extends FineDiffOp {
	public $encoding;

	public function __construct($fromLen, $text, $encoding = null) {
		$this->fromLen = $fromLen;
		$this->text = $text;
		$this->encoding = $encoding;
		if ( $encoding === null )
		{
			$this->encoding = mb_internal_encoding();
		}
	}
	public function getFromLen() {
		return $this->fromLen;
	}
	public function getToLen() {
		return mb_strlen($this->text, $this->encoding);
	}
	public function getText() {
		return $this->text;
	}
}

class FineDiffCopyOp extends FineDiffOp
{
	public function __construct($len)
	{
		$this->len = $len;
	}

	public function getFromLen()
	{
		return $this->len;
	}

	public function getToLen()
	{
		return $this->len;
	}

	public function increase($size)
	{
		return $this->len += $size;
	}
}

class FineDiffDeleteOp extends FineDiffOp
{
	public function __construct($len)
	{
		$this->fromLen = $len;
	}

	public function getFromLen()
	{
		return $this->fromLen;
	}

	public function getToLen()
	{
		return 0;
	}
}


class FineDiffHTML extends FineDiff
{

	public function renderDiffToHTML($textToEntities = true)
	{
		$in_offset = 0;
		ob_start();
		foreach ($this->edits as $edit)
		{
			$n = $edit->getFromLen();
			if ($edit instanceof FineDiffCopyOp)
			{
				FineDiff::renderDiffToHTMLFromOpcode('c', $this->from_text, $in_offset, $n, null, $textToEntities);
			}
			else if ($edit instanceof FineDiffDeleteOp)
			{
				FineDiff::renderDiffToHTMLFromOpcode('d', $this->from_text, $in_offset, $n, null, $textToEntities);
			}
			else if ($edit instanceof FineDiffInsertOp)
			{
				FineDiff::renderDiffToHTMLFromOpcode('i', $edit->getText(), 0, $edit->getToLen(), null, $textToEntities);
			}
			else /* if ( $edit instanceof FineDiffReplaceOp ) */
			{
				FineDiff::renderDiffToHTMLFromOpcode('d', $this->from_text, $in_offset, $n, null, $textToEntities);
				FineDiff::renderDiffToHTMLFromOpcode('i', $edit->getText(), 0, $edit->getToLen(), null, $textToEntities);
			}
			$in_offset += $n;
		}
		return ob_get_clean();
	}

	/**------------------------------------------------------------------------
	 * Render the diff to an HTML string
	 */
	public static function renderDiffToHTMLFromOpcodes($from, $opcodes, $encoding = null, $textToEntities = true)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}
		ob_start();
		FineDiff::renderFromOpcodes($from, $opcodes, 'FineDiffHTML::renderDiffToHTMLFromOpcode', $encoding, $textToEntities);
		return ob_get_clean();
	}

	public static function renderDiffToHTMLFromOpcode($opcode, $from, $from_offset, $from_len, $encoding = null, $textToEntities = true)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}

		if ($opcode === 'c')
		{
			if ($textToEntities)
			{
				echo htmlentities(mb_substr($from, $from_offset, $from_len, $encoding));
			}
			else
			{
				echo mb_substr($from, $from_offset, $from_len, $encoding);
			}
		}
		else if ($opcode === 'd')
		{
			$deletion = mb_substr($from, $from_offset, $from_len, $encoding);
			if (strcspn($deletion, " \n\r") === 0)
			{ // no mb_ here is okay
				$deletion = str_replace(array("\n", "\r"), array('\n', '\r'), $deletion);
			}
			if ($textToEntities)
			{
				echo '<del>', htmlspecialchars($deletion), '</del>';
			}
			else
			{
				echo '<del>', $deletion, '</del>';
			}
		}
		else /* if ( $opcode === 'i' ) */
		{
			if ($textToEntities)
			{
				echo '<ins>', htmlspecialchars(mb_substr($from, $from_offset, $from_len, $encoding), ENT_QUOTES), '</ins>';
			}
			else
			{
				echo '<ins>', mb_substr($from, $from_offset, $from_len, $encoding), '</ins>';
			}
		}
	}
}

class FineDiffInsertOp extends FineDiffOp
{
	public $encoding;

	public function __construct($text, $encoding = null)
	{
		$this->text = $text;
		$this->encoding = $encoding;
		if ($encoding === null)
		{
			$this->encoding = mb_internal_encoding();
		}
	}

	public function getFromLen()
	{
		return 0;
	}

	public function getToLen()
	{
		return mb_strlen($this->text, $this->encoding);
	}

	public function getText()
	{
		return $this->text;
	}
}

abstract class FineDiffOp
{
	abstract public function getFromLen();

	abstract public function getToLen();
}

/**
 * FineDiff ops
 *
 * Collection of ops
 */
class FineDiffOps
{
	public $edits = array();

	public function appendOpcode($opcode, $from, $from_offset, $from_len, $encoding = null)
	{
		if ($encoding === null)
		{
			$encoding = mb_internal_encoding();
		}
		if ($opcode === 'c')
		{
			$edits[] = new FineDiffCopyOp($from_len);
		}
		else if ($opcode === 'd')
		{
			$edits[] = new FineDiffDeleteOp($from_len);
		}
		else /* if ( $opcode === 'i' ) */
		{
			$edits[] = new FineDiffInsertOp(mb_substr($from, $from_offset, $from_len, $encoding), $encoding);
		}
	}
}