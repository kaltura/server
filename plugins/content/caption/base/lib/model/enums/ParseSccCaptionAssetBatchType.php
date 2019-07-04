<?php
/**
 * @package plugins.caption
  * @subpackage api.enum
   */
   class ParseSccCaptionAssetBatchType implements IKalturaPluginEnum, BatchJobType
   {
        const PARSE_SCC_CAPTION_ASSET = 'parsescccaptionasset';

        public static function getAdditionalValues()
        {
            return array(
                'PARSE_SCC_CAPTION_ASSET' => self::PARSE_SCC_CAPTION_ASSET
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

