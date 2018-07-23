<?php

/**
* Enable the plugin to add additional XML nodes and attributes to entry MRSS
* @package infra
* @subpackage Plugins
*/
abstract class KalturaParentContributedPlugin extends KalturaPlugin implements IKalturaMrssContributor{

    /**
     * @param BaseObject $object
     * @param SimpleXMLElement $mrss
     * @param kMrssParameters $mrssParams
     * @return SimpleXMLElement
     */
    public function contribute(BaseObject $object, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null)
    {
		KalturaLog::debug("using ParentContributedPlugin");
		if(!($object instanceof entry)){
			return;
		}

		$children = entryPeer::retrieveChildEntriesByEntryIdAndPartnerId($object->getId(), $object->getPartnerId());
		if(!count($children)){
			return;
		}
		$childrenNode = $mrss->addChild('children');
		$childrenDom = dom_import_simplexml($childrenNode);
		foreach ($children as $child)
		{
			$childXML = kMrssManager::getEntryMrssXml($child);
			$childDom = dom_import_simplexml($childXML);
			$childDom = $childrenDom->ownerDocument->importNode($childDom, true);
			$childrenDom->appendChild($childDom);
		}
    }

    /**
     * Function returns the object feature type for the use of the KmrssManager
     *
     * @return int
     */
    public function getObjectFeatureType()
    {
        $value = $this->getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . ParentObjectFeatureType::PARENT;
        return kPluginableEnumsManager::apiToCore('ObjectFeatureType', $value);
    }
}