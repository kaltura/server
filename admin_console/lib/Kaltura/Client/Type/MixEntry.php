<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_MixEntry extends Kaltura_Client_Type_PlayableEntry
{
	public function getKalturaObjectType()
	{
		return 'KalturaMixEntry';
	}
	
	/**
	 * Indicates whether the user has submited a real thumbnail to the mix (Not the one that was generated automaticaly)
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $hasRealThumbnail = null;

	/**
	 * The editor type used to edit the metadata
	 * 
	 *
	 * @var Kaltura_Client_Enum_EditorType
	 */
	public $editorType = null;

	/**
	 * The xml data of the mix
	 *
	 * @var string
	 */
	public $dataContent = null;


}

