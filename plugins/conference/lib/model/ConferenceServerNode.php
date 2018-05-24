<?php

class ConferenceServerNode extends ServerNode {
	const CUSTOM_DATA_SERVICE_URL = 'service_url';

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ConferencePlugin::getCoreValue('serverNodeType',ConferenceServerNodeType::CONFERENCE_SERVER));
	}


	/* (non-PHPdoc)
	 * @see BaseEntryServerNode::postUpdate()
 	*/
	public function postUpdate(PropelPDO $con = null)
	{
		parent::postUpdate();
		if($this->isColumnModified(ServerNodePeer::STATUS) && $this->getStatus() === ServerNodeStatus::NOT_REGISTERED)
		{
			$this->removeAttachedEntryServerNodes();
		}
	}

	public function getServiceBaseUrl()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SERVICE_URL, null, '');
	}

	public function setServiceBaseUrl($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SERVICE_URL, $v);
	}

	public function removeAttachedEntryServerNodes()
	{
		$confEntryServerNodes = EntryServerNodePeer::retrieveByServerNodeIdAndType($this->getId(), ConferencePlugin::getCoreValue('serverNodeType', ConferenceServerNodeType::CONFERENCE_SERVER));
		foreach ($confEntryServerNodes as $confEntryServerNode)
		{
			$confEntryServerNode->delete();
		}
	}


} // ConferenceServerNode
