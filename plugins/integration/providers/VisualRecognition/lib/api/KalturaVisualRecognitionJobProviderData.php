<?php
/**
 * @package plugins.visualRecognition
 * @subpackage api.objects
 */
class KalturaVisualRecognitionJobProviderData extends KalturaIntegrationJobProviderData
{

	/**
         * @var int
         */
        public $thumbInterval;

        /**
         * list of job IDs from external service. These are auto populated, no need to send them
         * @var KalturaKeyValueArray
         */
        public $externalJobs;

	private static $map_between_objects = array
	(
		"thumbInterval" , "externalJobs",
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
