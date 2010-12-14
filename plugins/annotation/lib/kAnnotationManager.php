<?php
class kAnnotationManager implements kObjectDeletedEventConsumer
{
	/**
	 * @param BaseObject $object
	 */
	public function objectDeleted(BaseObject $object) 
	{
		KalturaLog::debug("annotation objectDeleted");
		if($object instanceof entry)
			$this->entryDeleted($object->getId());
					
		if($object instanceof Annotation)
			$this->annotationDeleted($object);
			
		return true;
	}
	
	/**
	 * @param Annotation $annotation
	 */
	protected function annotationDeleted(Annotation $annotation) 
	{
		$childs = $annotation->getChilds();
		foreach($childs as $child)
		{
			$child->setStatus($annotation->getStatus());
			$child->save();
		}
	}
	
	/**
	 * @param int $entryId
	 */
	protected function entryDeleted($entryId) 
	{
		//TODO - list all related to entryid and delete
		
	}

}