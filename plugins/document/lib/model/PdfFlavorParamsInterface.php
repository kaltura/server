<?php

interface PdfFlavorParamsInterface
{
	

	// -- Conversion Parameters --
	
	public function setResolution($resolution);
	
	public function getResolution();

	
	// -- Paper size --
	
	public function setPaperHeight($height);
	
	public function getPaperHeight();
	
	public function setPaperWidth($width);
	
	public function getPaperWidth();
	
	
	

	
	/*
	 
	// A lot more can be added in the future
	
	// -- PDF Document Parameters --

	public function setAuthorName($author);
	
	public function getAuthorName();
	
	public function setCustomCreationDate($date);
	
	public function getCustomCreationDate();
	
	
	// -- Watermark --
	
	public function setWatermarkString($string);
	
	public function getWatermarkString();
		
	public function setWatermarkFontName($font_name);
	
	public function getWatermarkFontName();
	
	public function setWatermarkFontSize($size);
	
	public function getWatermarkFontSize();
	
	public function setWatermarkOutlineFontThickness($thickness);
	
	public function getWatermarkOutlineFontThickness();
	
	
	*/
	
	
	
	
	
	
}