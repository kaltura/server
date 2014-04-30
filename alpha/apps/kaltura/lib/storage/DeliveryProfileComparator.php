<?php 

class DeliveryProfileCdnHostComparator
{
	private $cdnhost;
	private $isSecured;

	function __construct( $isSecured , $cdnhost = null) {
		$this->cdnhost = $cdnhost;
		$this->isSecured = $isSecured;
	}
	
	function decorateWithUserOrder(DeliveryProfile &$v, $k)
	{
		$v->userOrder = $k;
	}

	function compare(DeliveryProfile $a,DeliveryProfile $b) {
		
		// Primary order - cdnHost
		if($this->cdnhost) {
			if($a->getHostName() == $this->cdnhost) {
				if($b->getHostName() == $this->cdnhost) {
					return $a->userOrder - $b->userOrder;
				} else {
					return -1;
				}
			}
				
			if($b->getHostName() == $this->cdnhost)
				return 1;
		}
		
		// secondary order in case of secured entry - Is secured
		if($this->isSecured) {
			if($a->getTokenizer()) {
				if($b->getTokenizer()) {
					return $a->userOrder - $b->userOrder;
				} else {
					return -1;
				}
			}
			if($b->getTokenizer())
				return 1;
		}
		
		return $a->userOrder - $b->userOrder;
	}
}