<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kResponseProfileHolder implements IResponseProfileHolder
{
	/**
	 * @var int
	 */
	private $id;
	
	/* (non-PHPdoc)
	 * @see IResponseProfileHolder::getId()
	 */
	public function getId()
	{
		return $this->id;
	}

	/* (non-PHPdoc)
	 * @see IResponseProfileHolder::setId()
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	/* (non-PHPdoc)
	 * @see IResponseProfileHolder::get()
	 */
	public function get()
	{
		return ResponseProfilePeer::retrieveByPK($this->id);		
	}
}