<?php

/**
 *  @package infra
 *  @subpackage general
 */
class KDOMDocument extends DOMDocument
{
	public function __construct ($version = '1.0', $encoding = '' )
	{
		parent::__construct($version, $encoding);
	}

	public function load($filename ,$options = 0 ,$key = null, $iv = null)
	{
		if(!kFile::checkFileExists($filename) || !kFile::fileSize($filename))
			throw new Exception('Empty file supplied as input');

		return parent::loadXML(kEncryptFileUtils::getEncryptedFileContent($filename, $key, $iv), $options);
	}

	public function schemaValidate($filename, $key = null, $iv = null): bool
	{
		if(!kFile::checkFileExists($filename) || !kFile::fileSize($filename))
			throw new Exception('Empty file supplied as input');
		
		return parent::schemaValidateSource(kEncryptFileUtils::getEncryptedFileContent($filename, $key, $iv));
	}

}

