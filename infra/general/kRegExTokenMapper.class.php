<?php

/**
 * @package infra
 * @subpackage utils
 */
class kRegExTokenMapper
{
	const TOKEN_REG_EX = "/@K\w+K@/";
	private $tokensMap;

	public function __construct()
	{
		$this->tokensMap = array();
	}

	public function tokenize($string, $tokenPatterns)
	{
		return preg_replace_callback($tokenPatterns, array( $this, "tokenToKey"), $string);
	}

	public function unTokenize($tokenizedString)
	{
		return preg_replace_callback( self::TOKEN_REG_EX, array( $this, "keyToToken"), $tokenizedString);
	}

	public function tokenToKey( $matches )
	{
		$token = $matches[0];
		$key = "@K" . kString::generateStringId() . "K@";

		$this->tokensMap[$key] = $token;
		return $key;
	}

	public function keyToToken( $matches )
	{
		$key = $matches[0];
		if ( array_key_exists( $key, $this->tokensMap ) )
		{
			return $this->tokensMap[$key];
		}

		return $key; // Return the original value if not found in the map
	}
}