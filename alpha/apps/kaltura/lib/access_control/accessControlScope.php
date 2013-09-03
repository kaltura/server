<?php
/**
 * @package Core
 * @subpackage model.data
 */
class accessControlScope extends kScope
{
	/**
	 * Key-value pairs of hashes  passed to the access control as part of the scope
	 * @var array
	 */
	protected $hashes;
	
	public function __construct()
	{
		parent::__construct();
		$this->setContexts(array(ContextType::PLAY));
	}
	
	/**
	 * @return the $hashes
	 */
	public function getHashes() {
		return $this->hashes;
	}

	/**
	 * @param array $hashes
	 */
	public function setHashes($hashes) {
		$this->hashes = $hashes;
	}
}