<?php
class KalturaLogPartnerFilter implements Zend_Log_Filter_Interface
{
	/**
	 * @param int
	 */
	public $_partnerId;
	
	/**
	 * Filter by the current partner
	 *
	 * @param int $partnerId
	 */
    public function __construct($partnerId = -1)
    {
        $this->_partnerId = $partnerId;
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param  array    $event    event data
     * @return boolean            accepted?
     */
    public function accept($event)
    {
    	if (!isset($GLOBALS["partnerId"]))
    		return false;
    			
        return ($GLOBALS["partnerId"] === $this->_partnerId);
    }
}
?>