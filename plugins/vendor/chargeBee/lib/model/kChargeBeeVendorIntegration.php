<?php
/**
 * @package plugins.chargeBee
 * @subpackage chargeBee.model
 */

class kChargeBeeVendorIntegration extends VendorIntegration
{
	const INVOICE_ID = 'invoiceId';
	const PLAN_ID = 'planId';
	const IS_PAYMENT_FAILED = 'isPaymentFailed';
	
	public function setInvoiceId ($v)	{ $this->putInCustomData ( self::INVOICE_ID, $v);}
	public function getInvoiceId ( )	{ return $this->getFromCustomData(self::INVOICE_ID);}

	public function setPlanId ($v)	{ $this->putInCustomData ( self::PLAN_ID, $v);}
	public function getPlanId ( )	{ return $this->getFromCustomData(self::PLAN_ID);}

	public function setIsPaymentFailed ($v)	{ $this->putInCustomData ( self::IS_PAYMENT_FAILED, $v);}
	public function getIsPaymentFailed ( )	{ return $this->getFromCustomData(self::IS_PAYMENT_FAILED);}
}