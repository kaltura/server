<?php
/**
 * 
 * Represents a XML bulk upload handler
 * @author Roni
 * @package plugins.bulk_upload.bulk_upload_xml
 * @subpackage lib
 */
interface IKalturaBulkUploadXMLHandler
{
	/**
	 * 
	 * Handles plugin data for item add bulk upload 
	 * @param KalturaBaseEntry $entry
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemAdded(KalturaBaseEntry $entry, SimpleXMLElement $item);

	/**
	 * 
	 * Handles plugin data for item update bulk upload 
	 * @param KalturaBaseEntry $entry
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemUpdated(KalturaBaseEntry $entry, SimpleXMLElement $item);

	/**
	 * 
	 * Handles plugin data for item delete bulk upload 
	 * @param KalturaBaseEntry $entry
	 * @param SimpleXMLElement $item
	 * @throws KalturaBulkUploadXmlException  
	 */
	public function handleItemDeleted(KalturaBaseEntry $entry, SimpleXMLElement $item);
}