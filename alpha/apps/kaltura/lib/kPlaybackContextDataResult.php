<?php

class kPlaybackContextDataResult
{
    /**
     *
     * @var array
     */
    private $pluginData = array();

    /**
     *
     * @var array
     */
    private $flavorIdsToRemove = array();

    /**
     *
     * @var array
     */
    private $playbackCaptions = array();

	/**
	 *
	 * @var array
	 */
	private $bumperData = array();

    /**
     * @return array
     */
    public function getPlaybackCaptions()
    {
        return $this->playbackCaptions;
    }

    /**
     * @param array $playbackCaptions
     */
    public function setPlaybackCaptions($playbackCaptions)
    {
        $this->playbackCaptions = $playbackCaptions;
    }

    /**
     * @return array
     */
    public function getFlavorIdsToRemove()
    {
        return $this->flavorIdsToRemove;
    }

    public function addToFlavorIdsToRemove ( $value )
    {
        if ( ! in_array ( $value , $this->flavorIdsToRemove ) )
            $this->flavorIdsToRemove[] = $value;
    }

    public function addToPluginData ( $pluginName, $value )
    {
            $this->pluginData[$pluginName] = $value;
    }

    /**
     * @param array $flavorIdsToRemove
     */
    public function setFlavorIdsToRemove($flavorIdsToRemove)
    {
        $this->flavorIdsToRemove = $flavorIdsToRemove;
    }

    /**
     * @return array
     */
    public function getPluginData()
    {
        return $this->pluginData;
    }

    /**
     * @param array $pluginData
     */
    public function setPluginData($pluginData)
    {
        $this->pluginData = $pluginData;
    }

	/**
	 * @return array
	 */
	public function getBumperData()
	{
		return $this->bumperData;
	}

	/**
	 * @param array $bumperData
	 */
	public function setBumperData($bumperData)
	{
		$this->bumperData = $bumperData;
	}

}