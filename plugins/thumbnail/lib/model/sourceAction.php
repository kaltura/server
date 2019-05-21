<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

abstract class sourceAction extends kThumbnailAction
{
	/** @var thumbnailSource */
	protected $source;

	/**
	 * @return thumbnailSource
	 * @throws KalturaAPIException
	 */
	protected abstract function doAction();

	/**
	 * @param thumbnailSource $source
	 * @param array $transformationParameters
	 * @return thumbnailSource $source
	 * @throws KalturaAPIException
	 */
	public function execute($source, &$transformationParameters)
	{
		$this->source = $source;
		$this->transformationParameters = $transformationParameters;
		$this->extractActionParameters();
		$this->validateInput();
		return $this->doAction();
	}

	public function getActionType()
	{
		return kActionType::SOURCE;
	}
}