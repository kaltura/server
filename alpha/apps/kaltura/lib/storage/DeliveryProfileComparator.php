<?php 

class DeliveryProfileComparator
{
	private $cdnhost;
	private $isSecured;

	function __construct( $isSecured , $cdnhost = null) {
		$this->cdnhost = $cdnhost;
		$this->isSecured = $isSecured;
	}
	
	public static function decorateWithUserOrder(DeliveryProfile $v, $k , $originalOrder)
	{
		$v->userOrder = array_search($v->getId(), $originalOrder);
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
		$tokenA = is_null($a->getTokenizer());
		$tokenB = is_null($b->getTokenizer());
		
		if ($tokenA != $tokenB) {
			if(($this->isSecured && $tokenA) || (!$this->isSecured && !$tokenA)) 
					return -1;
			if(($this->isSecured && $tokenB) || (!$this->isSecured && !$tokenB))
					return 1;
		}
		
		return $a->userOrder - $b->userOrder;
	}
}