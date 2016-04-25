<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_XmlFileHandlerConfig extends Form_BaseFileHandlerConfig
{
	/**
	 * {@inheritDoc}
	 * @see Form_BaseFileHandlerConfig::getFileHandlerType()
	 */
	protected function getFileHandlerType()
	{
		return Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::XML;
	}
}