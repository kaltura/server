<?php

class kStreamContainer
{
	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var int
	 */
	protected $trackIndex;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var int
	 */
	protected $channelIndex;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string
	 */
	protected $channelLayout;

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getTrackIndex()
	{
		return $this->trackIndex;
	}

	/**
	 * @param int $trackIndex
	 */
	public function setTrackIndex($trackIndex)
	{
		$this->trackIndex = $trackIndex;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/**
	 * @return string
	 */
	public function getChannelLayout()
	{
		return $this->channelLayout;
	}

	/**
	 * @param string $channelLayout
	 */
	public function setChannelLayout($channelLayout)
	{
		$this->channelLayout = $channelLayout;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}

	/**
	 * @return int
	 */
	public function getChannelIndex()
	{
		return $this->channelIndex;
	}

	/**
	 * @param int $channelIndex
	 */
	public function setChannelIndex($channelIndex)
	{
		$this->channelIndex = $channelIndex;
	}
}