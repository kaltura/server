<?php

interface IRelatedObjectPeer
{
	/**
	 * Return all direct parents
	 * @param IBaseObject $object
	 * @return array<IBaseObject>
	 */
	public function getParentObjects(IBaseObject $object);
	
	/**
	 * Return all root parents
	 * @param IBaseObject $object
	 * @return array<IBaseObject>
	 */
	public function getRootObjects(IBaseObject $object);
	
	/**
	 * Indicates that the parent object is pointing to the child and the current object is not pointing to its parent
	 * @return boolean
	 */
	public function isReferenced(IBaseObject $object);
}
