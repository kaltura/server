<?php

class PdfFlavorParamsOutput extends flavorParamsOutput implements PdfFlavorParamsInterface
{
	//TODO: which TAGS are valid ??
	
	
	// -- Conversion Parameters --
	
	public function setResolution($resolution)
	{
		parent::putInCustomData('resolution', $resolution);
	}
	
	public function getResolution()
	{
		parent::getFromCustomData('resolution');
	}
	
	
	// -- Paper size --
	
	public function setPaperHeight($height)
	{
		parent::putInCustomData('paperHeight', $height);
	}
	
	public function getPaperHeight()
	{
		parent::getFromCustomData('paperHeight');
	}
	
	public function setPaperWidth($width)
	{
		parent::putInCustomData('paperWidth', $width);
	}
	
	public function getPaperWidth()
	{
		parent::getFromCustomData('paperWidth');
	}
}