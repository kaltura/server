<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
abstract class Form_BaseFileHandlerConfig extends Zend_Form_SubForm
{
	abstract protected function getFileHandlerType();

	/**
	 * @param Kaltura_Client_DropFolder_Type_DropFolder $object
	 */
	public function applyObjectAttributes(Kaltura_Client_DropFolder_Type_DropFolder &$object)
	{
	}
	
	public static function getFileHandlerTypes()
	{
		$oClass = new ReflectionClass('Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType');
		return $oClass->getConstants();
	}
	
	/**
	 * {@inheritDoc}
	 * @see Zend_Form::init()
	 */
	public function init()
	{
		$formName = 'frmContentFileHandlerConfig' . str_replace('.', '_', $this->getFileHandlerType());
		$this->setDecorators(array(
	        'FormElements',
	        array('HtmlTag', array('tag' => 'span', 'id' => $formName, 'class' => 'contentFileHandler')),
        ));
	}
}