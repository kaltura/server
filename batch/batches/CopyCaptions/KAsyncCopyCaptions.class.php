<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */

/**
 * Will copy objects and add them
 * according to the suppolied engine type and filter
 *
 * @package Scheduler
 * @subpackage Copy
 */
class KAsyncCopyCaptions extends KJobHandlerWorker
{

    const START_TIME_ASC = "+startTime";

    /*
	 * @var KalturaCaptionSearchClientPlugin
	 */
    private $captionSearchClientPlugin = null;

    /*
    * @var KalturaCaptionClientPlugin
    */
    private $captionClientPlugin = null;

    public static function getType()
    {
        return KalturaBatchJobType::COPY_CAPTIONS;
    }
    /**
     * (non-PHPdoc)
     * @see KBatchBase::getJobType()
     */
    protected function getJobType()
    {
        return KalturaBatchJobType::COPY_CAPTIONS;
    }

    /* (non-PHPdoc)
     * @see KJobHandlerWorker::exec()
     */
    protected function exec(KalturaBatchJob $job)
    {
        return $this->copyCaptions($job, $job->data);
    }

    /**
     * copy captions on specific time frame
     */
    private function copyCaptions(KalturaBatchJob $job, KalturaCopyCaptionsJobData $data)
    {
        $this->updateJob($job, "Start copying captions from [$data->sourceEntryId] to [$data->entryId]", KalturaBatchJobStatus::QUEUED);
        $this->captionSearchClientPlugin = KalturaCaptionSearchClientPlugin::get(self::$kClient);
        $this->captionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);

        self::impersonate($job->partnerId);
        $originalCaptionAssets = $this->getAllCaptionAsset($data->sourceEntryId);

        foreach ($originalCaptionAssets->objects as $originalCaptionAsset){
            $newCaptionAsset = $this->cloneCaption($data->entryId, $originalCaptionAsset);
            $originalCaptionAssetItems = $this->getCaptionAssetItems($originalCaptionAsset->id, $data->offset, $data->duration);
            $newCaptionAssetResource = $this->createNewCaptionsFile($originalCaptionAsset->id, $originalCaptionAssetItems, $data->offset, $data->duration, $newCaptionAsset->format);
            $updatedCaption = $this->loadNewCaptionAssetFile($newCaptionAsset->id, $newCaptionAssetResource);
            if (!$updatedCaption)
                throw new kApplicativeException(KalturaBatchJobAppErrors::MISSING_ASSETS ,"Error while trying to copy captions");
        }
        self::unimpersonate();

        $this->closeJob($job, null, null, "Finished copying captions", KalturaBatchJobStatus::FINISHED);
        return $job;
    }


    private function getAllCaptionAsset($entryId)
    {
        KalturaLog::info("Retrieve all caption assets for: [$entryId]");
        $filter = new KalturaAssetFilter();
        $filter->entryIdEqual = $entryId;
        try {
            $captionAssetsList = $this->captionClientPlugin->captionAsset->listAction($filter);
        }
        catch(Exception $e) {
            KalturaLog::info("Can't list caption assets for entry id [$entryId] " . $e->getMessage());
        }
        return $captionAssetsList;
    }


    private function cloneCaption($targetEntryId, $originalCaptionAsset)
    {
        KalturaLog::info("Start copying properties from caption asset: [{$originalCaptionAsset->id}]");
        $captionAsset = new KalturaCaptionAsset();
        $propertiesToCopy = array("tags", "fileExt", "language", "label", "format");
        foreach ($propertiesToCopy as $property)
            $captionAsset->$property = $originalCaptionAsset->$property;
        try {
            $newCaption = $this->captionClientPlugin->captionAsset->add($targetEntryId , $captionAsset);
        }
        catch(Exception $e) {
            KalturaLog::info("Couldn't create new caption asset for entry id: [$targetEntryId]" . $e->getMessage());
        }
        return $newCaption;
    }

    private function getCaptionAssetItems($captionAssetId, $offset, $duration){
        $endTime = $offset + $duration;
        $captionItemsStartedBefore = self::getCaptionAssetStartedBeforeClip($captionAssetId, $offset, $endTime);
        $captionItemsStartedOnRange = self::getCaptionAssetStartedOnClipRange($captionAssetId, $offset, $endTime);
        $allCaptions = array_merge($captionItemsStartedBefore->objects, $captionItemsStartedOnRange->objects);
        return $allCaptions;
    }


    private function getCaptionAssetStartedBeforeClip($captionAssetId, $offset, $endTime)
    {
        KalturaLog::info("Retrieve caption asset items associated with captionAssetId: [$captionAssetId] starting before [$offset] and ending before[$endTime]");
        $filter = new KalturaCaptionAssetItemFilter();
        $filter->endTimeGreaterThanOrEqual = $offset;
        $filter->endTimeLessThanOrEqual = $endTime;
        $filter->startTimeLessThanOrEqual = $offset;
        $filter->orderBy = self::START_TIME_ASC;
        try {
            $captionItemsStartedBefore = $this->captionSearchClientPlugin->captionAssetItem->listAction($captionAssetId, $filter);
        }
        catch(Exception $e) {
            KalturaLog::info("Can't list caption assets items for caption asset id: [$captionAssetId]" . $e->getMessage());
        }
        return $captionItemsStartedBefore;
    }

    private function getCaptionAssetStartedOnClipRange($captionAssetId, $offset, $endTime){
        KalturaLog::info("Retrieve caption asset items associated with captionAssetId: [$captionAssetId] starting in the range [$offset - $endTime]");
        $filter = new KalturaCaptionAssetItemFilter();
        $filter->startTimeGreaterThanOrEqual = $offset;
        $filter->startTimeLessThanOrEqual = $endTime;
        $filter->orderBy = self::START_TIME_ASC;
        try {
            $captionItemsStartedOnClipRange = $this->captionSearchClientPlugin->captionAssetItem->listAction($captionAssetId, $filter);
        }
        catch(Exception $e) {
            KalturaLog::info("Can't list caption assets items for caption asset id: [$captionAssetId]" . $e->getMessage());
        }
        return $captionItemsStartedOnClipRange;
    }

    private function loadNewCaptionAssetFile($captionAssetId, $contentResource){
        try {
            $updatedCaption = $this->captionClientPlugin->captionAsset->setContent($captionAssetId, $contentResource);
        }
        catch(Exception $e) {
            KalturaLog::info("Can't set content to caption asset id: [$captionAssetId]" . $e->getMessage());
        }
        return $updatedCaption;
    }


    private function createNewCaptionsFile($captionAssetId, $captionItems, $offset, $duration , $format){
        KalturaLog::info("Create new caption file in format: [$format] with offset: [$offset]");
        $srtContent="";
        switch($format){
            case CaptionType::SRT:
                $srtContent = $this->buildSrtFile($captionItems, $offset);

            case CaptionType::DFXP:
                $captionContent = $this->getCaptionContent($captionAssetId);
                $srtContent = $this->buildDfxpFile($captionContent, $offset, $duration);

            case CaptionType::WEBVTT:
                $srtContent = $this->buildWebvttFile($captionItems, $offset);;

            case CaptionType::CAP:
                //$srtContent = $this->buildCapFile($captionItems, $offset);;
                return;
        }

        $contentResource = new KalturaStringResource();
        $contentResource->content = $srtContent;
        return $contentResource;
    }

    private function buildSrtFile($captionItems, $clipStartTime)
    {
        $result = '';
        $index = 0;

        foreach($captionItems as $captionAssetItem) {
            $result .= $this->addItemToSrt($captionAssetItem, $index, $clipStartTime);
            $index += 1;
        }

        return $result;
    }

    private function addItemToSrt($captionAssetItem, $index, $clipStartTime)
    {
        $adjustedStartTime = $captionAssetItem->startTime - $clipStartTime;
        if ($adjustedStartTime < 0)
            $adjustedStartTime = 0;
        $adjustedEndTime = $captionAssetItem->endTime - $clipStartTime;
        $content = '';
        $content .= $index. "\n";
        $content .= $this->formatTime($adjustedStartTime);
        $content .= " --> ". $this->formatTime($adjustedEndTime). "\n";
        $content .= $captionAssetItem->content. "\n\n";
        return $content;
    }


    private function formatTime($timeInMili){
        $seconds = $timeInMili / 1000;
        $remainder = round($seconds - ($seconds >> 0), 3) * 1000;
        $formatted_remainder = sprintf("%03d", $remainder);

        return gmdate('H:i:s,', $seconds).$formatted_remainder;
    }

    private function buildDfxpFile($captionContent, $clipStartTime, $duration)
    {
        $clipEndTime = $clipStartTime + $duration;
        $xml = new KDOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        try
        {
            $captionContent = trim($captionContent, " \r\n\t");
            $xml->loadXML($captionContent);
        }
        catch(Exception $e)
        {
            KalturaLog::err($e->getMessage());
            return null;
        }
        $xmlUpdatedContent = $this->parseBody($xml, $clipStartTime, $clipEndTime);
        $xmlUpdatedContent = trim($xmlUpdatedContent, " \r\n\t");
        return $xmlUpdatedContent;
    }


    private function getCaptionContent($captionAssetId)
    {
        KalturaLog::info("Retrieve caption assets content for: [$captionAssetId]");

        try {
            $captionAssetContentUrl= $this->captionClientPlugin->captionAsset->serve($captionAssetId);
            $captionAssetContent = KCurlWrapper::getContent($captionAssetContentUrl);
        }
        catch(Exception $e) {
            KalturaLog::info("Can't serve caption asset id [$captionAssetId] " . $e->getMessage());
        }
        return $captionAssetContent;
    }


    private function parseBody(DOMNode $curNode, $clipStartTime, $clipEndTime)
    {
        //$content = $curNode->saveXML();
        //KalturaLog::log("INBAL33 " . $content);
        for ($i = 0; $i < $curNode->childNodes->length; $i++)
        {
            $childNode = $curNode->childNodes->item($i);
            if ($childNode->nodeType != XML_ELEMENT_NODE)
                continue;

            if (strtolower($childNode->nodeName) != 'p')
            {
                $this->parseBody($childNode, $clipStartTime, $clipEndTime);
                continue;
            }

            $captionStartTime = $this->parseStrTTTime($childNode->getAttribute('begin'));
            $captionEndTime = $captionStartTime;
            if($childNode->hasAttribute('end'))
            {
                $captionEndTime = $this->parseStrTTTime($childNode->getAttribute('end'));
            }
            elseif($childNode->hasAttribute('dur'))
            {
                $duration = floatval($childNode->getAttribute('dur')) * 1000;
                $captionEndTime = $captionStartTime + $duration;
            }
            if(!$this->onTimeRange($captionStartTime, $captionEndTime, $clipStartTime, $clipEndTime))
                $curNode->removeChild($childNode);
            else{
                $adjustedStartTime = $captionStartTime - $clipStartTime;
                if ($adjustedStartTime < 0)
                    $adjustedStartTime = 0;
                $adjustedEndTime = $captionEndTime - $clipStartTime;

                $childNode->setAttribute('begin',kXml::integerToTime($adjustedStartTime));
                if($childNode->hasAttribute('end'))
                    $childNode->setAttribute('end',kXml::integerToTime($adjustedEndTime));
            }
        }
        $content = "";
        if(!$curNode instanceof DOMElement) {
            $content = $curNode->saveXML();
        }
        return $content;
    }

    private function parseStrTTTime($timeStr)
    {
        $matches = null;
        if(preg_match('/(\d+)s/', $timeStr))
            return intval($matches[1]) * 1000;

        return kXml::timeToInteger($timeStr);
    }

    private function onTimeRange($captionStartTime, $captionEndTime, $clipStartTime, $clipEndTime){
        if(($captionEndTime >= $clipStartTime) && ($captionEndTime <= $clipEndTime) && ($captionStartTime <= $clipStartTime))
            return true;

        if (($captionStartTime >= $clipStartTime) && ($captionStartTime <= $clipEndTime))
            return true;

        return false;
    }

    private function buildWebvttFile($captionItems, $clipStartTime)
    {
    }


}