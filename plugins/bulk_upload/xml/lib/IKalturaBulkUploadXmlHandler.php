<?php
/**
 * Plugins can handle bulk upload xml additional data by implementing this interface
 * @package plugins.bulkUploadXml
 * @subpackage lib
 */
interface IKalturaBulkUploadXmlHandler
{
	/**
	 * Configures the handler by passing all the required configuration 
	 * @param BulkUploadEngineXml $xmlBulkUploadEngine  
	 */
	public function configureBulkUploadXmlHandler(BulkUploadEngineXml $xmlBulkUploadEngine);
	
	/**
	 * Handles plugin data for new created object 
	 * @param KalturaObjectBase $object
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemAdded(KalturaObjectBase $object, SimpleXMLElement $item);

	/**
	 * 
	 * Handles plugin data for updated object  
	 * @param KalturaObjectBase $object
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemUpdated(KalturaObjectBase $object, SimpleXMLElement $item);

	/**
	 * 
	 * Handles plugin data for deleted object  
	 * @param KalturaObjectBase $object
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemDeleted(KalturaObjectBase $object, SimpleXMLElement $item);
	
	/**
	 * Return the container name to be handeled
	 */
	public function getContainerName();
}