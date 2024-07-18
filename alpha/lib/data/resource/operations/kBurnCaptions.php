<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBurnCaptions
{
	/**
	 * @var int
	 */
	private $fontSize;


	/**
	 * @var int
	 */
	private $alignment;

	/**
	 * @return int
	 */
	public function getFontSize()
	{
		return $this->fontSize;
	}

	/**
	 * @param $fontSize int
	 */
	public function setFontSize($fontSize)
	{
		$this->fontSize = $fontSize;
	}

	/**
	 * @return int
	 */
	public function getAlignment()
	{
		return $this->alignment;
	}

	/**
	 * @param int $alignment
	 */
	public function setAlignment($alignment)
	{
		$this->alignment = $alignment;
	}
}
