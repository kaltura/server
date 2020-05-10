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
		$value = array_search($v->getId(), $originalOrder);
		if ($value === false)
		{
			$value = PHP_INT_MAX;
		}
		$v->userOrder = $value;
	}

	public function compare(DeliveryProfile $a,DeliveryProfile $b)
	{
		//use the partner custom defined order
		return $a->userOrder - $b->userOrder;
	}
}
