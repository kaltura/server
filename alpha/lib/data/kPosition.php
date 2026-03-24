<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kPosition
{
	/**
	 * @var float
	 */
	private $x;

	/**
	 * @var float
	 */
	private $y;

	/**
	 * @return float
	 */
	public function getX()
	{
		return $this->x;
	}

	/**
	 * @param float $x
	 */
	public function setX($x)
	{
		$this->x = $x;
	}

	/**
	 * @return float
	 */
	public function getY()
	{
		return $this->y;
	}

	/**
	 * @param float $y
	 */
	public function setY($y)
	{
		$this->y = $y;
	}
}
