<?php
/**
 * @package plugins.externalMedia
 * @subpackage model
 */
class ExternalMediaEntry extends entry
{
	const CUSTOM_DATA_FIELD_EXTERNAL_SOURCE = 'externalSource';
	
	/* (non-PHPdoc)
	 * @see entry::getDownloadFileSyncAndLocal($version, $format, $sub_type)
	 */
	public function getDownloadFileSyncAndLocal ( $version = NULL , $format = null , $sub_type = null )
	{
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see entry::getDownloadUrl($version)
	 */
	public function getDownloadUrl( $version = NULL )
	{
		return null;
	}
	
	/**
	 * @return string external source, of enum ExternalMediaSourceType
	 */
	public function getExternalSourceType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_EXTERNAL_SOURCE);
	}
	
	/**
	 * @param string $v external source, of enum ExternalMediaSourceType
	 */
	public function setExternalSourceType($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_EXTERNAL_SOURCE, $v);
	}

	public function copyTemplate($coptPartnerId = false, $entry = null)
	{
		if ($entry)
		{
			$templateExternalSourceType = $this->getExternalSourceType();
			$externalSourceType = $entry instanceof ExternalMediaEntry ? $entry->getExternalSourceType() : "null";
	
			if($templateExternalSourceType != $externalSourceType)
				KalturaLog::debug('ENTRY_TEMPLATE_COPY_SOURCE_TYPE - original entry:template entry. externalSourceType - ' . $externalSourceType.':'.$templateExternalSourceType);
		}
		return parent::copyTemplate($coptPartnerId, $entry);
	}
	
	/* (non-PHPdoc)
	 * @see entry::getCreateThumb()
	 */
	public function getCreateThumb()
	{
		return false;
	}

	/* (non-PHPdoc)
	  * @see entry::Baseentry()
	  */
	public function copy($deepCopy = false)
	{
		$copyObj = parent::copy($deepCopy);
		$copyObj->setExternalSourceType($this->getExternalSourceType());
		return $copyObj;
	}
	
}