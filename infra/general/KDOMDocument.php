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

	public function load ( $filename , $options = 0 )
	{
		return parent::loadXML(file_get_contents($filename), $options);
	}

	public function schemaValidate ( $filename )
	{
		return parent::schemaValidateSource(file_get_contents($filename));
	}

}

