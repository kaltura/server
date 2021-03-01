<?php
/**
 * @package plugins.S3DropFolder
 * @subpackage lib
 */

class kDropFolderS3XmlFileHandler extends kDropFolderXmlFileHandler
{
	protected function getDropFolderFileInstance()
	{
		return new S3DropFolderFile();
	}
}