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
		$refId = $this->getReferenceID();
		if ($refId)
		{
			$copyObj->setReferenceID($refId);
		}
		return $copyObj;
	}

	public function copyTemplate($copyPartnerId = false, $template)
	{
		if ($template instanceof ExternalMediaEntry)
		{
			$this->setExternalSourceType($template->getExternalSourceType());
			$this->setReferenceID($template->getReferenceID());
		}
		return parent::copyTemplate($copyPartnerId, $template);

	}

	public function getObjectParams($params = null)
	{
		$body = array(
			'external_source_type' => $this->getExternalSourceType(),
		);

		elasticSearchUtils::cleanEmptyValues($body);

		return array_merge(parent::getObjectParams($params), $body);
	}

}