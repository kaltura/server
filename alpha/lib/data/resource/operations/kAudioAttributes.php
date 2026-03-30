<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAudioAttributes
{
	/**
	 * @var float
	 */
	private $volume;

	/**
	 * @return float
	 */
	public function getVolume()
	{
		return $this->volume;
	}

	/**
	 * @param float $volume
	 */
	public function setVolume($volume)
	{
		$this->volume = $volume;
	}

	public function toArray()
	{
		return array(
			'volume' => $this->volume,
		);
	}

	public function getApiType()
	{
		return 'KalturaAudioAttributes';
	}
}
