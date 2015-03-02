<?php
/**
 * @package plugins.annotation
 * @subpackage model
 */
class Annotation extends CuePoint implements IMetadataObject
{
	public function __construct() 
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setType(AnnotationPlugin::getCuePointTypeCoreValue(AnnotationCuePointType::ANNOTATION));
	}
	
	/* (non-PHPdoc)
	 * @see IMetadataObject::getMetadataObjectType()
	 */
	public function getMetadataObjectType()
	{
		return AnnotationMetadataPlugin::getMetadataObjectTypeCoreValue(AnnotationMetadataObjectType::ANNOTATION);
	}
	
	public function contributeData()
	{
		$data = null;
		
		if($this->getText())
			$data = $data . $this->getText() . ' ';
		
		if($this->getTags())
			$data = $data . $this->getTags() . ' ';
			
		return $data;
	}
}
