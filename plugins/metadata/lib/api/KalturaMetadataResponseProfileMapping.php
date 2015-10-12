<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaMetadataResponseProfileMapping extends KalturaResponseProfileMapping
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object = null, $propertiesToSkip = array())
	{
		if(is_null($object))
		{
			$object = new kMetadataResponseProfileMapping();
		}

		return parent::toObject($object, $propertiesToSkip);
	}

	public function apply(KalturaRelatedFilter $filter, KalturaObject $parentObject)
	{
		$filterProperty = $this->filterProperty;
		$parentProperty = $this->parentProperty;

		KalturaLog::info("Mapping XPath $parentProperty to " . get_class($filter) . "::$filterProperty");
	
		if(!$parentObject instanceof KalturaMetadata)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_TYPE, get_class($parentObject));
		}

		if(!property_exists($filter, $filterProperty))
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_IS_NOT_DEFINED, $filterProperty, get_class($filter));
		}

		$xml = $parentObject->xml;
		$doc = new KDOMDocument();
		$doc->loadXML($xml);
		$xpath = new DOMXPath($doc);
		$metadataElements = $xpath->query($parentProperty);
		if ($metadataElements->length == 1)
		{
			$filter->$filterProperty = $metadataElements->item(0)->nodeValue;
		}
		elseif ($metadataElements->length > 1)
		{
			$values = array();
			foreach($metadataElements as $element)
				$values[] = $element->nodeValue;
			$filter->$filterProperty = implode(',', $values);
		}
		elseif (!$this->allowNull)
		{
			return false;
		}
		return true;
	}
}