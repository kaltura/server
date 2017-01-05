<?php
abstract class kHashPatternUrlTokenizer extends kUrlTokenizer
{
	/**
	 * Regex pattern to find the part of the URL that should be hashed
	 *
	 * @var string
	 */
	protected $hashPatternRegex;
	
	/**
	 * @return the $hashPatternRegex
	 */
	public function getHashPatternRegex() {
		return $this->hashPatternRegex;
	}

	/**
	 * @param string $hashPatternRegex
	 */
	public function setHashPatternRegex($hashPatternRegex) {
		$this->hashPatternRegex = $hashPatternRegex;
	}

}
