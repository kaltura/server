<?php
class ComcastDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit
{
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaComcastDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaComcastDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaComcastDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaComcastDistributionJobProviderData");
		
		return false;
	}

	protected function newCustomDataElement($title, $value = '')
	{
		$customDataElement = new ComcastCustomDataElement();
		$customDataElement->title = $title;
		$customDataElement->value = $value;
		return $customDataElement;
	}
	
	public function doSubmit(KalturaDistributionSubmitJobData $data, KalturaComcastDistributionProfile $distributionProfile, KalturaComcastDistributionJobProviderData $providerData)
	{
		$comcastMediaService = new ComcastMediaService($distributionProfile->email, $distributionProfile->password);
		
		$entry = $this->getEntry($data->entryDistribution->entryId);
		$metadataObjects = $this->getMetadataObjects($data->entryDistribution->entryId);
		
		$media = new ComcastMedia();
		$media->contentType = ComcastContentType::_VIDEO;
		$media->language = ComcastLanguage::_ENGLISH;
		$media->rating = 'G';
		
		$media->album = $distributionProfile->album;
		$media->author = $distributionProfile->author;
		$media->keywords = $distributionProfile->keywords;
		
		$media->airdate = $data->entryDistribution->sunrise;
		$media->availableDate = $data->entryDistribution->sunrise;
		$media->expirationDate = $data->entryDistribution->sunset;
		
		$categories = $this->findMetadataValue($metadataObjects, 'ComcastCategory', true);
		$media->categories = new ComcastArrayOfstring();
		foreach($categories as $category)
			$media->categories[] = $category;
			
		$media->copyright = $this->findMetadataValue($metadataObjects, 'copyright');
		
		$media->formats = new ComcastArrayOfFormat();
		$media->formats = ComcastFormat::_FLV;
		
		$media->externalID = $entry->id;
		$media->length = $entry->duration;
		$media->title = $entry->name;
		$media->description = $entry->description;
		
		$media->customData = new ComcastCustomData();
		$media->customData[] = $this->newCustomDataElement('Headline', $this->findMetadataValue($metadataObjects, 'LongTitle'));
		$media->customData[] = $this->newCustomDataElement('Link Href');
		$media->customData[] = $this->newCustomDataElement('Link Text');
		
		$thumbAssets = $this->getThumbAssets($data->entryDistribution->thumbAssetIds);
		if($thumbAssets && count($thumbAssets))
		{
			foreach($thumbAssets as $thumbAsset)
			{
				if($thumbAsset->width == 72 && $thumbAsset->height = 92)
				{				
					$media->thumbnailURL = $this->getThumbAssetUrl($thumbAsset->id);
					break;
				}
			}
		}
		
		$mediaFiles = new ComcastMediaFileList();
		
		$mediaFile = new ComcastMediaFile();
		//$mediaFile->template = new ComcastArrayOfMediaFileField();
		$mediaFile->allowRelease = true;
		$mediaFile->bitrate = $flavorAsset1_Bitrate;
		$mediaFile->contentType = ComcastContentType::_VIDEO;
		$mediaFile->format = ComcastFormat::_FLV;
		$mediaFile->length = $flavorAsset1_Duration;
		$mediaFile->mediaFileType = ComcastMediaFileType::_INTERNAL;
		$mediaFile->originalLocation = $flavorAsset1_URL;
		$mediaFile->height = $flavorAsset1_Width;
		$mediaFile->width = $flavorAsset1_Height;
		$mediaFiles[] = $mediaFile;
		
		
		$mediaFile = new ComcastMediaFile();
		//$mediaFile->template = new ComcastArrayOfMediaFileField();
		$mediaFile->allowRelease = true;
		$mediaFile->bitrate = $flavorAsset2_Bitrate;
		$mediaFile->contentType = ComcastContentType::_VIDEO;
		$mediaFile->format = ComcastFormat::_FLV;
		$mediaFile->length = $flavorAsset2_Duration;
		$mediaFile->mediaFileType = ComcastMediaFileType::_INTERNAL;
		$mediaFile->originalLocation = $flavorAsset2_URL;
		$mediaFile->height = $flavorAsset2_Width;
		$mediaFile->width = $flavorAsset2_Height;
		$mediaFiles[] = $mediaFile;
		
		
		$options = new ComcastAddContentOptions();
		$options->generateThumbnail = false;
		$options->publish = false;
		$options->deleteSource = false;
		
				

		$comcastAddContentResults = $comcastMediaService->addContent($media, $mediaFiles, $options);
		
		$data->sentData = $comcastMediaService->request;
		$data->results = $comcastMediaService->response;
		
		if($comcastAddContentResults->mediaID)
		{
			$data->remoteId = $comcastAddContentResults->mediaID;
		}
		
		if(isset($comcastAddContentResults->faultcode) || isset($comcastAddContentResults->faultstring))
		{
			$err = "addContent failed with code [$comcastAddContentResults->faultcode] and message [$comcastAddContentResults->faultstring]";
			KalturaLog::err($err);
			throw new Exception($err);
		}
		
		return false;
	}
}