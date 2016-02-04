<?php

class kObjectReadyForIndexInheritedTreeHandler implements kObjectReadyForIndexInheritedTreeEventConsumer
{
	
	/* (non-PHPdoc)
	 * @see kObjectReadyForIndexInheritedTreeEventConsumer::shouldConsumeReadyForIndexInheritedTreeEvent()
	 */
	public function shouldConsumeReadyForIndexInheritedTreeEvent(BaseObject $object)
	{
		if ($object instanceof category)
		{
			return true;
		}

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectReadyForIndexInheritedTreeEventConsumer::objectReadyForIndexInheritedTreeEvent()
	 */
	public function objectReadyForIndexInheritedTreeEvent(BaseObject $object, BatchJob $raisedJob = null)
	{
		if ( $object instanceof category )
		{
			$object->addIndexCategoryInheritedTreeJob();
			$object->indexToSearchIndex();
		}
	}

}