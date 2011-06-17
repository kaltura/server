<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model
 */
abstract class ConfigurableDistributionProfile extends DistributionProfile
{
    const CUSTOM_DATA_FIELD_CONFIG_ARRAY = 'fieldConfigArray';
	
	protected $fieldConfigArray = null;
	
	protected $fieldValues = null;
	
	protected $requiredFields = null;
		
	
	/********************************/
	/* Field config array functions */
    /********************************/
	
	/**
	 * @return array<DistributionFieldConfig> An array of the default DistributionFieldConfig configurations
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
	            $fieldName = $tempConfig->getFieldName();
	            $this->fieldConfigArray[$fieldName] = $tempConfig;
	        }
	        
	        // merge with the default array for missing fields
	        $defaultArray = $this->getDefaultFieldConfigArray();
	        foreach ($defaultArray as $defaultConfig)
	        {
	            $fieldName = $defaultConfig->getFieldName();
	            if (!array_key_exists($fieldName, $this->fieldConfigArray))
	            {
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
	    
	    // turn the given array into an array mapped by field names
	    $tempArray = array();
	    foreach ($configArray as $config)
	    {
	        if ($config instanceof  DistributionFieldConfig) {
	            $fieldName = $config->getFieldName();
	            if (!empty($fieldName)) {
	                $tempArray[$fieldName] = $config;
	            }
	        }	        
	    }
	    $this->putInCustomData(self::CUSTOM_DATA_FIELD_CONFIG_ARRAY, serialize($tempArray));
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
        </xsl:stylesheet>
        ';
        
        return $xsl;
    }
		
    
	protected function getFieldValuesXml(EntryDistribution $entryDistribution, $fieldName = null)
	{
		$entry = entryPeer::retrieveByPKNoFilter($entryDistribution->getEntryId());
        if (!$entry) {
            KalturaLog::err('Entry not found with ID ['.$entry->getId().']');
            return null;
        }
			
		$mrssStr = kMrssManager::getEntryMrss($entry);
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
		$proc->registerPHPFunctions(kConf::get('xslt_enabled_php_functions'));
		$proc->importStyleSheet($xslObj);
		
		$resultXmlObj = $proc->transformToDoc($mrssObj);
		if (!$resultXmlObj) {
		    KalturaLog::err('Error transforming XML for distribution profile ['.$this->getId().'] and entry id ['.$entry->getId().']');
		    return null;
		}
		
        /* DEBUG logs
		KalturaLog::log('entry mrss = '.$mrssStr);
		KalturaLog::log('profile xslt = '.$xslStr);
		KalturaLog::log('resutl xml = '.$resultXmlObj->saveXML());
		*/
		
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
		if (is_null($this->fieldValues) || !is_array($this->fieldValues))
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
		    $this->fieldValues = $fieldValues;
		}	    
	    
	    return $this->fieldValues;
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
                $updateParam = $fieldConfig->getUpdateParam();
                if (stripos($updateParam, 'ENTRY.') === 0) {
                    $updateRequired[] = $updateParam;
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
                $updateParam = $fieldConfig->getUpdateParam();
                if (stripos($updateParam, "/*[local-name()='metadata']/*[local-name()='") === 0) {
                    $updateRequired[] = $updateParam;
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
			if ($fieldConfig->getIsRequired()) {
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
		
	
}