<?php


/**
 * @package plugins.codeCuePoint
 * @subpackage model
 */
class CodeCuePoint extends CuePoint implements IMetadataObject
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
		$this->setType(CodeCuePointPlugin::getCuePointTypeCoreValue(CodeCuePointType::CODE));
	}
	
	/* (non-PHPdoc)
	 * @see IMetadataObject::getMetadataObjectType()
	 */
	public function getMetadataObjectType()
	{
		return CodeCuePointMetadataPlugin::getMetadataObjectTypeCoreValue(CodeCuePointMetadataObjectType::CODE_CUE_POINT);
	}

	public function copyFromLiveToVodEntry( $vodEntry, $adjustedStartTime )
	{
		// Clone the cue point to the destination entry
		$vodThumbCuePoint = parent::copyToEntry( $vodEntry );
		$vodThumbCuePoint->setStartTime( $adjustedStartTime );
		$vodThumbCuePoint->save();
		return $vodThumbCuePoint;
	}

	public function getIsPublic()	              {return true;}
}
