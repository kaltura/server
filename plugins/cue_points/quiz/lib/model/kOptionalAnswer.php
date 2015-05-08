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
	 * @var bool
	 */
	protected $correct = false;

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

	/**
	 * @param $correct
	 */
	public function setCorrect($correct)
	{
		$this->correct = $correct;
	}

	/**
	 * @return bool
	 */
	public function getCorrect()
	{
		return $this->correct;
	}

}