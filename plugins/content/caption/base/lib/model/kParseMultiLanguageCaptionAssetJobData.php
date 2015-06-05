<?php
/**
 * @package plugins.caption
 * @subpackage model.data
 */
 class kParseMultiLanguageCaptionAssetJobData extends kJobData
 {
      /**
       * @var string
       */
      private $parentCaptionAssetId;

      /**
       * @var string
       */
      private $entryId;

      /**
       * @var string
       */
      private $fileLocation;

      public function getParentCaptionAssetId()
      {
          return $this->parentCaptionAssetId;
      }

      public function setParentCaptionAssetId($parentCaptionAssetId)
      {
          $this->parentCaptionAssetId = $parentCaptionAssetId;
      }

      public function getEntryId()
      {
          return $this->entryId;
      }

      public function setEntryId($entryId)
      {
          $this->entryId = $entryId;
      }

      public function getFileLocation()
      {
          return $this->fileLocation;
      }

      public function setFileLocation($fileLocation)
      {
          $this->fileLocation = $fileLocation;
      }
}

