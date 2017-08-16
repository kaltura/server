<?php


/**
 * @package plugins.codeCuePoint
 * @subpackage model
 */
class CodeCuePoint extends CuePoint implements IMetadataObject
{
	public function __construct() 
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setType(CodeCuePointPlugin::getCuePointTypeCoreValue(CodeCuePointType::CODE));
	}
	
	/* (non-PHPdoc)
	 * @see IMetadataObject::getMetadataObjectType()
	 */
	public function getMetadataObjectType()
	{
		return CodeCuePointMetadataPlugin::getMetadataObjectTypeCoreValue(CodeCuePointMetadataObjectType::CODE_CUE_POINT);
	}

	public function copyFromLiveToVodEntry( $vodEntry, $adjustedStartTime )
	{
		// Clone the cue point to the destination entry
		$vodCodeCuePoint = parent::copyToEntry( $vodEntry );
		$vodCodeCuePoint->setStartTime( $adjustedStartTime );
		$vodCodeCuePoint->save();
		return $vodCodeCuePoint;
	}

	public function getIsPublic()	              {return true;}

	public function contributeElasticData()
	{
		$data = null;
		if($this->getName())
			$data['cue_point_name'] = $this->getName();//todo change after debug -need to add pools data here

		if($this->getText())
			$data['cue_point_text'] = $this->getText();

		if($this->getPartnerData())
			$this->addElasticPoolsData($data);

		return $data;
	}

	private function addElasticPoolsData(&$data)
	{
		$partnerData = $this->getPartnerData();
		$pd = json_decode($partnerData, true);
		if(isset($pd['text']))
		{
			if(isset($pd['text']['question']) && $pd['text']['question'])
				$data['cue_point_question'] = $pd['text']['question'];
			if(isset($pd['text']['answers']) && $pd['text']['answers'])
			{
				$answers = null;
				foreach ($pd['text']['answers'] as $answer)
					$answers[] = $answer;
				$data['cue_point_answers'] = $answers;
			}

		}
	}
}
