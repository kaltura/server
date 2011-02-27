<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.data
 */
class kDistributionValidationError
{
	/**
	 * enum from DistributionAction
	 * @var int
	 */
	private $action;
	
	/**
	 * enum from DistributionErrorType
	 * @var int
	 */
	private $errorType;
	
	/**
	 * Missing flavor params id, thumbnail params id or missing metadata field name
	 * @var string
	 */
	private $data;
	
	/**
	 * @var string
	 */
	private $description;
	
	/**
	 * enum from DistributionValidationErrorType
	 * @var int
	 */
	private $validationErrorType;
	
	/**
	 * Parameter for the validation error
	 * For example, minimum value for DistributionValidationErrorType::STRING_TOO_SHORT
	 * @var string
	 */
	private $validationErrorParam;
	
	/**
	 * @return the $action
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @return the $errorType
	 */
	public function getErrorType()
	{
		return $this->errorType;
	}

	/**
	 * @return the $data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return the $description
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param $action the $action to set
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}

	/**
	 * @param $errorType the $errorType to set
	 */
	public function setErrorType($errorType)
	{
		$this->errorType = $errorType;
	}

	/**
	 * @param $data the $data to set
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @param $description the $description to set
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return the $validationErrorType
	 */
	public function getValidationErrorType()
	{
		return $this->validationErrorType;
	}

	/**
	 * @param int $validationErrorType
	 */
	public function setValidationErrorType($validationErrorType)
	{
		$this->validationErrorType = $validationErrorType;
	}
	
	/**
	 * @return the $validationErrorParam
	 */
	public function getValidationErrorParam()
	{
		return $this->validationErrorParam;
	}

	/**
	 * @param string $validationErrorParam
	 */
	public function setValidationErrorParam($validationErrorParam)
	{
		$this->validationErrorParam = $validationErrorParam;
	}
}