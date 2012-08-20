<?php

/**
 * @package plugins.exampleDistribution
 * @subpackage external
 */
class ExampleExternalApiMediaItem
{
	public $resourceId;
	public $title;
	public $description;
	public $width;
	public $height;
}

/**
 * @package plugins.exampleDistribution
 * @subpackage external
 */
class ExampleExternalApiService
{
	/**
	 * @param ExampleExternalApiMediaItem $mediaItem
	 * @return int media id
	 */
	public static function submit(ExampleExternalApiMediaItem $mediaItem)
	{
		// do something
		return rand(1, 1000);
	}

	/**
	 * @param int $mediaItemId
	 * @return boolean succeed or not
	 */
	public static function wasSubmitSucceed($mediaItemId)
	{
		// do something
		return true;
	}
}