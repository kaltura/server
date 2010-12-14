<?php


/**
 * Skeleton subclass for representing a row from the 'annotation' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class Annotation extends BaseAnnotation {

	public function getChilds()
	{
		if ($this->isNew())
			return array();
			
		$c = new Criteria();
		$c->add(AnnotationPeer::PARENT_ID, $this->getId());
		$c->add(AnnotationPeer::STATUS, AnnotationStatus::ANNOTATION_STATUS_READY);
		return AnnotationPeer::doSelect($c);
	}
	
	public function getPuserId()
	{
		return PuserKuserPeer::getPuserIdFromKuserId($this->getPartnerId(), $this->getKuserId());
	} 
	
	public function setPuserId($v)
	{
		return $this->setKuserId(PuserKuserPeer::getKuserIdFromPuserId($this->getPartnerId(), $v));
	} 
	
	/*
	 * @param $status - Annotation::ANNOTATION_STATUS
	 * 
	 */
	public function setStatusToThisAndToAllChilds($status)
	{
		parent::setStatus($status);
		
		$childs = $this->getChilds();
		foreach($childs as $child)
		{
			$child->setStatus($status);
			$child->save();
		}
	}

	public function getUniqueAnnotationId()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ($i = 0; $i < 10; $i++)
		{
			$id = $dc["id"].'_'.kString::generateStringId();
			$existingObject = AnnotationPeer::retrieveByPK($id);
			if ($existingObject){
				KalturaLog::log(__METHOD__ . ": id [$id] already exists");
			}else{
				return $id;
			}
		}
		
		throw new Exception("Could not find unique id for annotation");
	}
	
	/**
	 * return true is annotationId is an offspring or itself
	 * @param string $offspringAnnotationId
	 */
	public function isOffspring($offspringAnnotationId = null)
	{
		if($this->id == $offspringAnnotationId)
				return true;
				
		$childs = $this->getChilds();
		foreach($childs as $child)
		{
			if ($child->isOffspring($offspringAnnotationId))
				return true;
		}
		
		return false;	
	}
	
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(AnnotationPeer::STATUS) && $this->getStatus() == AnnotationStatus::ANNOTATION_STATUS_DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
} // Annotation
