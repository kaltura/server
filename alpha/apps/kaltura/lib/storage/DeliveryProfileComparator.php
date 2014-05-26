<?php 

class DeliveryProfileComparator
{
	private $cdnhost;
	private $isSecured;

	function __construct( $isSecured , $cdnhost = null) {
		$this->cdnhost = $cdnhost;
		$this->isSecured = $isSecured;
	}
	
	public static function decorateWithUserOrder(DeliveryProfile $v, $k)
	{
		$v->userOrder = $k;
	}

	public function compare(DeliveryProfile $a,DeliveryProfile $b) {
		
		// Primary order - cdnHost
		if (($this->cdnhost) && ($a->getHostName() != $b->getHostName())) {
			if($a->getHostName() == $this->cdnhost) 
				return -1;
			if($b->getHostName() == $this->cdnhost)
				return 1;
		}
			
		// secondary order in case of secured entry - Is secured
		if ($a->getTokenizer() != $b->getTokenizer()) {
			if(($this->isSecured && $a->getTokenizer()) || (!$this->isSecured && !$a->getTokenizer())) 
					return -1;
			if(($this->isSecured && $b->getTokenizer()) || (!$this->isSecured && !$b->getTokenizer()))
					return 1;
		}
		
		return $a->userOrder - $b->userOrder;
	}
}