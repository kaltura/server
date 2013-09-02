<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model
 */
abstract class ConfigurableDistributionProfile extends DistributionProfile
{
    const CUSTOM_DATA_FIELD_CONFIG_ARRAY = 'fieldConfigArray';
    const CUSTOM_DATA_ITEM_XPATHS_TO_EXTEND = 'itemXpathsToExtend';
	
	protected $fieldConfigArray = null;
	
	protected $fieldValuesByEntryDistributionId = null;
	
	protected $requiredFields = null;
		
	
	/********************************/
	/* Field config array functions */
    /********************************/
	
	/**
	 * @return array<DistributionFieldConfig> An array of the default DistributionFieldConfig configurations
	 * The key of each item in the array MUST be the field name!
	 */
	abstract protected function getDefaultFieldConfigArray();
	
	/**
	 * @return array<DistributionFieldConfig> An array of DistributionFieldConfig objects
	 */
	public function getFieldConfigArray()
	{
	    if (is_null($this->fieldConfigArray))
	    {
	        $this->fieldConfigArray = array();
	        $tempArray = unserialize($this->getFromCustomData(self::CUSTOM_DATA_FIELD_CONFIG_ARRAY));
	        if (!is_array($tempArray)) {
	            $tempArray = array();
	        }
	        foreach ($tempArray as $tempConfig)
	        {
	            if (!$tempConfig instanceof DistributionFieldConfig)
	            	continue;
	            $fieldName = $tempConfig->getFieldName();
	            $tempConfig->setIsDefault(false);
	            $this->fieldConfigArray[$fieldName] = $tempConfig;
	        }
	        
	        // merge with the default array for missing fields
	        $defaultArray = $this->getDefaultFieldConfigArray();
	        foreach ($defaultArray as $defaultConfig)
	        {
	            $fieldName = $defaultConfig->getFieldName();
	            if (!array_key_exists($fieldName, $this->fieldConfigArray))
	            {
	                $defaultConfig->setIsDefault(true);
	                $this->fieldConfigArray[$fieldName] = $defaultConfig;
	            }
	        }
	    }
	    return $this->fieldConfigArray;
	}
	
	/**
	 * @param array<DistributionFieldConfig> $v An array of DistributionFieldConfig objects
	 */
	public function setFieldConfigArray($configArray)
	{
	    $this->fieldConfigArray = null;
	    
	    $defaultConfigArray = $this->getDefaultFieldConfigArray();
	    
	    // turn the given array into an array mapped by field names
	    $tempArray = array();
	    foreach ($configArray as $config)
	    {
	        if ($config instanceof  DistributionFieldConfig) {
	            $fieldName = $config->getFieldName();
	            $defaultRequiredStatus = isset($defaultConfigArray[$fieldName]) ? $defaultConfigArray[$fieldName]->getIsRequired() : null;
	            if ($defaultRequiredStatus == DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER) {
	            	$config->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	            }
	            else if ($config->getIsRequired() == DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER) {
	            	$config->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PARTNER);
	            }
	            if (!empty($fieldName)) {
	                $tempArray[$fieldName] = $config;
	            }
	        }	        
	    }
	    $this->putInCustomData(self::CUSTOM_DATA_FIELD_CONFIG_ARRAY, serialize($tempArray));
	}
	
	public function getItemXpathsToExtend()
	{
		$temp = unserialize($this->getFromCustomData(self::CUSTOM_DATA_ITEM_XPATHS_TO_EXTEND));
		if (is_array($temp))
			return $temp;
		else
			return array();
	}
	
	public function setItemXpathsToExtend($itemXpathsToExtend)
	{
		if (!is_array($itemXpathsToExtend))
			$itemXpathsToExtend = array();
			
		$array = array();
		foreach($itemXpathsToExtend as $val)
		{
			if ($val)
				$array[] = $val;
		}
		$this->putInCustomData(self::CUSTOM_DATA_ITEM_XPATHS_TO_EXTEND, serialize($array));
	}
	
	/**
	 * @param string $fieldName
	 * @return DistributionFieldConfig
	 */
    public function getConfigForField($fieldName)
    {
        $fieldConfigArray = $this->getFieldConfigArray();
        foreach ($fieldConfigArray as $fieldConfig)
        {
            if ($fieldConfig->getFieldName() == $fieldName) {
                return $fieldConfig;
            }
        }
        return null; // config not found
    }
    
    /**
	 * @param string $fieldName
	 * @return string field friendly name or $fieldName when null
	 */
	public function getUserFriendlyFieldName($fieldName)
	{
	    $fieldConfig = $this->getConfigForField($fieldName);
	    if ($fieldConfig) {
            $friendlyName = $fieldConfig->getUserFriendlyFieldName();
            return !is_null($friendlyName) ? $friendlyName : $fieldName;
	    }
	    return null;
	}
	
	
	/**
	 * @param string $fieldName
	 * @return string entry mrss xslt string for the given field
	 */
	public function getEntryMrssXsltForField($fieldName)
	{
	    $fieldConfig = $this->getConfigForField($fieldName);
	    if ($fieldConfig) {
	        $mrssXslt = $fieldConfig->getEntryMrssXslt();
	        if (!is_null($mrssXslt)) {
	            return $mrssXslt;
	        }
	    }	    
	    return null;
	}
	
	
	
	/*****************************************/
	/* Profile XSLT transformation functions */
    /*****************************************/
	
	/**
	 * Returns an associative array of variables to be passed to the XSLT.
	 * This function can be extended by specific profiles to add additonal variables to their XSLT transformations.
	 * @param EntryDistribution $entryDistribution
	 * @return array associative array of variables to be passed to the XSLT
	 */
	protected function getXslVariables(EntryDistribution $entryDistribution)
	{
	    return array(
	        'entryDistributionId' => $entryDistribution->getId(),
	        'distributionProfileId' => $entryDistribution->getDistributionProfileId(),
	    );
	}
	
	
    protected function getFieldValuesXslt($entryDistribution, $fieldName = null)
    {
        $xsl = '<?xml version="1.0" encoding="UTF-8"?>
		<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
			<xsl:output omit-xml-declaration="no" method="xml" />
			<xsl:template match="item">
				<distribution_values>
		';
        
        $variables = $this->getXslVariables($entryDistribution);
        foreach ($variables as $varName => $varValue)
        {
            $xsl .= '<xsl:variable name="'.$varName.'" select="\''.$varValue.'\'"/>';
        }
        
        $fieldConfigArray = $this->getFieldConfigArray();
        foreach ($fieldConfigArray as $fieldConfig)
        {
            $nextFieldName = $fieldConfig->getFieldName();
            if ($fieldName && $fieldName != $nextFieldName) {
                continue;
            }
            if (!empty($nextFieldName))
            {
                $xsl .= '<value id="'.$nextFieldName.'">';
                $xsl .= $this->getEntryMrssXsltForField($nextFieldName);
                $xsl .= '</value>';
            }
        }
             
      $xsl .= '
        		</distribution_values>
        	</xsl:template>
        	'.implode(PHP_EOL.PHP_EOL, $this->getXslTemplates()).' 
        </xsl:stylesheet>
        ';
      
        KalturaLog::debug('Result XSL: '. $xsl);
        return $xsl;
    }
		
    
	protected function getFieldValuesXml(EntryDistribution $entryDistribution, $fieldName = null)
	{
		$entry = entryPeer::retrieveByPKNoFilter($entryDistribution->getEntryId());
        if (!$entry) {
            KalturaLog::err('Entry not found with ID ['. $entryDistribution->getEntryId() .']');
            return null;
        }
		
		// set the default criteria to use the current entry distribution partner id (it is restored later)
		// this is needed for related entries under kMetadataMrssManager which is using retrieveByPK without the correct partner id filter
		$oldEntryCriteria = entryPeer::getCriteriaFilter()->getFilter();
		myPartnerUtils::resetPartnerFilter('entry');
		myPartnerUtils::addPartnerToCriteria('entry', $entryDistribution->getPartnerId(), true);
		
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
		
		if(!$mrssStr)
		{
			KalturaLog::err('No MRSS returned for entry ['.$entry->getId().']');
			return null;
		}
		
		$mrssObj = new DOMDocument();
		if(!$mrssObj->loadXML($mrssStr))
		{
		    KalturaLog::err('Error loading MRSS XML object for entry ['.$entry->getId().']');
			return null;
		}
		
		$xslObj = new DOMDocument();
		$xslStr = $this->getFieldValuesXslt($entryDistribution, $fieldName);
		$xslStr = trim($xslStr);
		
		if(!$xslObj->loadXML($xslStr))
		{
		    KalturaLog::err('Error loading distribution profile XSLT for profile ID ['.$this->getId().']');
			return null;
		}
		
		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions(kXml::getXslEnabledPhpFunctions());
		$proc->importStyleSheet($xslObj);
		
		$resultXmlObj = $proc->transformToDoc($mrssObj);
		if (!$resultXmlObj) {
		    KalturaLog::err('Error transforming XML for distribution profile ['.$this->getId().'] and entry id ['.$entry->getId().']');
		    return null;
		}
		
        /* DEBUG logs
		KalturaLog::log('entry mrss = '.$mrssStr);
		KalturaLog::log('profile xslt = '.$xslStr);
		*/
		
		KalturaLog::debug('Result XML: '.$resultXmlObj->saveXML());		
		return $resultXmlObj;
	}
	
	protected function getFieldValueFromXml($fieldName, $xmlObj)
	{
	    $xpath = new DOMXPath($xmlObj);
        $fieldElement = $xpath->query("//*[@id='$fieldName']")->item(0);
	    
	    if (!$fieldElement) {
	        KalturaLog::err('Cannot find element with ID ['.$fieldName.'] in XML');
	        return null;
	    }
	    $fieldValue = $fieldElement->nodeValue;
	    return $fieldValue;
	}
	
	
	public function getAllFieldValues(EntryDistribution $entryDistribution)
	{
		if (is_null($this->fieldValuesByEntryDistributionId) || !is_array($this->fieldValuesByEntryDistributionId) || !isset($this->fieldValuesByEntryDistributionId[$entryDistribution->getId()]))
		{
		    $valuesXmlObj = $this->getFieldValuesXml($entryDistribution);
		    if (!$valuesXmlObj) {
		        KalturaLog::err('Error transforming XML for distribution profile ['.$this->getId().'] and entry distribution id ['.$entryDistribution->getId().']');
		        return null;
		    }
		    
		    $valuesXmlStr = $valuesXmlObj->saveXML();
		    KalturaLog::debug('All field values result XML: '.$valuesXmlStr);
		    
		    $fieldValues = array();
		    $fieldConfigArray = $this->getFieldConfigArray();
		    foreach ($fieldConfigArray as $fieldConfig)
		    {
		        $fieldName = $fieldConfig->getFieldName();
		        $fieldValues[$fieldName] = $this->getFieldValueFromXml($fieldName, $valuesXmlObj);
		    }
		    $this->fieldValuesByEntryDistributionId[$entryDistribution->getId()] = $fieldValues;
		}	    
	    
	    return $this->fieldValuesByEntryDistributionId[$entryDistribution->getId()];
	}
	
	public function getFieldValue(EntryDistribution $entryDistribution, $fieldName)
	{
	    $valuesXmlObj = $this->getFieldValuesXml($entryDistribution, $fieldName);
	    if (!$valuesXmlObj) {
	        KalturaLog::err('Error transforming XML for distribution profile ['.$this->getId().'] and entry distribution id ['.$entryDistribution->getId().'] field name ['.$fieldName.']');
	        return null;
	    }
	    
	    return $this->getFieldValueFromXml($fieldName, $valuesXmlObj);
	}
	
	public function clearFieldValues()
	{
		$this->fieldValuesByEntryDistributionId = null;
	}
	
	
	/*****************************************/
	/* Update required entry/metadata fields */
    /*****************************************/
	
	
	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredEntryFields()
	 */
	public function getUpdateRequiredEntryFields()
	{
	    $updateRequired = array();
        $fieldConfigArray = $this->getFieldConfigArray();
        foreach ($fieldConfigArray as $fieldConfig)
        {
            if ($fieldConfig->getUpdateOnChange()) {
                $updateParams = $fieldConfig->getUpdateParams();
                foreach ($updateParams as $updateParam)
                {
	                if (stripos($updateParam, 'ENTRY.') === 0) {
	                    $updateRequired[] = $updateParam;
	                }
                }
            }            
        }
        return $updateRequired;
	}
	

	/* (non-PHPdoc)
	 * @see IDistributionProvider::getUpdateRequiredMetadataXPaths()
	 */
	public function getUpdateRequiredMetadataXPaths()
	{
	    $updateRequired = array();
        $fieldConfigArray = $this->getFieldConfigArray();
        foreach ($fieldConfigArray as $fieldConfig)
        {
            if ($fieldConfig->getUpdateOnChange()) {
                $updateParams = $fieldConfig->getUpdateParams();
                foreach ($updateParams as $updateParam)
                {
	                if (stripos($updateParam, "/*[local-name()='metadata']/*[local-name()='") === 0) {
	                    $updateRequired[] = $updateParam;
	                }
                }
            }            
        }
        return $updateRequired;
	}
	

	/*******************************/
	/* Validation helper functions */
    /*******************************/
	
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{	    
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);
	    
	    //TODO: move entry validation to DistributionProfile ?
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if(!$entry)
		{
			KalturaLog::err("Entry [" . $entryDistribution->getEntryId() . "] not found");
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'entry', 'entry not found');
			return $validationErrors;
		}
		
		
		// verify fields markes as required
		$fieldConfigArray = $this->getFieldConfigArray();
		foreach ($fieldConfigArray as $fieldConfig)
		{
			if ($fieldConfig->getIsRequired() != DistributionFieldRequiredStatus::NOT_REQUIRED) {
				$this->addRequiredFieldForValidation($fieldConfig->getFieldName());
			}
		}		
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateNotEmpty($this->requiredFields, $allFieldValues, $action));
						
		return $validationErrors;
	}
	
    
	protected function validateNotEmpty($fieldArray, $allFieldValues, $action)
	{
	    $validationErrors = array();
	    foreach ($fieldArray as $fieldName)
	    {
    	    $value = isset($allFieldValues[$fieldName]) ? $allFieldValues[$fieldName] : null;
    		if (strlen($value) <= 0) {
    		    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
    			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
    			$validationErrors[] = $validationError;
    		}
	    }
	    return $validationErrors;
	}
	
	
	protected function validateMaxLength($fieldArray, $allFieldValues, $action)
	{
	    $validationErrors = array();
	    foreach ($fieldArray as $fieldName => $maxLength)
	    {
    	    $value = isset($allFieldValues[$fieldName]) ? $allFieldValues[$fieldName] : null;
    		if (!empty($value) && strlen($value) > $maxLength) {
    		    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
    			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
    			$validationError->setValidationErrorParam($maxLength);
    			$validationErrors[] = $validationError;
    		}
	    }
	    return $validationErrors;
	}	
	
	protected function validateInListOrNull($fieldArray, $allFieldValues, $action)
	{
	    $validationErrors = array();
	    foreach ($fieldArray as $fieldName => $validValues)
	    {
    	    $value = isset($allFieldValues[$fieldName]) ? $allFieldValues[$fieldName] : null;
    		if (!empty($value) && !in_array($value, $validValues))
		    {
		        $validValuesStr = implode(',',$validValues);
		        $errorMsg = $this->getUserFriendlyFieldName($fieldName).' value must be in ['.$validValuesStr.']';
    		    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
    			$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
    			$validationError->setValidationErrorParam($errorMsg);
    			$validationError->setDescription($errorMsg);
    			$validationErrors[] = $validationError;
		    }
	    }
	    return $validationErrors;
	}
	
	protected function addRequiredFieldForValidation($fieldName)
	{
		if (is_null($this->requiredFields) || !is_array($this->requiredFields))
		{
			$this->requiredFields = array();
		}
		
		if (!in_array($fieldName, $this->requiredFields)) {
			$this->requiredFields[$fieldName] = $fieldName;
		}
	}
	
	/**
	 * @return array<string> an array containing xsl templates to add to the default xsl
	 */
	protected function getXslTemplates()
	{
		$templates = array();
		$stringReplaceAllTemplate = '<xsl:template name="string-replace-all">
				    <xsl:param name="text" />
				    <xsl:param name="replace" />
				    <xsl:param name="by" />
				    <xsl:choose>
				      <xsl:when test="contains($text, $replace)">
				        <xsl:value-of select="substring-before($text,$replace)" />
				        <xsl:value-of select="$by" />
				        <xsl:call-template name="string-replace-all">
				          <xsl:with-param name="text" select="substring-after($text,$replace)" />
				          <xsl:with-param name="replace" select="$replace" />
				          <xsl:with-param name="by" select="$by" />
				        </xsl:call-template>
				      </xsl:when>
				      <xsl:otherwise>
				        <xsl:value-of select="$text" />
				      </xsl:otherwise>
				    </xsl:choose>
			 	</xsl:template>';
		
		$templates[] = $stringReplaceAllTemplate;
		return $templates;
	}
	
}