<?php
/** 
 * @package infra
 * @subpackage utils
 */
class KStringTokenizer 
{
	/**
	 * @var string
	 */
	private $token;
	
	/**
	 * @var string
	 */
	private $delim;
	
	/**
	 * Constructs a string tokenizer for the specified string
	 * 
	 * @param string $str String to tokenize
	 * @param string $delim The set of delimiters (the characters that separate tokens)
	 * specified at creation time, default to ' '
	 */
	public function __construct(/*string*/ $str, /*string*/ $delim = ' ') 
	{
		$this->token = strtok($str, $delim);
		$this->delim = $delim;
	}
	
	/**
	 * Tests if there are more tokens available from this tokenizer's string. It
	 * does not move the internal pointer in any way. To move the internal pointer
	 * to the next element call nextToken()
	 * 
	 * @return boolean - true if has more tokens, false otherwise
	 */
	public function hasMoreTokens() 
	{
		return ($this->token !== false);
	}
	
	/**
	 * Returns the next token from this string tokenizer and advances the internal
	 * pointer by one.
	 * 
	 * @return string - next element in the tokenized string
	 */
	public function nextToken() 
	{
		$current = $this->token;
		$this->token = strtok($this->delim);
		
		return $current;
	}
}