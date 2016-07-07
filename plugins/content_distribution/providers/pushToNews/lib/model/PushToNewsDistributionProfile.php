<?php
/**
 * @package plugins.pushToNewsDistribution
 * @subpackage model
 */
class PushToNewsDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_PROTOCOL = 'protocol';
	const CUSTOM_DATA_HOST = 'host';
	const CUSTOM_DATA_PORT = 'port';
	const CUSTOM_DATA_BASE_PATH = 'basePath';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_CERTIFICATE_KEY = 'certificateKey';
	
	const CUSTOM_METADATA_ID_XSLT = "idXslt";
	const CUSTOM_DATA_PUBLISHDAT_XSLT = 'publishdatXslt';
	const CUSTOM_DATA_CREATIONAT_XSLT = "creationatXslt";
	const CUSTOM_METADATA_TITLELANGUAGEDAT_XSLT = "titlelanguagedatXslt";
	const CUSTOM_DATA_TITLE_XSLT = "titleXslt";
	const CUSTOM_METADATA_MIMETYPE_XSLT = "mimetypeXslt";
	const CUSTOM_METADATA_LANGUAGE_XSLT = "languageXslt";
	const CUSTOM_METADATA_BODY_XSLT = "bodyXslt";
	const CUSTOM_DATA_AUTHOR_NAME_XSLT = "authorNameXslt";
	const CUSTOM_DATA_AUTHOR_EMAIL_XSLT = "authorEmailXslt";	
	const CUSTOM_DATA_RIGHTSINFO_COPYRIGHTHOLDER_XSLT = "rightsinfoCopyrightholderXslt";
	const CUSTOM_DATA_RIGHTSINFO_NAME_XSLT = "rightsinfoNameXslt";
	const CUSTOM_DATA_RIGHTSINFO_COPYRIGHTNOTICE_XSLT = "rightsinfoCopyrightnoticeXslt";
	const CUSTOM_METADATA_PRODUCTCODE_XSLT = "productcodeXslt";
	const CUSTOM_METADATA_ATTRIBUTION_XSLT = "attributionXslt";
	const CUSTOM_METADATA_METADATA_ORGANIZATIONS_XSLT = "metadataOrganizationsXslt";
	const CUSTOM_METADATA_METADATA_SUBJECTS_XSLT = "metadataSubjectsXslt";

	
	protected $maxLengthValidation= array (
	);
	
	protected $inListOrNullValidation = array (
	);
	
	protected function getDefaultFieldConfigArray()
	{
		return parent::getDefaultFieldConfigArray();
	}

	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!is_array($allFieldValues))
			$allFieldValues = array();
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($this->maxLengthValidation, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($this->inListOrNullValidation, $allFieldValues, $action));

		return $validationErrors;
	}
	
	public function getMetadataFilename(EntryDistribution $entryDistribution)
	{
		if ($this->getTitleXslt())
			return trim($this->transformXslForEntry($entryDistribution, $this->getTitleXslt()));
		else
			return $entryDistribution->getEntryId() . '_metadata.xml';
	}
	
	public function getMetadata(EntryDistribution $entryDistribution, $format = PushToNewsDistributionPlugin::DATA_FORMAT_JSON)
	{

		$entryDistribution->incrementSubmitDataVersion();
		$currentVersion = $entryDistribution->getSubmitDataVersion();
		$entryDistribution->save();
		
		switch($format)
		{
			case PushToNewsDistributionPlugin::DATA_FORMAT_JSON:
					$xslt = "<xsl:stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\"><xsl:template match=\"item\"><item>";

					$xslt .= "<id>" . $this->getIdXslt() . "</id>";
					$xslt .= "<action>update</action>";
					$xslt .= "<currentversion>" . $currentVersion . "</currentversion>";
					$xslt .= "<publishedat>" . $this->getPublishdatXslt() . "</publishedat>";
					$xslt .= "<creationat>" . $this->getCreationatXslt() . "</creationat>";
					$xslt .= "<titlelanguage>" . $this->getTitlelanguagedatXslt() . "</titlelanguage>";
					$xslt .= "<title>" . $this->getTitleXslt() . "</title>";
					$xslt .= "<mimetype>" . $this->getMimetypeXslt() . "</mimetype>";					
					$xslt .= "<language>" . $this->getLanguageXslt() . "</language>";
					$xslt .= "<body>" . $this->getLanguageXslt() . "</body>";
					$xslt .= "<author><name>" . $this->getAuthorNameXslt() . "</name>";
					$xslt .= "<email>" . $this->getAuthorEmailXslt() . "</email></author>";

					if($this->getRightsinfoCopyrightholderXslt() && $this->getRightsinfoNameXslt() && $this->getRightsinfoCopyrightnoticeXslt())
					{
						$xslt .= "<rightsinfo><copyrightholder>" . $this->getRightsinfoCopyrightholderXslt() . "</copyrightholder>";
						$xslt .= "<name>" . $this->getRightsinfoNameXslt() . "</name>";
						$xslt .= "<copyrightnotice>" . $this->getRightsinfoCopyrightnoticeXslt() . "</copyrightnotice></rightsinfo>";
					}
					
					$xslt .= "</item></xsl:template></xsl:stylesheet>";

					$tranformedEntryXml = $this->transformXslForEntry($entryDistribution, $xslt);
					$transformendXmlStr = simplexml_load_string($tranformedEntryXml);
					$json = json_encode($transformendXmlStr);
					return $json;
			default:
				$mrssDoc = $this->getEntryMrssDoc($entryDistribution);
				return $mrssDoc->saveXML();
		}
	}
	
	public function transformXslForEntry(EntryDistribution $entryDistribution, $xsl, $xslParams = array())
	{
		$xslParams['entryDistributionId'] = $entryDistribution->getId();
		$xslParams['distributionProfileId'] = $entryDistribution->getDistributionProfileId();
		
		$mrssDoc = $this->getEntryMrssDoc($entryDistribution);

		$xslDoc = new DOMDocument();
		$xslDoc->loadXML($xsl);
		
		$xslt = new XSLTProcessor;
		$xslt->registerPHPFunctions(); // it is safe to register all php fuctions here
		$xslt->setParameter('', $xslParams);
		$xslt->importStyleSheet($xslDoc);
		
		return $xslt->transformToXml($mrssDoc);
	}
	
	public function getEntryMrssDoc(EntryDistribution $entryDistribution)
	{
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
				
		// set the default criteria to use the current entry distribution partner id (it is restored later)
		// this is needed for related entries under kMetadataMrssManager which is using retrieveByPK without the correct partner id filter
		$oldEntryCriteria = entryPeer::getCriteriaFilter()->getFilter();
		myPartnerUtils::resetPartnerFilter('entry');
		myPartnerUtils::addPartnerToCriteria('entry', $this->getPartnerId(), true);
		
		try
		{
    		$mrss = null;
    		$mrssParams = new kMrssParameters();
    		if ($this->getItemXpathsToExtend())
    			$mrssParams->setItemXpathsToExtend($this->getItemXpathsToExtend());
    		$mrss = kMrssManager::getEntryMrssXml($entry, $mrss, $mrssParams);
    		$mrssStr = $mrss->asXML();
		}
		catch (Exception $e)
		{
		    // restore the original criteria so it will not get stuck due to the exception
		    entryPeer::getCriteriaFilter()->setFilter($oldEntryCriteria);
		    throw $e;
		}
		
		// restore the original criteria
		entryPeer::getCriteriaFilter()->setFilter($oldEntryCriteria);
		
		$mrssObj = new DOMDocument();
        	if(!$mrssObj->loadXML($mrssStr))
			throw new Exception('Entry mrss xml is not valid');
		    
		return $mrssObj;
	}
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return PushToNewsDistributionPlugin::getProvider();
	}
	
	public function getProtocol()						{return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL);}
	public function getHost()							{return $this->getFromCustomData(self::CUSTOM_DATA_HOST);}
	public function getPort()							{return $this->getFromCustomData(self::CUSTOM_DATA_PORT);}
	public function getBasePath()						{return $this->getFromCustomData(self::CUSTOM_DATA_BASE_PATH);}
	public function getUsername()						{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()						{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getCertificateKey()					{return $this->getFromCustomData(self::CUSTOM_DATA_CERTIFICATE_KEY);}
	
	public function getIdXslt()					{return $this->getFromCustomData(self::CUSTOM_METADATA_ID_XSLT);}
	public function getPublishdatXslt()			{return $this->getFromCustomData(self::CUSTOM_DATA_PUBLISHDAT_XSLT);}
	public function getCreationatXslt()			{return $this->getFromCustomData(self::CUSTOM_DATA_CREATIONAT_XSLT);}
	public function getTitlelanguagedatXslt()	{return $this->getFromCustomData(self::CUSTOM_METADATA_TITLELANGUAGEDAT_XSLT);}
	public function getTitleXslt()				{return $this->getFromCustomData(self::CUSTOM_DATA_TITLE_XSLT);}
	public function getMimetypeXslt()			{return $this->getFromCustomData(self::CUSTOM_METADATA_MIMETYPE_XSLT);}
	public function getLanguageXslt()			{return $this->getFromCustomData(self::CUSTOM_METADATA_LANGUAGE_XSLT);}
	public function getBodyXslt()				{return $this->getFromCustomData(self::CUSTOM_METADATA_BODY_XSLT);}
	public function getAuthorNameXslt()					{return $this->getFromCustomData(self::CUSTOM_DATA_AUTHOR_NAME_XSLT);}
	public function getAuthorEmailXslt()				{return $this->getFromCustomData(self::CUSTOM_DATA_AUTHOR_EMAIL_XSLT);}
	public function getRightsinfoCopyrightholderXslt()	{return $this->getFromCustomData(self::CUSTOM_DATA_RIGHTSINFO_COPYRIGHTHOLDER_XSLT);}
	public function getRightsinfoNameXslt()				{return $this->getFromCustomData(self::CUSTOM_DATA_RIGHTSINFO_NAME_XSLT);}
	public function getRightsinfoCopyrightnoticeXslt()	{return $this->getFromCustomData(self::CUSTOM_DATA_RIGHTSINFO_COPYRIGHTNOTICE_XSLT);}
	public function getpProductcodeXslt()				{return $this->getFromCustomData(self::CUSTOM_METADATA_PRODUCTCODE_XSLT);}
	public function getAttributionXslt()				{return $this->getFromCustomData(self::CUSTOM_METADATA_ATTRIBUTION_XSLT);}
	public function getMetadataOrganizationsXslt()		{return $this->getFromCustomData(self::CUSTOM_METADATA_METADATA_ORGANIZATIONS_XSLT);}
	public function getMetadataSubjectsXslt()			{return $this->getFromCustomData(self::CUSTOM_METADATA_METADATA_SUBJECTS_XSLT);}
	
	
	public function setProtocol($v)						{$this->putInCustomData(self::CUSTOM_DATA_PROTOCOL, $v);}
	public function setHost($v)							{$this->putInCustomData(self::CUSTOM_DATA_HOST, $v);}
	public function setPort($v)							{$this->putInCustomData(self::CUSTOM_DATA_PORT, $v);}
	public function setBasePath($v)						{$this->putInCustomData(self::CUSTOM_DATA_BASE_PATH, $v);}
	public function setUsername($v)						{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)						{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setCertificateKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_CERTIFICATE_KEY, $v);}
	
	public function setIdXslt($v)				{$this->putInCustomData(self::CUSTOM_METADATA_ID_XSLT, $v);}
	public function setPublishdatXslt($v)		{$this->putInCustomData(self::CUSTOM_DATA_PUBLISHDAT_XSLT, $v);}
	public function setCreationatXslt($v)		{$this->putInCustomData(self::CUSTOM_DATA_CREATIONAT_XSLT, $v);}
	public function setTitlelanguagedatXslt($v)	{$this->putInCustomData(self::CUSTOM_METADATA_TITLELANGUAGEDAT_XSLT, $v);}
	public function setTitleXslt($v)			{$this->putInCustomData(self::CUSTOM_DATA_TITLE_XSLT, $v);}
	public function setMimetypeXslt($v)			{$this->putInCustomData(self::CUSTOM_METADATA_MIMETYPE_XSLT, $v);}
	public function setLanguageXslt($v)			{$this->putInCustomData(self::CUSTOM_METADATA_LANGUAGE_XSLT, $v);}
	public function setBodyXslt($v)				{$this->putInCustomData(self::CUSTOM_METADATA_BODY_XSLT, $v);}
	public function setAuthorNameXslt($v)		{$this->putInCustomData(self::CUSTOM_DATA_AUTHOR_NAME_XSLT, $v);}
	public function setAuthorEmailXslt($v)					{$this->putInCustomData(self::CUSTOM_DATA_AUTHOR_EMAIL_XSLT, $v);}
	public function setRightsinfoCopyrightholderXslt($v)	{$this->putInCustomData(self::CUSTOM_DATA_RIGHTSINFO_COPYRIGHTHOLDER_XSLT, $v);}
	public function setRightsinfoNameXslt($v)				{$this->putInCustomData(self::CUSTOM_DATA_RIGHTSINFO_NAME_XSLT, $v);}
	public function setRightsinfoCopyrightnoticeXslt($v)	{$this->putInCustomData(self::CUSTOM_DATA_RIGHTSINFO_COPYRIGHTNOTICE_XSLT, $v);}
	public function setProductcodeXslt($v)					{$this->putInCustomData(self::CUSTOM_METADATA_PRODUCTCODE_XSLT, $v);}
	public function setAttributionXslt($v)					{$this->putInCustomData(self::CUSTOM_METADATA_ATTRIBUTION_XSLT, $v);}
	public function setMetadataOrganizationsXslt($v)		{$this->putInCustomData(self::CUSTOM_METADATA_METADATA_ORGANIZATIONS_XSLT, $v);}
	public function setMetadataSubjectsXslt($v)				{$this->putInCustomData(self::CUSTOM_METADATA_METADATA_SUBJECTS_XSLT, $v);}
}
