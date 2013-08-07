<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Score_assessment_result_outcomesType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $score_value;
	
	/**
	 *
	 * @var integer
	 */
	protected $score_min;
	
	/**
	 *
	 * @var integer
	 */
	protected $score_max;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'score_value':
				return 'integer';
	
			case 'score_min':
				return 'integer';
	
			case 'score_max':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'score_value',
			'score_min',
			'score_max',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'score_value',
			'score_min',
			'score_max',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'score_assessment_result_outcomesType';
	}
	
	/**
	 * @param integer $score_value
	 */
	public function setScore_value($score_value)
	{
		$this->score_value = $score_value;
	}
	
	/**
	 * @return integer $score_value
	 */
	public function getScore_value()
	{
		return $this->score_value;
	}
	
	/**
	 * @param integer $score_min
	 */
	public function setScore_min($score_min)
	{
		$this->score_min = $score_min;
	}
	
	/**
	 * @return integer $score_min
	 */
	public function getScore_min()
	{
		return $this->score_min;
	}
	
	/**
	 * @param integer $score_max
	 */
	public function setScore_max($score_max)
	{
		$this->score_max = $score_max;
	}
	
	/**
	 * @return integer $score_max
	 */
	public function getScore_max()
	{
		return $this->score_max;
	}
	
}
		
