<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadJobData extends kJobData
{
/**
	 * @var int
	 */
	private $userId;
	
	/**
	 * The screen name of the user
	 * 
	 * @var string
	 */
	private $uploadedBy;
	
	/**
	 * Selected profile id for all bulk entries
	 * @deprecated
	 * @var int
	 */
	private $conversionProfileId;
		
	/**
	 * Number of created entries
	 * @deprecated Use numOfObjects instead
	 * @var int
	 */
	private $numOfEntries;
	
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	private $resultsFileLocalPath;
	
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	private $resultsFileUrl;

	/**
	 * 
	 * The bulk upload job file path
	 * @var string
	 */
	private $filePath;
	
	/**
	 * 
	 * The bulk upload job file name
	 * @var string
	 */
	private $fileName;
	
	/**
	 * Type of object for bulk upload
	 * @var int
	 */
	protected $bulkUploadObjectType;

	/**
	 * Number of created objects
	 * @var int
	 */
	protected $numOfObjects;
	
	/**
	 * Data pertaining to the objects being uploaded
	 * @var kBulkUploadObjectData
	 */
	protected $objectData;
	
	/**
	 * Number of bulk upload results is status ERROR 
	 * @var int
	 */
	protected $numOfErrorObjects;

    	/**
    	 * Recipients of the email for bulk upload success/failure
     	* @var string
     	*/
   	 protected $emailRecipients;

	/**
	 * @return the $userId
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @return the $uploadedBy
	 */
	public function getUploadedBy() {
		return $this->uploadedBy;
	}

	/**
	 * @return the $conversionProfileId
	 */
	public function getConversionProfileId() {
		return $this->conversionProfileId;
	}

	/**
	 * @return the $numOfEntries
	 */
	public function getNumOfEntries() {
		return $this->numOfEntries;
	}

	/**
	 * @param int $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	/**
	 * @param string $uploadedBy
	 */
	public function setUploadedBy($uploadedBy) {
		$this->uploadedBy = $uploadedBy;
	}

	/**
	 * @param int $conversionProfileId
	 */
	public function setConversionProfileId($conversionProfileId) {
		$this->conversionProfileId = $conversionProfileId;
	}

	/**
	 * @param int $numOfEntries
	 */
	public function setNumOfEntries($numOfEntries) {
		$this->numOfEntries = $numOfEntries;
	}
	
	/**
	 * @return the $filePath
	 */
	public function getFilePath() {
		return $this->filePath;
	}

	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
	}
	
	/**
	 * @return the $fileName
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * @param string $fileName
	 */
	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}

	/**
	 * @return the $resultsFileLocalPath
	 */
	public function getResultsFileLocalPath() {
		return $this->resultsFileLocalPath;
	}

	/**
	 * @return the $resultsFileUrl
	 */
	public function getResultsFileUrl() {
		return $this->resultsFileUrl;
	}

	/**
	 * @param string $resultsFileLocalPath
	 */
	public function setResultsFileLocalPath($resultsFileLocalPath) {
		$this->resultsFileLocalPath = $resultsFileLocalPath;
	}

	/**
	 * @param string $resultsFileUrl
	 */
	public function setResultsFileUrl($resultsFileUrl) {
		$this->resultsFileUrl = $resultsFileUrl;
	}
	/**
     	* @return the $numOfObjects
     	*/
    	public function getNumOfObjects ()
   	{
      	 	return $this->numOfObjects;
   	}

	/**
	* @param int $numOfObjects
    	*/
   	public function setNumOfObjects ($numOfObjects)
    	{
    	    	$this->numOfObjects = $numOfObjects;
   	}
	/**
	* @return the $bulkUploadObjectType
    	*/
    	public function getBulkUploadObjectType ()
   	{
        	return $this->bulkUploadObjectType;
    	}

	/**
     	* @param int $bulkUploadObjectType
     	*/
   	public function setBulkUploadObjectType ($bulkUploadObjectType)
   	{
       		$this->bulkUploadObjectType = $bulkUploadObjectType;
   	}
	/**
     	* @return kBulkUploadObjectData
     	*/
    	public function getObjectData ()
 	{
        	return $this->objectData;
    	}

	/**
    	* @param kBulkUploadObjectData $objectData
     	*/
    	public function setObjectData ($objectData)
    	{
        	$this->objectData = $objectData;
    	}
    
	/**
     	* @return int
     	*/
    	public function getNumOfErrorObjects ()
    	{
     	   	return $this->numOfErrorObjects;
   	}

	/**
     	* @param int $numOfErrorObjects
     	*/
    	public function setNumOfErrorObjects ($numOfErrorObjects)
    	{
        	$this->numOfErrorObjects = $numOfErrorObjects;
    	}

    	/**
     	* @param string $emailRecipients
     	*/
    	public function setEmailRecipients ($emailRecipients)
    	{
        	$this->emailRecipients = $emailRecipients;
    	}

    	/**
     	* @return string
     	*/
    	public function getEmailRecipients ()
    	{
        	return $this->emailRecipients;
    	}

}
