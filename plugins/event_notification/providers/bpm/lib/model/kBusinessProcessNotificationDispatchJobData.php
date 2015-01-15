<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model.data
 */
class kBusinessProcessNotificationDispatchJobData extends kEventNotificationDispatchJobData
{
	/**
	 * Define the business-process server id
	 * 
	 * @var int
	 */
	private $serverId;
	
	/**
	 * Related object peer class name
	 * 
	 * @var string
	 */
	private $peer;
	
	/**
	 * Related object primarty key
	 * 
	 * @var string
	 */
	private $primaryKey;
	
	/**
	 * Id of the process execution
	 * 
	 * @var string
	 */
	private $caseId;

	/**
	 * @return BaseObject
	 */
	public function getObject()
	{
		$retrieveByPK = array($this->peer, 'retrieveByPK');
		if(is_callable($retrieveByPK))
		{
			return call_user_func($retrieveByPK, $this->primaryKey);
		}
		return null;
	}

	/**
	 * @param BaseObject $object
	 */
	public function setObject(BaseObject $object)
	{
		$this->peer = get_class($object->getPeer());
		$this->primaryKey = $object->getPrimaryKey();
	}

	/**
	 * @return the $caseId
	 */
	public function getCaseId()
	{
		if($this->caseId)
			return $this->caseId;

		$template = EventNotificationTemplatePeer::retrieveByPK($this->getTemplateId());
		$object = $this->getObject();
		if($template && $object)
			return $template->getCaseId($object);
	}

	/**
	 * @param string $caseId
	 */
	public function setCaseId($caseId)
	{
		$this->caseId = $caseId;
	}

	/**
	 * @return BusinessProcessServer
	 */
	public function getServer()
	{
		return BusinessProcessServerPeer::retrieveByPK($this->serverId);
	}

	/**
	 * @return int
	 */
	public function getServerId()
	{
		return $this->serverId;
	}

	/**
	 * @param int $serverId
	 */
	public function setServerId($serverId)
	{
		$this->serverId = $serverId;
	}

	/**
	 * @param BusinessProcessServer $server
	 */
	public function setServer(BusinessProcessServer $server)
	{
		$this->serverId = $server->getId();
	}
}