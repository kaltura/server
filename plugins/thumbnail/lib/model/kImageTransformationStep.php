<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kImageTransformationStep
{
	/** @var kThumbnailSource */
	protected $source;
	protected $imageActionCollection = array();
	protected $sourceActionCollection = array();
	protected $useCompositeObject = false;

	public function usesCompositeObject()
	{
		return $this->useCompositeObject;
	}

	/**
	 * @param kThumbnailSource $source
	 */
	public function setSource($source)
	{
		$this->source = $source;
	}


	/**
	 * @param array $transformationParameters
	 * @return Imagick
	 */
	public function execute(&$transformationParameters)
	{
		foreach($this->sourceActionCollection as $action)
		{
			/* @var kSourceAction $action */
			$this->source = $action->execute($this->source, $transformationParameters);
		}

		$image = $this->source->getImage();
		foreach($this->imageActionCollection as $action)
		{
			/* @var kImagickAction $action */
			$image = $action->execute($image, $transformationParameters);
		}

		return $image;
	}

	/**
	 * @param kImagickAction $imageAction
	 */
	protected function addImageAction($imageAction)
	{
		$this->imageActionCollection[] = $imageAction;
		if($imageAction->canHandleCompositeObject())
		{
			$this->useCompositeObject = true;
		}
	}

	/**
	 * @param kSourceAction $sourceAction
	 */
	protected function addSourceAction($sourceAction)
	{
		$this->sourceActionCollection[] = $sourceAction;
	}

	/**
	 * @param kThumbnailAction $thumbnailAction
	 */
	public function addAction($thumbnailAction)
	{
		switch($thumbnailAction->getActionType())
		{
			case kActionType::SOURCE:
				$this->addSourceAction($thumbnailAction);
				break;
			case kActionType::IMAGICK:
				$this->addImageAction($thumbnailAction);
				break;
		}
	}

	public function getLastModified()
	{
		return $this->source->getLastModified();
	}
}