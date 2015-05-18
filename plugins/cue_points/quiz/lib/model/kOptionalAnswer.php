<?php
/**
 * Quiz optional answer
 *
 * @package plugins.quiz
 * @subpackage model
 *
 */

class kOptionalAnswer {

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * @var float
	 */
	protected $weight = 1.0;

	/**
	 * @var boolean
	 */
	protected $isCorrect;

	/**
	 * @return boolean
	 */
	public function getIsCorrect()
	{
		return $this->isCorrect;
	}

	/**
	 * @param boolean $isCorrect
	 */
	public function setIsCorrect($isCorrect)
	{
		$this->isCorrect = $isCorrect;
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @param string $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @return float
	 */
	public function getWeight()
	{
		return $this->weight;
	}

	/**
	 * @param float $weight
	 */
	public function setWeight($weight)
	{
		$this->weight = $weight;
	}

}