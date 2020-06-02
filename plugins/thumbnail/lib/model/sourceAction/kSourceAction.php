<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.sourceAction
 */

abstract class kSourceAction extends kThumbnailAction
{
	/** @var kThumbnailSource */
	protected $source;

	/**
	 * @return kThumbnailSource
	 * @throws kThumbnailException
	 */
	protected abstract function doAction();

	/**
	 * @param kThumbnailSource $source
	 * @param array $transformationParameters
	 * @return kThumbnailSource
	 * @throws kThumbnailException
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