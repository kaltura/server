<?php
/**
 * @package plugins.visualRecognition
 * @subpackage model.data
 */
class kVisualRecognitionJobProviderData extends kIntegrationJobProviderData
{
	/**
	 * @var int
	 */
	private  $thumbInterval;

        /**
         * @var KalturaKeyValueArray
         */
        private $externalJobs;

	/**
	 * @return int
	 */
	public function getThumbInterval()
	{
		return $this->thumbInterval;
	}

	/**
	 * @param int $thumbInterval
	 */
	public function setThumbInterval($thumbInterval)
	{
		$this->thumbInterval = $thumbInterval;
	}

        /**
         * @return KalturaKeyValueArray
       	 */
	public function getExternalJobs()
        {
                return $this->externalJobs;
        }

       /**
         * @var KalturaKeyValueArray
       	 */
       public function	setExternalJobs($externalJobs = array())
       {
              $this->externalJobs = $externalJobs;
       }

}
