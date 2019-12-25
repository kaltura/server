<?php


class kExportToCsvOptions
{
	/**
	 * @var int
	 */
	public $option;

	/**
	 * @return int
	 */
	public function getOption()
	{
		return $this->option;
	}

	/**
	 * @param int $option
	 */
	public function setOption($option)
	{
		$this->option = $option;
	}
}
