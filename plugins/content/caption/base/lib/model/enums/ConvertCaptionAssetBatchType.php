<?php
/**
 * @package plugins.caption
  * @subpackage api.enum
   */
   class ConvertCaptionAssetBatchType implements IKalturaPluginEnum, BatchJobType
   {
        const CONVERT_CAPTION_ASSET = 'convertcaptionasset';

        public static function getAdditionalValues()
        {
            return array(
                'CONVERT_CAPTION_ASSET' => self::CONVERT_CAPTION_ASSET
            );
         }

        /**
        * @return array
        */
        public static function getAdditionalDescriptions()
        {
            return array();
        }
    }

