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
		if(!file_exists($filename) || !filesize($filename))
			throw new Exception('Empty file supplied as input');
			
		return $this->loadXML(file_get_contents($filename), $options);
	}

	public function loadXML ( $source, $options = null)
	{
		$regex = '&(?!amp;)';
		$source =  preg_replace($regex,' &amp; ',$source);

		return parent::loadXML($source, $options);
	}

	public function schemaValidate ( $filename )
	{
		if(!file_exists($filename) || !filesize($filename))
			throw new Exception('Empty file supplied as input');
		
		return parent::schemaValidateSource(file_get_contents($filename));
	}

}

