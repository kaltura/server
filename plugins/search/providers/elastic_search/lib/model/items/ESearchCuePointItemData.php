<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
class ESearchCuePointItemData extends ESearchItemData
{

	/**
	 * @string
	 **/
	protected $cuePointType;

	/**
	 * @string
	 **/
	protected $id;

	/**
	 * @string
	 **/
	protected $name;

	/**
	 * @string
	 **/
	protected $text;

	/**
	 * @string
	 **/
	protected $tags;

	/**
	 * @string
	 **/
	protected $startTime;

	/**
	 * @string
	 **/
	protected $endTime;

	/**
	 * @string
	 **/
	protected $subType;

	/**
	 * @string
	 **/
	protected $question;

	/**
	 * @string
	 **/
	protected $answers;

	/**
	 * @string
	 **/
	protected $hint;

	/**
	 * @string
	 **/
	protected $explanation;

	/**
	 * @return mixed
	 */
	public function getCuePointType()
	{
		return $this->cuePointType;
	}

	/**
	 * @param mixed $cuePointType
	 */
	public function setCuePointType($cuePointType)
	{
		$this->cuePointType = $cuePointType;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @param mixed $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @return mixed
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param mixed $tags
	 */
	public function setTags($tags)
	{
		$this->tags = $tags;
	}

	/**
	 * @return mixed
	 */
	public function getStartTime()
	{
		return $this->startTime;
	}

	/**
	 * @param mixed $startTime
	 */
	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
	}

	/**
	 * @return mixed
	 */
	public function getEndTime()
	{
		return $this->endTime;
	}

	/**
	 * @param mixed $endTime
	 */
	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
	}

	/**
	 * @return mixed
	 */
	public function getSubType()
	{
		return $this->subType;
	}

	/**
	 * @param mixed $subType
	 */
	public function setSubType($subType)
	{
		$this->subType = $subType;
	}

	/**
	 * @return mixed
	 */
	public function getQuestion()
	{
		return $this->question;
	}

	/**
	 * @param mixed $question
	 */
	public function setQuestion($question)
	{
		$this->question = $question;
	}

	/**
	 * @return mixed
	 */
	public function getAnswers()
	{
		return $this->answers;
	}

	/**
	 * @param mixed $answers
	 */
	public function setAnswers($answers)
	{
		$this->answers = $answers;
	}

	/**
	 * @return mixed
	 */
	public function getHint()
	{
		return $this->hint;
	}

	/**
	 * @param mixed $hint
	 */
	public function setHint($hint)
	{
		$this->hint = $hint;
	}

	/**
	 * @return mixed
	 */
	public function getExplanation()
	{
		return $this->explanation;
	}

	/**
	 * @param mixed $explanation
	 */
	public function setExplanation($explanation)
	{
		$this->explanation = $explanation;
	}

	public function getType()
	{
		return ESearchItemDataType::CUE_POINTS;
	}

	public function loadFromElasticHits($objectResult)
	{
		if(isset($objectResult['_source']['cue_point_type']))
		{
			$apiCuePointType = kPluginableEnumsManager::coreToApi('CuePointType', $objectResult['_source']['cue_point_type']);
			$this->cuePointType = $apiCuePointType;
		}

		$this->id = $objectResult['_source']['cue_point_id'];

		if (isset($objectResult['_source']['cue_point_name']))
			$this->name = $objectResult['_source']['cue_point_name'];

		if (isset($objectResult['_source']['cue_point_start_time']))
			$this->startTime = $objectResult['_source']['cue_point_start_time'];

		if (isset($objectResult['_source']['cue_point_end_time']))
			$this->endTime = $objectResult['_source']['cue_point_end_time'];

		if (isset($objectResult['_source']['cue_point_text']))
			$this->text = $objectResult['_source']['cue_point_text'];

		if (isset($objectResult['_source']['cue_point_tags']))
			$this->tags = $objectResult['_source']['cue_point_tags'];

		if (isset($objectResult['_source']['cue_point_sub_type']))
			$this->subType = $objectResult['_source']['cue_point_sub_type'];

		if (isset($objectResult['_source']['cue_point_question']))
			$this->question = $objectResult['_source']['cue_point_question'];

		if (isset($objectResult['_source']['cue_point_answers']))
			$this->answers = $objectResult['_source']['cue_point_answers'];

		if (isset($objectResult['_source']['cue_point_hint']))
			$this->hint = $objectResult['_source']['cue_point_hint'];

		if (isset($objectResult['_source']['cue_point_explanation']))
			$this->explanation = $objectResult['_source']['cue_point_explanation'];

		if(isset($objectResult['highlight']))
			$this->setHighlight($objectResult['highlight']);

	}
}