<?php

/**
 * @package infra
 * @subpackage Plugins
 * Represents the xml upload ingestion interface
 */
interface  IKalturaXmlUploadHandler extends IKalturaBase
{
	/**
	 * 
	 * Handles xml upload data - for adding new entry
	 * @param DOMDocument $xml
	 * @param KalturaBaseEntry $entry
	 * @return KalturaBaseEntry - the new entry that was created
	 */
	function handleXmlUploadData(DOMDocument $xml, KalturaBaseEntry $entry);
	
	/**
	 * 
	 * Handles xml update data - for updating a given entry
	 * @param DOMDocument $xml
	 * @param KalturaBaseEntry $entry
	 * @return KalturaBaseEntry - the updated entry
	 */
	function handleXmlUpdateData(DOMDocument $xml, KalturaBaseEntry $entry);
	
	/**
	 * 
	 * Handles xml delete data - for deleting a given entry
	 * @param DOMDocument $xml
	 * @param KalturaBaseEntry $entry
	 * @return KalturaBaseEntry - the deleted entry
	 */
	function handleXmlDeleteData(DOMDocument $xml, KalturaBaseEntry $entry);	
}