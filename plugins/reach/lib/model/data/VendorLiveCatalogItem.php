<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorLiveCatalogItem extends VendorCaptionsCatalogItem implements IVendorScheduledCatalogItem
{
	const CUSTOM_DATA_MINIMAL_REFUND_TIME = "minimal_refund_time";
	const CUSTOM_DATA_MINIMAL_ORDER_TIME = "minimal_order_time";
	const CUSTOM_DATA_DURATION_LIMIT = "duration_limit";

	public function setMinimalRefundTime($minimalRefundTime)
	{
		$this->putInCustomData(self::CUSTOM_DATA_MINIMAL_REFUND_TIME, $minimalRefundTime);
	}

	public function setMinimalOrderTime($minimalOrderTime)
	{
		$this->putInCustomData(self::CUSTOM_DATA_MINIMAL_ORDER_TIME, $minimalOrderTime);
	}

	public function setDurationLimit($durationLimit)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DURATION_LIMIT, $durationLimit);
	}

	public function getMinimalRefundTime()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MINIMAL_REFUND_TIME);
	}

	public function getMinimalOrderTime()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MINIMAL_ORDER_TIME);
	}

	public function getDurationLimit()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DURATION_LIMIT);
	}
}
