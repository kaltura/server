<?php
/**
 * @package core
 * @subpackage thumbnail.sourceAction
 */

abstract class kSourceAction extends kThumbnailAction
{
	/** @var kThumbnailSource */
	protected $source;

	/**
	 * @return kThumbnailSource
	 * @throws KalturaAPIException
	 */
	protected abstract function doAction();

	/**
	 * @param kThumbnailSource $source
	 * @param array $transformationParameters
	 * @return kThumbnailSource $source
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