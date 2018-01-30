<?php

class ConferenceServerNode extends ServerNode {

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(KonferencePlugin::getConferenceCoreValue(ConferenceServerNodeType::CONFERENCE_SERVER));
	}

} // ConferenceServerNode
