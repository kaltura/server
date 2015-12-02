<?php


/**
 * @package plugins.facebookDistribution
 * @subpackage model
 */
class FacebookDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_PAGE_ID = 'pageId';
	const CUSTOM_DATA_PAGE_ACCESS_TOKEN = 'pageAccessToken';
	const CUSTOM_DATA_USER_ACCESS_TOKEN = 'userAccessToken';
	const CUSTOM_DATA_PERMISSIONS = 'permissions';
	const CUSTOM_DATA_RE_REQUEST_PERMISSIONS = 'reRequestPermissions';
		
	const CALL_TO_ACTION_TYPE_VALID_VALUES = 'SHOP_NOW,BOOK_TRAVEL,LEARN_MORE,SIGN_UP,DOWNLOAD,WATCH_MORE';
	const DEFAULT_RE_REQUEST_PERMISSIONS = 'false';
	private static $DEFAULT_PERMISSIONS = array('manage_pages', 'publish_actions', 'user_videos');


	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return FacebookDistributionPlugin::getProvider();
	}
			
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		$inListOrNullFields = array (
		    FacebookDistributionField::CALL_TO_ACTION_TYPE_VALID_VALUES => explode(',', self::CALL_TO_ACTION_TYPE_VALID_VALUES),
		);
		
		$flavorAssets = array();
		if(count($entryDistribution->getFlavorAssetIds()))
		{
			$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		}
		else 
		{
			$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($entryDistribution->getEntryId());
		}
		
		$validVideo = false;
		foreach ($flavorAssets as $flavorAsset) 
		{
			$validVideo = $this->validateVideo($flavorAsset);
			if($validVideo)
				break;
		}

		if(!$validVideo)
		{
			KalturaLog::err("No valid video found for entry [" . $entryDistribution->getEntryId() . "]");
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'flavorAsset', 'no valid flavor found');			
		}
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
	    $validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));
					
		return $validationErrors;
	}

	public function getPageId()				    {return $this->getFromCustomData(self::CUSTOM_DATA_PAGE_ID);}
	public function setPageId($v)			    {$this->putInCustomData(self::CUSTOM_DATA_PAGE_ID, $v);}
	public function getPageAccessToken()	    {return $this->getFromCustomData(self::CUSTOM_DATA_PAGE_ACCESS_TOKEN);}
	public function setPageAccessToken($v)	    {$this->putInCustomData(self::CUSTOM_DATA_PAGE_ACCESS_TOKEN, $v);}
	public function getUserAccessToken()	    {return $this->getFromCustomData(self::CUSTOM_DATA_USER_ACCESS_TOKEN);}
	public function setUserAccessToken($v)	    {$this->putInCustomData(self::CUSTOM_DATA_USER_ACCESS_TOKEN, $v);}
	public function getPermissions()	        {return $this->getFromCustomData(self::CUSTOM_DATA_PERMISSIONS, null, self::$DEFAULT_PERMISSIONS);}
	public function setPermissions($v)	        {$this->putInCustomData(self::CUSTOM_DATA_PERMISSIONS, $v);}
	public function getReRequestPermissions()	{return $this->getFromCustomData(self::CUSTOM_DATA_RE_REQUEST_PERMISSIONS, null , self::DEFAULT_RE_REQUEST_PERMISSIONS);}
	public function setReRequestPermissions($v)	{$this->putInCustomData(self::CUSTOM_DATA_RE_REQUEST_PERMISSIONS, $v);}
	
	protected function getDefaultFieldConfigArray()
	{	    
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::DESCRIPTION);
	    $fieldConfig->setUserFriendlyFieldName('Entry description');
	    /*$fieldConfig->setEntryMrssXslt('
        			<xsl:choose>
                    	<xsl:when test="customData/metadata/'.self::METADATA_FIELD_DESCRIPTION.' != \'\'">
                    		<xsl:value-of select="customData/metadata/'.self::METADATA_FIELD_DESCRIPTION.'" />
                    	</xsl:when>
                    	<xsl:otherwise>
                    		<xsl:value-of select="string(description)" />
                    	</xsl:otherwise>
                    </xsl:choose>');*/

	    $fieldConfig->setUpdateOnChange(true);
	    //$fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION,"/*[local-name()='metadata']/*[local-name()='".self::METADATA_FIELD_DESCRIPTION."']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::SCHEDULE_PUBLISHING_TIME);
	    $fieldConfig->setUserFriendlyFieldName('Schedule Publishing Time');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;	   

	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::CALL_TO_ACTION_TYPE);
	    $fieldConfig->setUserFriendlyFieldName('Call To Action Type');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/call_to_action_type" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	   	$fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::CALL_TO_ACTION_VALUE);
	    $fieldConfig->setUserFriendlyFieldName('Call To Action Value');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/call_to_action_value" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	   	$fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::PLACE);
	    $fieldConfig->setUserFriendlyFieldName('ID of location to tag in video');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/place" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::TAGS);
	    $fieldConfig->setUserFriendlyFieldName('IDs (comma separated) of persons to tag in video');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/tags" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	   	$fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::TARGETING);
	    $fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/targeting" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	   	$fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING);
	    $fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to promote the video in specific audience feeds');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/feed_targeting" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	      
	    return $fieldConfigArray;
	}

	public function getApiAuthorizeUrl()
	{
		$permissions = implode(',', $this->getPermissions());
		$url = kConf::get('apphome_url');
		$url .= "/index.php/extservices/facebookoauth2".
            "/".FacebookRequestParameters::FACEBOOK_PROVIDER_ID_REQUEST_PARAM."/".base64_encode($this->getId()).
            "/".FacebookRequestParameters::FACEBOOK_PAGE_ID_REQUEST_PARAM."/".base64_encode($this->getPageId()).
            "/".FacebookRequestParameters::FACEBOOK_PERMISSIONS_REQUEST_PARAM."/".base64_encode($permissions).
            "/".FacebookRequestParameters::FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM."/".base64_encode($this->getReRequestPermissions())
        ;

		return $url;
	}
	
	private function validateVideo(flavorAsset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		if(kFileSyncUtils::fileSync_exists($syncKey))
		{
			$videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
			$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
			if(!$mediaInfo)
				return false;
			try
			{
				FacebookGraphSdkUtils::isValidVideo($videoAssetFilePath, $mediaInfo->getFileSize(), $mediaInfo->getVideoDuration(), $mediaInfo->getVideoWidth(), $mediaInfo->getVideoHeight());
				return true;
			}
			catch(Exception $e)
			{
				KalturaLog::debug('Asset ['.$flavorAsset->getId().'] not valid for distribution: '.$e->getMessage());
			}			
		}				
		return false;
	}
}