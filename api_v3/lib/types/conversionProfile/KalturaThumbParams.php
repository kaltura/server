<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaThumbParams extends KalturaAssetParams 
{
	/**
	 * @var KalturaThumbCropType
	 */
	public $cropType;
	
	/**
	 * @var int
	 */
	public $quality;
	
	/**
	 * @var int
	 */
	public $cropX;
	
	/**
	 * @var int
	 */
	public $cropY;
	
	/**
	 * @var int
	 */
	public $cropWidth;
	
	/**
	 * @var int
	 */
	public $cropHeight;
	
	/**
	 * @var float
	 */
	public $videoOffset;
	
	/**
	 * @var int
	 */
	public $width;
	
	/**
	 * @var int
	 */
	public $height;
	
	/**
	 * @var float
	 */
	public $scaleWidth;
	
	/**
	 * @var float
	 */
	public $scaleHeight;
	
	/**
	 * Hexadecimal value
	 * @var string
	 */
	public $backgroundColor;
	
	/**
	 * Id of the flavor params or the thumbnail params to be used as source for the thumbnail creation
	 * @var int
	 */
	public $sourceParamsId;

	/**
	 * The container format of the Flavor Params
	 *  
	 * @var KalturaContainerFormat
	 * @filter eq
	 */
	public $format;
	
	/**
	 * The image density (dpi) for example: 72 or 96
	 * 
	 * @var int
	 */
	public $density;
	
	/**
	 * Strip profiles and comments
	 * 
	 * @var bool
	 */
	public $stripProfiles;

    /**
     * Create thumbnail from the videoLength*percentage second
     *
     * @var int
     */
    public $videoOffsetInPercentage;
	
	
//	Maybe support will be added in the future
//	
//	/**
//	 * @var KalturaCropProvider
//	 */
//	public $cropProvider;
//	
//	/**
//	 * @var KalturaCropProviderData
//	 */
//	public $cropProviderData;

	
	private static $map_between_objects = array
	(
		"cropType",
		"quality",
		"cropX",
		"cropY",
		"cropWidth",
		"cropHeight",
		"videoOffset",
		"width",
		"height",
		"scaleWidth",
		"scaleHeight",
		"backgroundColor",
		"sourceParamsId",
		"format",
		"density",
		"stripProfiles",
        "videoOffsetInPercentage",
	
//		Maybe support will be added in the future
//		"cropProvider",
//		"cropProviderData",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaAssetParams::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/**
	 * @param array $propertiesToSkip
	 */
	public function validate($propertiesToSkip = array())
	{
		$this->validatePropertyMinMaxValue('quality', 20, 100, true);
		$this->validatePropertyMinMaxValue('cropX', 0, 10000, true);
		$this->validatePropertyMinMaxValue('cropY', 0, 10000, true);
		$this->validatePropertyMinMaxValue('cropWidth', 0, 10000, true);
		$this->validatePropertyMinMaxValue('cropHeight', 0, 10000, true);
		$this->validatePropertyMinMaxValue('width', 0, 10000, true);
		$this->validatePropertyMinMaxValue('height', 0, 10000, true);
		$this->validatePropertyMinMaxValue('scaleWidth', 0, 10, true);
		$this->validatePropertyMinMaxValue('scaleHeight', 0, 10, true);
		$this->validatePropertyMinValue('density', 0, true);
		$this->validatePropertyMinValue('videoOffset', 0, true);
        $this->validatePropertyMinMaxValue('videoOffsetInPercentage', 0, 100, true);
		
		$this->validatePropertyMinMaxLength('backgroundColor', 1, 6, true);
		if(!is_null($this->backgroundColor) && !preg_match('/^[0-9a-fA-F]{1,6}$/', $this->backgroundColor))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_WRONG_FORMAT, $this->getFormattedPropertyNameWithClassName('backgroundColor'), 'six hexadecimal characters');
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyMinLength("name", 1);
		$this->validate($propertiesToSkip);
		
		parent::validateForInsert($propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyMinLength("name", 1, true);
		$this->validate($propertiesToSkip);
			
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaAssetParams::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaAssetParams::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new thumbParams();
			
		return parent::toObject($object, $skip);
	}
}