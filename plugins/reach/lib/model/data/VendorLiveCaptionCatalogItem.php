<?php
/**
 * @package plugins.reach
 * @subpackage model
 */
class VendorLiveCaptionCatalogItem extends VendorCaptionsCatalogItem implements IVendorScheduledCatalogItem
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::LIVE_CAPTION);
	}

	const CUSTOM_DATA_MINIMAL_REFUND_TIME = "minimal_refund_time";
	const CUSTOM_DATA_MINIMAL_ORDER_TIME = "minimal_order_time";
	const CUSTOM_DATA_DURATION_LIMIT = "duration_limit";
	const CUSTOM_DATA_CONTENT_TRANFER_METHOD = "content_transfer_method";

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

	public function setContentTransferMethod($contentTransferMethod)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CONTENT_TRANFER_METHOD, $contentTransferMethod);
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

	public function getContentTransferMethod()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CONTENT_TRANFER_METHOD);
	}
}
