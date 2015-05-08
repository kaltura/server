<?php
/**
 * @package plugins.quiz
 */
class kQuizManager implements kObjectAddedEventConsumer, kObjectChangedEventConsumer
{

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof AnswerCuePoint)
			return true;

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof AnswerCuePoint)
			return true;

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if( $object instanceof AnswerCuePoint
			&& in_array(CuePointPeer::CUSTOM_DATA, $modifiedColumns)
			&& $object->isCustomDataModified(AnswerCuePoint::CUSTOM_DATA_ANSWER_KEY) )
		{
			$object->setIsCorrect( in_array( $object->getAnswerKey(), $object->getCorrectAnswerKeys() ) );
			$object->save();
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof AnswerCuePoint) {
			$dbParentCuePoint = CuePointPeer::retrieveByPK($object->getParentId());
			$correctKeys =  $dbParentCuePoint->getCorrectAnswerKeys();
			$object->setCorrectAnswerKeys( $correctKeys );
			$object->setExplanation( $dbParentCuePoint->getExplanation() );
			$object->setIsCorrect( in_array( $object->getAnswerKey(), $correctKeys ) );
			$object->save();
		}

		return true;
	}

}