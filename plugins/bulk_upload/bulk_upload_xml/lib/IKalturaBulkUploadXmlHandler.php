<?php
/**
 * Plugins can handle bulk upload xml additional data by implementing this interface
 * @package plugins.bulkUploadXml
 * @subpackage lib
 */
interface IKalturaBulkUploadXmlHandler
{
	/**
	 * Handles plugin data for new created object 
	 * @param KalturaClient $client
	 * @param KalturaObjectBase $object
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemAdded(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item);

	/**
	 * 
	 * Handles plugin data for updated object  
	 * @param KalturaClient $client
	 * @param KalturaObjectBase $object
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemUpdated(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item);

	/**
	 * 
	 * Handles plugin data for deleted object  
	 * @param KalturaClient $client
	 * @param KalturaObjectBase $object
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemDeleted(KalturaClient $client, KalturaObjectBase $object, SimpleXMLElement $item);
}