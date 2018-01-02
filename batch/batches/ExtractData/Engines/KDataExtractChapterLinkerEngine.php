<?php
/**
 *
 * @package Scheduler
 * @subpackage DataExtracter.engines
 */

class KDataExtractChapterLinkerEngine extends KDataExtractEngine
{
    
    public function getSubType()
    {
        return KalturaEventType::CONNECTIVITY;
    }

    public function extractData(KalturaFileContainer $fileContainer, $extraParams = array())
    {

    }

}