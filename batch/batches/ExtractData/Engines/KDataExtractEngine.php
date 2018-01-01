<?php
/**
 *
 * @package Scheduler
 * @subpackage ExtractData.engines
 */
abstract class KDataExtractEngine
{
    /**
     * Will return the proper engine depending on the type (KalturaConversionEngineType)
     *
     * @param int $type
     * @return KConversionEngine
     */
    public static function getInstance($type)
    {
        switch ($type)
        {
            case KalturaDataExtractEngineType::MUSIC_RECOGNIZER:
                return null;
            case KalturaDataExtractEngineType::CHAPTER_LINKER:
                return null;
            default:
                return null;
        }
    }

    abstract public function getSubType();

    /**
     * @param KalturaFileContainer $fileContainer
     *  return keys: startTime and data
     * @return array
     */
    abstract public function extractData ( KalturaFileContainer $fileContainer );


}


