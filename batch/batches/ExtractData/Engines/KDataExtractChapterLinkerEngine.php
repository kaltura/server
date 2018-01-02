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
        KalturaLog::log("inside the KDataExtractChapterLinkerEngine");
        KalturaLog::debug(print_r($extraParams, true));
        $partnerId = isset($extraParams[self::PARTNER_ID_FIELD]) ? $extraParams[self::PARTNER_ID_FIELD] : null;
        $entryId = isset($extraParams[self::ENTRY_ID_FIELD]) ? $extraParams[self::ENTRY_ID_FIELD] : null;
        KalturaLog::log("params [$partnerId] [$entryId]");
        
        return array();
    }

}