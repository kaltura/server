<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class imageTransformationStep
{
	protected $source;
	protected $imageActionCollection = array();
	protected $useCompositeObject = false;

	public function usesCompositeObject()
	{
		return $this->useCompositeObject;
	}

	/**
	 * @param thumbnailSource $source
	 */
	public function setSource($source)
	{
		$this->source = $source;
	}


	/**
	 * @param array $transformationParameters
	 * @return Imagick
	 */
	public function execute($transformationParameters)
	{
		$image = $this->source->getImage();
		foreach($this->imageActionCollection as $action)
		{
			/* @var imagickAction $action */
			$image = $action->execute($image, $transformationParameters);
		}

		return $image;
	}

	/**
	 * @param imagickAction $imageAction
	 */
	public function addAction($imageAction)
	{
		$this->imageActionCollection[] = $imageAction;
		if($imageAction->canHandleCompositeObject())
		{
			$this->useCompositeObject = true;
		}
	}
}