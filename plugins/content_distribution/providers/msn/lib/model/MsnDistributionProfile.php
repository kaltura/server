<?php
/**
 * @package plugins.msnDistribution
 * @subpackage model
 */
class MsnDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN = 'domain';
	const CUSTOM_DATA_CS_ID = 'csId';
	const CUSTOM_DATA_SOURCE = 'source';
	const CUSTOM_DATA_SOURCE_FRIENDLY_NAME = 'sourceFriendlyName';
	const CUSTOM_DATA_SOURCE_FLAVOR_PARAMS_ID = 'sourceFlavorParamsId';
	const CUSTOM_DATA_PAGE_GROUP = 'pageGroup';
	const CUSTOM_DATA_WMV_FLAVOR_PARAMS_ID = 'wmvFlavorParamsId';
	const CUSTOM_DATA_FLV_FLAVOR_PARAMS_ID = 'flvFlavorParamsId';
	const CUSTOM_DATA_SL_FLAVOR_PARAMS_ID = 'slFlavorParamsId';
	const CUSTOM_DATA_SL_HD_FLAVOR_PARAMS_ID = 'slHdFlavorParamsId';
	const CUSTOM_DATA_MSNVIDEO_CAT= 'msnvideoCat';
	const CUSTOM_DATA_MSNVIDEO_TOP = 'msnvideoTop';
	const CUSTOM_DATA_MSNVIDEO_TOP_CAT = 'msnvideoTopCat';
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return MsnDistributionPlugin::getProvider();
	}
	
		
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{	    	    
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);
		return $validationErrors;
	}
	
	
	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getDomain()					{return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN);}
	public function getCsId()					{return $this->getFromCustomData(self::CUSTOM_DATA_CS_ID);}
	public function getSource()					{return $this->getFromCustomData(self::CUSTOM_DATA_SOURCE);}
	public function getSourceFriendlyName()		{return $this->getFromCustomData(self::CUSTOM_DATA_SOURCE_FRIENDLY_NAME);}
	public function getPageGroup()				{return $this->getFromCustomData(self::CUSTOM_DATA_PAGE_GROUP);}
	public function getSourceFlavorParamsId()	{return $this->getFromCustomData(self::CUSTOM_DATA_SOURCE_FLAVOR_PARAMS_ID);}
	public function getFlvFlavorParamsId()		{return $this->getFromCustomData(self::CUSTOM_DATA_FLV_FLAVOR_PARAMS_ID);}
	public function getWmvFlavorParamsId()		{return $this->getFromCustomData(self::CUSTOM_DATA_WMV_FLAVOR_PARAMS_ID);}
	public function getSlFlavorParamsId()		{return $this->getFromCustomData(self::CUSTOM_DATA_SL_FLAVOR_PARAMS_ID);}
	public function getSlHdFlavorParamsId()		{return $this->getFromCustomData(self::CUSTOM_DATA_SL_HD_FLAVOR_PARAMS_ID);}
	public function getMsnvideoCat()			{return $this->getFromCustomData(self::CUSTOM_DATA_MSNVIDEO_CAT);}
	public function getMsnvideoTop()			{return $this->getFromCustomData(self::CUSTOM_DATA_MSNVIDEO_TOP);}
	public function getMsnvideoTopCat()			{return $this->getFromCustomData(self::CUSTOM_DATA_MSNVIDEO_TOP_CAT);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setDomain($v)				{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN, $v);}
	public function setCsId($v)					{$this->putInCustomData(self::CUSTOM_DATA_CS_ID, $v);}
	public function setSource($v)				{$this->putInCustomData(self::CUSTOM_DATA_SOURCE, $v);}
	public function setSourceFriendlyName($v)	{$this->putInCustomData(self::CUSTOM_DATA_SOURCE_FRIENDLY_NAME, $v);}
	public function setPageGroup($v)			{$this->putInCustomData(self::CUSTOM_DATA_PAGE_GROUP, $v);}
	public function setSourceFlavorParamsId($v)	{$this->putInCustomData(self::CUSTOM_DATA_SOURCE_FLAVOR_PARAMS_ID, $v);}
	public function setFlvFlavorParamsId($v)	{$this->putInCustomData(self::CUSTOM_DATA_FLV_FLAVOR_PARAMS_ID, $v);}
	public function setWmvFlavorParamsId($v)	{$this->putInCustomData(self::CUSTOM_DATA_WMV_FLAVOR_PARAMS_ID, $v);}
	public function setSlFlavorParamsId($v)		{$this->putInCustomData(self::CUSTOM_DATA_SL_FLAVOR_PARAMS_ID, $v);}
	public function setSlHdFlavorParamsId($v)	{$this->putInCustomData(self::CUSTOM_DATA_SL_HD_FLAVOR_PARAMS_ID, $v);}
	public function setMsnvideoCat($v)			{$this->putInCustomData(self::CUSTOM_DATA_MSNVIDEO_CAT, $v);}
	public function setMsnvideoTop($v)			{$this->putInCustomData(self::CUSTOM_DATA_MSNVIDEO_TOP, $v);}
	public function setMsnvideoTopCat($v)		{$this->putInCustomData(self::CUSTOM_DATA_MSNVIDEO_TOP_CAT, $v);}
    
	
	protected function getDefaultFieldConfigArray()
	{	    
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
	      
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::PROVIDER_ID);
	    $fieldConfig->setUserFriendlyFieldName('Entry id');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	      
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::CSID);
	    $fieldConfig->setUserFriendlyFieldName('CSID');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/csid" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	      
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::SOURCE);
	    $fieldConfig->setUserFriendlyFieldName('Source');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/source" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	      
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::SOURCE_FRIENDLY_NAME);
	    $fieldConfig->setUserFriendlyFieldName('Source friendly name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/source_friendly_name" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	      
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::PAGE_GROUP);
	    $fieldConfig->setUserFriendlyFieldName('Page group');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/page_group" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::DESCRIPTION);
	    $fieldConfig->setUserFriendlyFieldName('Entry description');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	        
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::TAGS_PUBLIC);
	    $fieldConfig->setUserFriendlyFieldName('Entry tags');
	    $fieldConfig->setEntryMrssXslt(
	    			'<xsl:for-each select="tags/tag">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::TAGS));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
        $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::START_DATE);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunrise');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::ACTIVATE_END_DATE);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunset');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::SEARCHABLE_END_DATE);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunset');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::ARCHIVE_END_DATE);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunset');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::TAGS_MSNVIDEO_CAT);
	    $fieldConfig->setUserFriendlyFieldName('MSNVideo_Cat');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/msnvideo_cat" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::TAGS_MSNVIDEO_TOP);
	    $fieldConfig->setUserFriendlyFieldName('MSNVideo_Top');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/msnvideo_top" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(MsnDistributionField::TAGS_MSNVIDEO_TOP_CAT);
	    $fieldConfig->setUserFriendlyFieldName('MSNVideo_Top_Cat');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/msnvideo_top_cat" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    // placeholders for dynamic fields
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MsnDistributionField::RELATED_LINK_N_TITLE);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MsnDistributionField::RELATED_LINK_N_URL);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MsnDistributionField::TAGS_PREMIUM_N_MARKET);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MsnDistributionField::TAGS_PREMIUM_N_NAMESPACE);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MsnDistributionField::TAGS_PREMIUM_N_VALUE);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
			    
	    return $fieldConfigArray;
	}
	
	
}