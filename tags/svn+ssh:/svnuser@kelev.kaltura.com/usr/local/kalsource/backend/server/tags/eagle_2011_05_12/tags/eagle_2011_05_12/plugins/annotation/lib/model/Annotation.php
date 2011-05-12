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
 * @package plugins.annotation
 * @subpackage model
 */
class Annotation extends BaseAnnotation {

	public function getChildren()
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
		$kuser =  KuserPeer::retrieveByPK($this->getKuserId());
		return $kuser->getPuserId();
	} 
	
	public function setPuserId($v)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $v);
		return $this->setKuserId($kuser->getId());
	} 
	
	/**
	 * generate unique string id for annotation
	 */
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
	 * return true is annotationId is an descendant  or itself
	 * @param string $descendantAnnotationId
	 */
	public function isDescendant($descendantAnnotationId = null)
	{
		if($this->id == $descendantAnnotationId)
				return true;
				
		$children = $this->getChildren();
		foreach($children as $child)
		{
			if ($children->isDescendant($descendantAnnotationId))
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
