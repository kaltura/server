<?php
class kWidevineRepositorySyncJobData extends kJobData
{    
     /**
     * @var string
     */    
    private $wvAssetIds;
    
   	/**
	 * @var int
	 */
	private $syncMode;
	
	/**
     * @var string
     */    
    private $modifiedAttributes;
    
    /**
     * Identifies if this job requires closer
     * @var int
     */
    private $monitorSyncCompletion;
	
	/**
	 * @return the $monitorSyncCompletion
	 */
	public function getMonitorSyncCompletion() {
		return $this->monitorSyncCompletion;
	}

	/**
	 * @param int $monitorSyncCompletion
	 */
	public function setMonitorSyncCompletion($monitorSyncCompletion) {
		$this->monitorSyncCompletion = $monitorSyncCompletion;
	}

	/**
	 * @return the $wvAssetIds
	 */
	public function getWvAssetIds() {
		return $this->wvAssetIds;
	}

	/**
	 * @return the $syncMode
	 */
	public function getSyncMode() {
		return $this->syncMode;
	}

	/**
	 * @param string $wvAssetIds
	 */
	public function setWvAssetIds($wvAssetIds) {
		$this->wvAssetIds = $wvAssetIds;
	}

	/**
	 * @param int $syncMode
	 */
	public function setSyncMode($syncMode) {
		$this->syncMode = $syncMode;
	}

		/**
	 * @return the $modifiedAttributes
	 */
	public function getModifiedAttributes() {
		return $this->modifiedAttributes;
	}

	/**
	 * @param array $modifiedAttributes
	 */
	public function setModifiedAttributes($modifiedAttributes) {
		$this->modifiedAttributes = $modifiedAttributes;
	}

    public function addModifiedAttribute($attrName, $attrValue)
    {
    	if($this->modifiedAttributes)
    		$arr = explode(',', $this->modifiedAttributes);
    	else 
    		$arr = array();
    	$arr[] = $attrName.':'.$attrValue;
    	$this->modifiedAttributes = implode(',', $arr);
    }   

}