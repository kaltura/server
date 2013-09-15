<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Outcomes_item_resultType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiScore_item_result_outcomesType
	 */
	protected $score;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'score':
				return 'WebexXmlQtiScore_item_result_outcomesType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'score',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'score',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'outcomes_item_resultType';
	}
	
	/**
	 * @param WebexXmlQtiScore_item_result_outcomesType $score
	 */
	public function setScore(WebexXmlQtiScore_item_result_outcomesType $score)
	{
		$this->score = $score;
	}
	
	/**
	 * @return WebexXmlQtiScore_item_result_outcomesType $score
	 */
	public function getScore()
	{
		return $this->score;
	}
	
}
		
