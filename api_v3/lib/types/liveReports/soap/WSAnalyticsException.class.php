<?php


class WSAnalyticsException extends WSBaseObject
{	
	/** 
	 * Currently, this object doesn't have a Kaltura API representation
	 * and it is used only to enable parsing the exception if recieved from the client
	 */
	function getKalturaObject() {
		return null;
	}
				
	/**
	 * @var string
	 **/
	public $message;
	
}


