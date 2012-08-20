<?php
/**
 * @package plugins.crossKalturaDistribution
 * @subpackage model.data
 */
class kCrossKalturaDistributionJobProviderData extends kDistributionJobProviderData
{

    /**
     * array of information about distributed flavor assets
     * @var string
     */
    protected $distributedFlavorAssets;
    
    /**
     * array of information about distributed thumb assets
     * @var string
     */
    protected $distributedThumbAssets;
    
    /**
     * array of information about distributed metadata
     * @var string
     */
    protected $distributedMetadata;
    
    /**
     * array of information about distributed caption assets
     * @var string
     */
    protected $distributedCaptionAssets;
    
    /**
     * array of information about distributed cue points
     * @var string
     */
    protected $distributedCuePoints;
    
    
	/**
     * @return the $distributedFlavorAssets
     */
    public function getDistributedFlavorAssets ()
    {
        return $this->distributedFlavorAssets;
    }

	/**
     * @param string $distributedFlavorAssets
     */
    public function setDistributedFlavorAssets ($distributedFlavorAssets)
    {
        $this->distributedFlavorAssets = $distributedFlavorAssets;
    }

	/**
     * @return the $distributedThumbAssets
     */
    public function getDistributedThumbAssets ()
    {
        return $this->distributedThumbAssets;
    }

	/**
     * @param string $distributedThumbAssets
     */
    public function setDistributedThumbAssets ($distributedThumbAssets)
    {
        $this->distributedThumbAssets = $distributedThumbAssets;
    }

	/**
     * @return the $distributedMetadata
     */
    public function getDistributedMetadata ()
    {
        return $this->distributedMetadata;
    }

	/**
     * @param string $distributedMetadata
     */
    public function setDistributedMetadata ($distributedMetadata)
    {
        $this->distributedMetadata = $distributedMetadata;
    }

	/**
     * @return the $distributedCaptionAssets
     */
    public function getDistributedCaptionAssets ()
    {
        return $this->distributedCaptionAssets;
    }

	/**
     * @param string $distributedCaptionAssets
     */
    public function setDistributedCaptionAssets ($distributedCaptionAssets)
    {
        $this->distributedCaptionAssets = $distributedCaptionAssets;
    }

	/**
     * @return the $distributedCuePoints
     */
    public function getDistributedCuePoints ()
    {
        return $this->distributedCuePoints;
    }

	/**
     * @param string $distributedCuePoints
     */
    public function setDistributedCuePoints ($distributedCuePoints)
    {
        $this->distributedCuePoints = $distributedCuePoints;
    }    
    
}