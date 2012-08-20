<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMixEntry extends KalturaPlayableEntry
{
	/**
	 * Indicates whether the user has submited a real thumbnail to the mix (Not the one that was generated automaticaly)
	 * 
	 * @var bool
	 * @readonly
	 */
	public $hasRealThumbnail;
	
	/**
	 * The editor type used to edit the metadata
	 * 
	 * @var KalturaEditorType
	 */
	public $editorType;

	/**
	 * The xml data of the mix
	 *
	 * @var string
	 */
	public $dataContent;
	
	public function __construct()
	{
		$this->type = KalturaEntryType::MIX;
	}
	
	private static $map_between_objects = array
	(
		"hasRealThumbnail" => "hasRealThumb",
		"editorType",
		"dataContent"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
    public function fromObject($entry)
	{
		parent::fromObject($entry);

		if ($entry->getEditorType() == "kalturaAdvancedEditor" || $entry->getEditorType() == "Keditor")
		    $this->editorType = KalturaEditorType::ADVANCED;
		else
		    $this->editorType = KalturaEditorType::SIMPLE;
	}
	
	public function toObject($entry = null, $skip = array())
	{
		$entry = parent::toObject($entry, $skip);
		
		if ($this->editorType === KalturaEditorType::ADVANCED)
			$entry->setEditorType("kalturaAdvancedEditor");
		else
			$entry->setEditorType("kalturaSimpleEditor");
			
		return $entry;
	}
}
?>