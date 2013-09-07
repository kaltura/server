<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2Outcomes_processingType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiQticommentType
	 */
	protected $qticomment;
	
	/**
	 *
	 * @var WebexXmlQtiasiOutcomesType
	 */
	protected $outcomes;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiObjects_conditionType>
	 */
	protected $objects_condition;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiProcessing_parameterType>
	 */
	protected $processing_parameter;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiMap_outputType>
	 */
	protected $map_output;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiOutcomes_feedback_testType>
	 */
	protected $outcomes_feedback_test;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'qticomment':
				return 'WebexXmlQtiasiQticommentType';
	
			case 'outcomes':
				return 'WebexXmlQtiasiOutcomesType';
	
			case 'objects_condition':
				return 'WebexXmlArray<WebexXmlQtiasiObjects_conditionType>';
	
			case 'processing_parameter':
				return 'WebexXmlArray<WebexXmlQtiasiProcessing_parameterType>';
	
			case 'map_output':
				return 'WebexXmlArray<WebexXmlQtiasiMap_outputType>';
	
			case 'outcomes_feedback_test':
				return 'WebexXmlArray<WebexXmlQtiasiOutcomes_feedback_testType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'qticomment',
			'outcomes',
			'objects_condition',
			'processing_parameter',
			'map_output',
			'outcomes_feedback_test',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'outcomes',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'outcomes_processingType';
	}
	
	/**
	 * @param WebexXmlQtiasiQticommentType $qticomment
	 */
	public function setQticomment(WebexXmlQtiasiQticommentType $qticomment)
	{
		$this->qticomment = $qticomment;
	}
	
	/**
	 * @return WebexXmlQtiasiQticommentType $qticomment
	 */
	public function getQticomment()
	{
		return $this->qticomment;
	}
	
	/**
	 * @param WebexXmlQtiasiOutcomesType $outcomes
	 */
	public function setOutcomes(WebexXmlQtiasiOutcomesType $outcomes)
	{
		$this->outcomes = $outcomes;
	}
	
	/**
	 * @return WebexXmlQtiasiOutcomesType $outcomes
	 */
	public function getOutcomes()
	{
		return $this->outcomes;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiObjects_conditionType> $objects_condition
	 */
	public function setObjects_condition(WebexXmlArray $objects_condition)
	{
		if($objects_condition->getType() != 'WebexXmlQtiasiObjects_conditionType')
			throw new WebexXmlException(get_class($this) . "::objects_condition must be of type WebexXmlQtiasiObjects_conditionType");
		
		$this->objects_condition = $objects_condition;
	}
	
	/**
	 * @return WebexXmlArray $objects_condition
	 */
	public function getObjects_condition()
	{
		return $this->objects_condition;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiProcessing_parameterType> $processing_parameter
	 */
	public function setProcessing_parameter(WebexXmlArray $processing_parameter)
	{
		if($processing_parameter->getType() != 'WebexXmlQtiasiProcessing_parameterType')
			throw new WebexXmlException(get_class($this) . "::processing_parameter must be of type WebexXmlQtiasiProcessing_parameterType");
		
		$this->processing_parameter = $processing_parameter;
	}
	
	/**
	 * @return WebexXmlArray $processing_parameter
	 */
	public function getProcessing_parameter()
	{
		return $this->processing_parameter;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiMap_outputType> $map_output
	 */
	public function setMap_output(WebexXmlArray $map_output)
	{
		if($map_output->getType() != 'WebexXmlQtiasiMap_outputType')
			throw new WebexXmlException(get_class($this) . "::map_output must be of type WebexXmlQtiasiMap_outputType");
		
		$this->map_output = $map_output;
	}
	
	/**
	 * @return WebexXmlArray $map_output
	 */
	public function getMap_output()
	{
		return $this->map_output;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiOutcomes_feedback_testType> $outcomes_feedback_test
	 */
	public function setOutcomes_feedback_test(WebexXmlArray $outcomes_feedback_test)
	{
		if($outcomes_feedback_test->getType() != 'WebexXmlQtiasiOutcomes_feedback_testType')
			throw new WebexXmlException(get_class($this) . "::outcomes_feedback_test must be of type WebexXmlQtiasiOutcomes_feedback_testType");
		
		$this->outcomes_feedback_test = $outcomes_feedback_test;
	}
	
	/**
	 * @return WebexXmlArray $outcomes_feedback_test
	 */
	public function getOutcomes_feedback_test()
	{
		return $this->outcomes_feedback_test;
	}
	
}
		
