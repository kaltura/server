<?php

/**
 *  @package infra
 *  @subpackage general
 */
class KDOMDocument extends DOMDocument
{
	public function __construct ($version = null, $encoding = null )
	{
		parent::__construct($version, $encoding);
	}

	public function load ( $filename , $options = 0 , $key = null)
	{
		if(!file_exists($filename) || !filesize($filename))
			throw new Exception('Empty file supplied as input');
			
		return parent::loadXML(kEncryptFileUtils::getEncryptedFileContent($filename, $key), $options);
	}

	public function schemaValidate ( $filename , $key = null)
	{
		if(!file_exists($filename) || !filesize($filename))
			throw new Exception('Empty file supplied as input');
		
		return parent::schemaValidateSource(kEncryptFileUtils::getEncryptedFileContent($filename, $key));
	}

}

