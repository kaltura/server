<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KOperationEnginePdf2Swf extends KSingleOutputOperationEngine
{
	const PDF_FORMAT = 'PDF document';
	
	/* (non-PHPdoc)
	 * @see KOperationEngine::doOperation()
	 */
	protected function doOperation()
	{
		$this->validateFormat(self::PDF_FORMAT);
		return parent::doOperation();
	}
}