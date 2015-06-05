<?php
/**
 * @package plugins.caption
 * @subpackage Scheduler
 */
 class KAsyncParseMultiLanguageCaptionAsset extends KJobHandlerWorker
 {
    /*
     * @var KalturaCaptionSearchClientPlugin
     */
    private $CaptionClientPlugin = null;

    
    /* (non-PHPdoc)
     * @see KBatchBase::getType()
     */
    public static function getType()
    {
      return KalturaBatchJobType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET;
    }

    /* (non-PHPdoc)
     * @see KJobHandlerWorker::exec()
     */
    protected function exec(KalturaBatchJob $job)
    {
       return $this->parseMultiLanguage($job, $job->data);
    }

    protected function parseMultiLanguage(KalturaBatchJob $job, KalturaParseMultiLanguageCaptionAssetJobData $data)
    {
       KalturaLog::debug("parse Multi Language job id - ($job->id)");

       try
       {
           $this->updateJob($job, "Start parsing multi-language caption asset [$data->parentCaptionAssetId]", KalturaBatchJobStatus::QUEUED);
       }
       catch(Exception $e)
       {
            $this->unimpersonate();
            $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, $data);
            return $job;
       }

       $this->CaptionClientPlugin = KalturaCaptionClientPlugin::get(self::$kClient);
       $this->impersonate($job->partnerId);

       $parentId = $data->parentCaptionAssetId;
       $entryId = $data->entryId;
       $fileLoc = $data->fileLocation;
       $xmlString = file_get_contents($fileLoc);
       if (!$xmlString)
       {
            $this->unimpersonate();
            $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'UNABLE_TO_GET_FILE' , "Error: " . 'UNABLE_TO_GET_FILE', KalturaBatchJobStatus::FAILED, $data);
            return $job;
       }

       $xml = simplexml_load_string($xmlString);
       if (!$xml)
       {
           $this->unimpersonate();
           $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, 'INVALID_XML' , "Error: " . 'INVALID_XML', KalturaBatchJobStatus::FAILED, $data);
           return $job;
       }

       $filter = new KalturaAssetFilter();
       $filter->entryIdEqual = $entryId;
       $pager = null;
       try
       {
           $result = $this->CaptionClientPlugin->captionAsset->listAction($filter, $pager);
       }
       catch(Exception $e)
       {
           $this->unimpersonate();   
           $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, $data);
           return $job;
       }

       $captionChildern = array();
       foreach($result->objects as $caption)
       {
          if($caption->parentId == $parentId)
            $captionChildern[] = $caption;
       }

       $indexStart = strpos($xmlString,'<div');
       $indexEnd = strrpos($xmlString, '</div>', -1);

       $subXMLStart = substr($xmlString, 0, $indexStart);
       $subXMLEnd = substr($xmlString, $indexEnd + 6);
       $bodyNode = $xml->body;
          
       foreach ($bodyNode->div as $divNode)
       {
         $shouldUpdate = false;
         $xmlDivNode = $divNode->asXml();
         $langPos = strpos($xmlDivNode, "xml:lang=");
         $languageShort = substr($xmlDivNode, $langPos + 10, 2);
         $languageShort = strtoupper($languageShort);
         foreach($captionChildern as $key => $captionChild)
         {
              $existingLangShort = strtoupper($captionChild->languageCode);
              if ($existingLangShort == $languageShort)
              {
                  unset($captionChildern[$key]);
                  KalturaLog::debug("language $languageShort exists as a child of asset $parentId");
                  $shouldUpdate = true;
                  $id = $captionChild->id;
                  continue;
              }
         }

         $completeXML = $subXMLStart . $xmlDivNode . $subXMLEnd;
         $languageLong = constant('KalturaLanguage::' . $languageShort);

         $captionAsset = new KalturaCaptionAsset();
         $captionAsset->fileExt = 'xml';
         $captionAsset->language = $languageLong;
         $captionAsset->format = KalturaCaptionType::DFXP;
         $captionAsset->parentId = $parentId;

         if (!$shouldUpdate)
         {
            try
            {
                $captionCreated = $this->CaptionClientPlugin->captionAsset->add($entryId , $captionAsset);
            }
            catch(Exception $e)
            {
                KalturaLog::debug("problem with caption creation - language $languageLong - " . $e->getMessage());
                continue;
            }
            $id = $captionCreated->id;
         }
         
         $contentResource = new KalturaStringResource();
         $contentResource->content = $completeXML;

         try
         {
              $captionCreated = $this->CaptionClientPlugin->captionAsset->setContent($id , $contentResource);
         }
         catch(Exception $e)
         {
             KalturaLog::debug("problem with caption content-setting id - $id - language $languageLong - " . $e->getMessage());
             continue;
         }

       }
        self::deleteCaptions($captionChildern);

        $this->unimpersonate();
        $this->closeJob($job, null, null, "Finished parsing", KalturaBatchJobStatus::FINISHED);

      return $job;
    }

    private function deleteCaptions(array $captions)
    {
        foreach ($captions as $key => $caption)
            if (isset($captions[$key]))
            {
                $captionToDeleteId = $caption->id;
                try
                {
                    $this->CaptionClientPlugin->captionAsset->delete($captionToDeleteId);
                }
                catch(Exception $e)
                {
                    $language = $caption->language;
                    KalturaLog::debug("problem with deleting caption id - $captionToDeleteId - language $language - " . $e->getMessage());
                }
            }
        }
   }

