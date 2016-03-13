<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KOperationEngineThumbAssetsGenerator extends KOperationEngineDocument
{
	const IMAGES_LIST_XML_NAME = 'imagesList.xml';

	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		KalturaLog::info('In KOperationEngineGenerateThumbs');
		$this->generateThumbAssets($this->parseImagesListXML());

		return true;
	}

	private function parseImagesListXML(){
		$imagesList = array();
		$imagesXml = simplexml_load_file($this->inFilePath . DIRECTORY_SEPARATOR . self::IMAGES_LIST_XML_NAME);

		foreach ($imagesXml->item as $item) {
			$imagesList[] = (string)$item->name;
		}

		return $imagesList;
	}

	private function generateThumbAssets($imagesList)
	{
		KBatchBase::impersonate($this->job->partnerId);
		$entry = KBatchBase::$kClient->baseEntry->get($this->job->entryId);
		KBatchBase::unimpersonate();
		if ( !$entry || !$entry->parentEntryId ) {
			KalturaLog::info('no parentEntryId, cannot generate thumbAssets');
			return;
		}
		KalturaLog::debug("start generate thumbassets");
		KBatchBase::$kClient->startMultiRequest();
		$index = 0;
		foreach ($imagesList as $image) {
			$thumbCuePoint = new KalturaThumbCuePoint();
			$thumbCuePoint->entryId = $entry->parentEntryId;
			KBatchBase::$kClient->cuePoint->add( $thumbCuePoint ) ;
			$index++;

			$thumbAsset = new KalturaTimedThumbAsset();
			$thumbAsset->tags = $this->job->entryId;
			$thumbAsset->cuePointId = "{" . $index . ":result:id}";
			KBatchBase::$kClient->thumbAsset->add( $entry->parentEntryId, $thumbAsset) ;
			$index++;

			$resource = new KalturaServerFileResource();
			$resource->localFilePath = $this->inFilePath . DIRECTORY_SEPARATOR . $image;
			KBatchBase::$kClient->thumbAsset->setContent("{" . $index . ":result:id}", $resource);
			$index++;
		}
		KBatchBase::$kClient->doMultiRequest();
	}
}
