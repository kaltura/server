<?php


/**
 * @package sip
 * @subpackage model
 * Class SipServerNode
 */
class SipServerNode extends ServerNode {

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(SipPlugin::getCoreValue('serverNodeType',SipServerNodeType::SIP_SERVER));
	}


} // SipServerNode
