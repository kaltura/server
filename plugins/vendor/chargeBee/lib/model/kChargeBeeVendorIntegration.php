<?php
/**
 * @package plugins.chargeBee
 * @subpackage chargeBee.model
 */

class kChargeBeeVendorIntegration extends VendorIntegration
{
	const LAST_ACCESS_TO_CHARGE_BEE = 'lastAccessToChargeBee';
	const HANDLED_EVENT_IDS = 'handledEventIds';
	const PLAN_ID = 'planId';

	public function setLastAccessToChargeBee ($v)	{ $this->putInCustomData ( self::LAST_ACCESS_TO_CHARGE_BEE, $v);}
	public function getLastAccessToChargeBee ( )	{ return $this->getFromCustomData(self::LAST_ACCESS_TO_CHARGE_BEE);}

	public function setHandledEventIds ($v)	{ $this->putInCustomData ( self::HANDLED_EVENT_IDS, $v);}
	public function getHandledEventIds ( )	{ return $this->getFromCustomData(self::HANDLED_EVENT_IDS);}

	public function setPlanId ($v)	{ $this->putInCustomData ( self::PLAN_ID, $v);}
	public function getPlanId ( )	{ return $this->getFromCustomData(self::PLAN_ID);}

}