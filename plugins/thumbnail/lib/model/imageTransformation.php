<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class imageTransformation
{
	/** @var imageTransformationStep[] */
	protected $imageSteps = array();

	public function validate()
	{
		$stepsCount = count($this->imageSteps);
		if(!$stepsCount)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::EMPTY_IMAGE_TRANSFORMATION);
		}

		$firstStep = $this->imageSteps[0];
		if($firstStep->usesCompositeObject())
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::FIRST_STEP_CANT_USE_COMP_ACTION);
		}

		for($i = 1 ; $i < $stepsCount; $i++)
		{
			if(!$this->imageSteps[$i]->usesCompositeObject())
			{
				throw new KalturaAPIException(KalturaThumbnailErrors::MISSING_COMPOSITE_ACTION);
			}
		}
	}

	public function execute()
	{
		$transformationParameters = array();
		foreach ($this->imageSteps as $step)
		{
			$transformationParameters[kThumbnailParameterName::COMPOSITE_OBJECT] = $step->execute($transformationParameters);
		}

		return $transformationParameters[kThumbnailParameterName::COMPOSITE_OBJECT];
	}

	/**
	 * @param imageTransformationStep $step
	 */
	public function addImageTransformationStep($step)
	{
		$this->imageSteps[] = $step;
	}
}