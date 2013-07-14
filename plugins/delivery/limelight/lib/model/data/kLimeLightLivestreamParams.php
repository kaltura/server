<?php

/**
 * @package plugins.limeLight
 * @subpackage model.data
 */ 
class kLimeLightLivestreamParams
{
	private $limelightPrimaryPublishUrl;
	private $limelightSecondaryPublishUrl;
	private $limelightStreamUrl;
	
	

	/**
     * @return the $limelightPrimaryPublishUrl
     */
    public function getLimelightPrimaryPublishUrl ()
    {
        return $this->limelightPrimaryPublishUrl;
    }

	/**
     * @param field_type $limelightPrimaryPublishUrl
     */
    public function setLimelightPrimaryPublishUrl ($limelightPrimaryPublishUrl)
    {
        $this->limelightPrimaryPublishUrl = $limelightPrimaryPublishUrl;
    }

	/**
     * @return the $limelightSecondaryPublishUrl
     */
    public function getLimelightSecondaryPublishUrl ()
    {
        return $this->limelightSecondaryPublishUrl;
    }

	/**
     * @param field_type $limelightSecondaryPublishUrl
     */
    public function setLimelightSecondaryPublishUrl (
    $limelightSecondaryPublishUrl)
    {
        $this->limelightSecondaryPublishUrl = $limelightSecondaryPublishUrl;
    }

	/**
     * @return the $limelightStreamUrl
     */
    public function getLimelightStreamUrl ()
    {
        return $this->limelightStreamUrl;
    }

	/**
     * @param field_type $limelightStreamUrl
     */
    public function setLimelightStreamUrl ($limelightStreamUrl)
    {
        $this->limelightStreamUrl = $limelightStreamUrl;
    }

	

}