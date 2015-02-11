<?php
/**
 * @package plugins.metadata
 * @subpackage api.filters
 */
class KalturaMetadataFilter extends KalturaMetadataBaseFilter
{	
	static private $map_between_objects = array
	(
		"metadataObjectTypeEqual" => "_eq_object_type",
	);

	/* (non-PHPdoc)
	 * @see KalturaMetadataBaseFilter::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/**
	 * Instantiate default value
	 */
	public function __construct()
	{
		// default value for backward compatibility
		$this->metadataObjectTypeEqual = MetadataObjectType::ENTRY;
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new MetadataFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array()) 
	{
		if($this->metadataObjectTypeEqual == KalturaMetadataObjectType::USER)
		{
			if ($this->objectIdEqual)
			{
				$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->objectIdEqual);
				if($kuser)				
					$this->objectIdEqual = $kuser->getId();
			}
				
			if ($this->objectIdIn)
			{
				$kusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), explode(',', $this->objectIdIn));
				
				$kusersIds = array();
				foreach($kusers as $kuser)				
					$kusersIds[] = $kuser->getId();
				
				$this->objectIdIn = implode(',', $kusersIds);
			}
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
