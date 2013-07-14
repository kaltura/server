<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaThumbCropType extends KalturaEnum
{
	const RESIZE = 1;
	const RESIZE_WITH_PADDING = 2;
	const CROP = 3;
	const CROP_FROM_TOP = 4;
	const RESIZE_WITH_FORCE= 5;
	
	/* (non-PHPdoc)
     * @see KalturaEnum::getDescriptions()
     */
    public static function getDescriptions ()
    {
        return array(
            self::RESIZE => 'Resize according to the given dimensions while maintaining the original aspect ratio.',
            self::RESIZE_WITH_PADDING => 'Place the image within the given dimensions and fill the remaining spaces using the given background color.',
            self::CROP => 'Crop according to the given dimensions while maintaining the original aspect ratio. The resulting image may be cover only part of the original image.',
            self::CROP_FROM_TOP => 'Crops the image so that only the upper part of the image remains.',
            self::RESIZE_WITH_FORCE => 'Forcibly resize the image according to the given dimensions without necessarily maintaining the original aspect ratio. ',
        );
        
    }

	
}
