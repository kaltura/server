<?php
/**
 * @package infra
 * @subpackage log
 */
class KalturaLogFilterType implements Zend_Log_Filter_Interface
{
	/**
	 * @param string
	 */
	public $_type;
	
	/**
	 * Filter by the current log writer type
	 *
	 * @param string $type
	 */
    public function __construct($type = '')
    {
        $this->_type = $type;
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param  array    $event    event data
     * @return boolean            accepted?
     */
    public function accept($event)
    {
    	if (!isset($event['type']) || $event['type'] == null){
    		 if ($this->_type == KalturaLog::LOG_TYPE_KALTURA_API_V3)
    		 	return true;
    		 
    		 return false;
    	} 
    	    	
    	if ($event['type'] == $this->_type)
    		return true;
    			
        return false;
    }
}
