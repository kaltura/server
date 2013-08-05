<?php
/**
 * @package Core
 * @subpackage storage
 */
class kXslPathManager extends kPathManager
{
	/**
	 * will return a pair of file_root and file_path
	 * This is the only function that should be extended for building a different path
	 *
	 * @param ISyncableFile $object
	 * @param int $subType
	 * @param $version
	 */
	public function generateFilePathArr(ISyncableFile $object, $subType, $version = null, $storageProfileId = null)
	{
		// currently xsl paths are only used for assets
		if (!($object instanceof asset))
		{
		    return parent::generateFilePathArr($object, $subType, $version, $storageProfileId);
		}
		
		$storageProfile = kPathManager::getStorageProfile($storageProfileId);
		$params = $storageProfile->getPathManagerParams();
		
		$pathXsl = null;
		if (isset($params[kPathManager::PATH_FORMAT_PARAM]))
		{
		    $pathXsl = $params[kPathManager::PATH_FORMAT_PARAM];
		}
		else
		{
		    $pathXsl = $this->getDefaultPathXsl();
		}
		
		
		$entry = $object->getEntry();
		$xslVariables = $this->getXslVariables($storageProfile, $object, $subType, $version, $entry);
		$xslStr = $this->getXsl($pathXsl, $xslVariables);
		
		try {
		    $path = $this->getPathValue($entry, $xslStr);
		}
		catch (Exception $e) {
		    KalturaLog::err('Error executing XSL - '.$e->getMessage());
		    $path = null;
		}
		if (empty($path)) {
		    KalturaLog::log('Empty path recieved - using parent\'s path instead');
		    return parent::generateFilePathArr($object, $subType, $version, $storageProfileId);
		}
		$path = trim($path);
		
		KalturaLog::debug('Path value ['.$path.']');
		
		$root = '/';
		return array($root, $path);
	}
	
	
	/**
	 * @return default path xsl
	 * Enter description here ...
	 */
	protected function getDefaultPathXsl()
	{
	   // the default xsl will set the path to be {year}{month}{day}/{partnerDir}/{defaultFileName}
	   $xsl = '<xsl:value-of select="php:function(\'date\', \'Ymd\', $currentTime)" />';
	   $xsl .= '<xsl:text>/</xsl:text>';
	   $xsl .= '<xsl:value-of select="floor($partnerId div 1000)"/>';
	   $xsl .= '<xsl:text>/</xsl:text>';
	   $xsl .= '<xsl:value-of select="$defaultFileName"/>';
       return $xsl;
	}
	
	
	/**
	 * Returns an associative array of variables to be passed to the XSLT.
	 * This function can be extended by specific managers to add additonal variables to their XSLT transformations.
	 * @param StorageProfile $storageProfile
	 * @param ISyncableFile $object
	 * @param $subType
	 * @param $version
	 * @param entry $entry
	 * @return array associative array of variables to be passed to the XSLT
	 */
	protected function getXslVariables(StorageProfile $storageProfile, ISyncableFile $object, $subType, $version, entry $entry)
	{
	    return array(
	    	'currentTime' => time(),
	        'storageProfileId' => $storageProfile->getId(),
	        'objectClass' => get_class($object),
	        'objectId' => $object->getId(),
	        'objectSubType' => $subType,
	        'objectVersion' => $version,
	        'entryId' => $entry->getId(),
	        'partnerId' => $entry->getPartnerId(),
	        'defaultFileName' => $object->generateFileName($subType, $version),
	    );
	}
	
	
	/**
	 * @return XSL string to execute on the entrie's XML
	 * @param string $pathXsl
	 * @param $xslVariables
	 */
    protected function getXsl($pathXsl, $xslVariables = array())
    {
        $xsl = '<?xml version="1.0" encoding="UTF-8"?>
		<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" version="1.0">
			<xsl:output omit-xml-declaration="no" method="xml" />
			<xsl:template match="item">	
		';
        
        foreach ($xslVariables as $varName => $varValue)
        {
            $xsl .= '<xsl:variable name="'.$varName.'" select="\''.$varValue.'\'"/>';
        }

        $xsl .= '		<path_value>'.$pathXsl.'</path_value>';        
             
        $xsl .= '
        	</xsl:template>
        </xsl:stylesheet>
        ';
      
        KalturaLog::debug('Result XSL: '. $xsl);
        return $xsl;
    }
    
    
    /**
     * @return string path value
     * @param entry $entry
     * @param string $xslStr
     */
	protected function getPathValue(entry $entry, $xslStr)
	{
		// set the default criteria to use the current entry distribution partner id (it is restored later)
		// this is needed for related entries under kMetadataMrssManager which is using retrieveByPK without the correct partner id filter
		$oldEntryCriteria = entryPeer::getCriteriaFilter()->getFilter();
		myPartnerUtils::resetPartnerFilter('entry');
		myPartnerUtils::addPartnerToCriteria('entry', $entry->getPartnerId(), true);
		
		$mrss = null;
		$mrssParams = new kMrssParameters();
		$mrssParams->setStatuses(array(flavorAsset::ASSET_STATUS_READY, flavorAsset::ASSET_STATUS_EXPORTING));
		$mrss = kMrssManager::getEntryMrssXml($entry, $mrss, $mrssParams);
		$mrssStr = $mrss->asXML();
		
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
		$xslStr = trim($xslStr);
		
		if(!$xslObj->loadXML($xslStr))
		{
		    KalturaLog::err('Error loading XSL');
			return null;
		}
		
		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions(kXml::getXslEnabledPhpFunctions());
		$proc->importStyleSheet($xslObj);
		
		$resultXmlObj = $proc->transformToDoc($mrssObj);
		if (!$resultXmlObj) {
		    KalturaLog::err('Error transforming XML for entry id ['.$entry->getId().']');
		    return null;
		}
		
        /* DEBUG logs
		KalturaLog::log('entry mrss = '.$mrssStr);
		KalturaLog::log('profile xslt = '.$xslStr);
		*/
		
		KalturaLog::debug('Result XML: '.$resultXmlObj->saveXML());

		$xpath = new DOMXPath($resultXmlObj);
        $fieldElement = $xpath->query("//path_value")->item(0);
	    
	    if (!$fieldElement) {
	        KalturaLog::err('Cannot find element <path_value> in XML');
	        return null;
	    }
	    $fieldValue = $fieldElement->nodeValue;
	    return $fieldValue;
	}
	
}