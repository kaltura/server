<?php
/**
 * this class represent a compressed serialized kJobDate
 * @package Core
 * @subpackage model.data
 */
class kJobCompressedData extends kJobData {
	
	/**
	 * the compressed kJobData
	 * @var string
	 */
	private $compressedJobData;
	
	/**
	 * constructor. get a serialized kJobData and compress it.
	 * @param string $serializedJobData serialized kJobData
	 */
	public function kJobCompressedData($serializedJobData) {
		$this->compressedJobData = gzcompress ( $serializedJobData );
		if (! $this->compressedJobData) {
			throw new Exception ( KalturaErrors::ERROR_OCCURED_WHILE_GZCOMPRESS );
		}
	}
	
	/**
	 * return serialized kJobData
	 */
	public function getSerializedJobData() {
		$serializedJobData = gzuncompress ( $this->compressedJobData );
		if ($serializedJobData )
			return $serializedJobData;
		else
			throw new KalturaBatchException ( KalturaErrors::ERROR_OCCURED_WHILE_GZUNCOMPRESS);
	}

}
