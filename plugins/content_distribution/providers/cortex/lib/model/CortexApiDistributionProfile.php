<?php
/**
 * @package plugins.cortexApiDistribution
 * @subpackage model
 */
class CortexApiDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_HOST = 'host';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_FOLDER_RECORD_ID = 'folderrecordid';
	const CUSTOM_DATA_META_DATA_PROFILE_ID = 'metadataprofileid';
	const CUSTOM_DATA_META_DATA_PROFILE_ID_PUSHING = 'metadataprofileidpushing';

	const MEDIA_TITLE_MAXIMUM_LENGTH = 100;
	const MEDIA_DESCRIPTION_MAXIMUM_LENGTH = 5000;
	const MEDIA_KEYWORDS_MAXIMUM_LENGTH = 500;

	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */

	public function getProvider()
	{
		return CortexApiDistributionPlugin::getProvider();
	}
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);

		$maxLengthFields = array (
		    CortexApiDistributionField::MEDIA_TITLE => self::MEDIA_TITLE_MAXIMUM_LENGTH,
		    CortexApiDistributionField::MEDIA_DESCRIPTION => self::MEDIA_DESCRIPTION_MAXIMUM_LENGTH,
		    CortexApiDistributionField::MEDIA_KEYWORDS => self::MEDIA_KEYWORDS_MAXIMUM_LENGTH,
		    CortexApiDistributionField::MEDIA_USER_ID => self::MEDIA_TITLE_MAXIMUM_LENGTH // TODO: check this
		);

		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues))
		{
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));


		return $validationErrors;
	}

	public function getHost()				{return $this->getFromCustomData(self::CUSTOM_DATA_HOST);}
	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getFolderRecordID()				{return $this->getFromCustomData(self::CUSTOM_DATA_FOLDER_RECORD_ID);}
	public function getMetadataProfileId()				{return $this->getFromCustomData(self::CUSTOM_DATA_META_DATA_PROFILE_ID);}
	public function getMetadataProfileIdPushing()				{return $this->getFromCustomData(self::CUSTOM_DATA_META_DATA_PROFILE_ID_PUSHING);}

	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setHost($v)				{$this->putInCustomData(self::CUSTOM_DATA_HOST, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setFolderRecordID($v)				{$this->putInCustomData(self::CUSTOM_DATA_FOLDER_RECORD_ID, $v);}
	public function setMetadataProfileId($v)				{$this->putInCustomData(self::CUSTOM_DATA_META_DATA_PROFILE_ID, $v);}
	public function setMetadataProfileIdPushing($v)				{$this->putInCustomData(self::CUSTOM_DATA_META_DATA_PROFILE_ID_PUSHING, $v);}
	
	protected function getDefaultFieldConfigArray()
	{	    
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		//entry title
		$fieldConfig = new DistributionFieldConfig();
		 $fieldConfig->setFieldName(CortexApiDistributionField::MEDIA_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setUpdateParams(array(entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
		//entry description
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(CortexApiDistributionField::MEDIA_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Video description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;


		// entry tags
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(CortexApiDistributionField::MEDIA_KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Tags');
		$fieldConfig->setEntryMrssXslt('<xsl:for-each select="tags/tag">
                                			<xsl:if test="position() &gt; 1">
                                				<xsl:text>,</xsl:text>
                                			</xsl:if>
                                			<xsl:value-of select="normalize-space(.)" />
                                		</xsl:for-each>');
		$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setUpdateParams(array(entryPeer::TAGS));
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		// entry user id
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(CortexApiDistributionField::MEDIA_USER_ID);
		$fieldConfig->setUserFriendlyFieldName('User ID');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(userId)" />');
		$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setUpdateParams(array(entryPeer::PUSER_ID));
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		//entry id
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(CortexApiDistributionField::MEDIA_ID);
		$fieldConfig->setUserFriendlyFieldName('item entry id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		// entry created at
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(CortexApiDistributionField::MEDIA_CREATION_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry created at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(createdAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		return $fieldConfigArray;
	}

	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		$ret = parent::getOptionalAssetDistributionRules();
		if(!class_exists('CaptionPlugin') || !CaptionPlugin::isAllowedPartner($this->getPartnerId()))
		{
			return $ret;
		}
		$isCaptionCondition = new kAssetDistributionPropertyCondition();
		$isCaptionCondition->setPropertyName(assetPeer::translateFieldName(assetPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME));
		$isCaptionCondition->setPropertyValue(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));

		$captionDistributionRule = new kAssetDistributionRule();
		$captionDistributionRule->setAssetDistributionConditions(array($isCaptionCondition));
		$ret[] = $captionDistributionRule;
		$this->setOptionalAssetDistributionRules($ret);
		$this->save();
	}

	public function shouldExcludeAudioFlavors()
	{
		return true;
	}
}
