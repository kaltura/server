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
		//TODO throw exception
		}
	}
	
	/**
	 * return serialized kJobData
	 */
	public function getSerializedJobData() {
		return gzuncompress($this->compressedJobData);
	}

}
