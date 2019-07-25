<?php
/**
 * @package core
 * @subpackage thumbnail
 */

class kImageTransformation
{
	/** @var kImageTransformationStep[] */
	protected $imageSteps = array();

	public function validate()
	{
		myPartnerUtils::blockInactivePartner(kCurrentContext::getCurrentPartnerId());
		$stepsCount = count($this->imageSteps);
		if(!$stepsCount)
		{
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::EMPTY_IMAGE_TRANSFORMATION);
		}

		$firstStep = $this->imageSteps[0];
		if($firstStep->usesCompositeObject())
		{
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::FIRST_STEP_CANT_USE_COMP_ACTION);
		}

		for($i = 1 ; $i < $stepsCount; $i++)
		{
			if(!$this->imageSteps[$i]->usesCompositeObject())
			{
				throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::MISSING_COMPOSITE_ACTION);
			}
		}
	}

	public function execute()
	{
		try
		{
			$transformationParameters = array();
			foreach ($this->imageSteps as $step)
			{
				$transformationParameters[kThumbnailParameterName::COMPOSITE_OBJECT] = $step->execute($transformationParameters);
			}
		}
		catch(ImagickException $e)
		{
			KalturaLog::err('Imagick error:' . print_r($e));
			throw new kThumbnailException(kThumbnailException::TRANSFORMATION_RUNTIME_ERROR, kThumbnailException::TRANSFORMATION_RUNTIME_ERROR);
		}

		return $transformationParameters[kThumbnailParameterName::COMPOSITE_OBJECT];
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	public function addImageTransformationStep($step)
	{
		$this->imageSteps[] = $step;
	}

	public function getLastModified()
	{
		$lastModifiedArray = array();
		foreach ($this->imageSteps as $imageTransformationStep)
		{
			$lastModified = $imageTransformationStep->getLastModified();
			if($lastModified)
			{
				$lastModifiedArray[] = $lastModified;
			}
		}

		if($lastModifiedArray)
		{
			return min($lastModifiedArray);
		}

		return null;
	}

}