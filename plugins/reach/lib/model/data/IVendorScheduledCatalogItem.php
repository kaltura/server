<?php


interface IVendorScheduledCatalogItem
{
	/**
	 * @return int
	 */
	public function getMinimalOrderTime();

	/**
	 * @return int
	 */
	public function getMinimalRefundTime();

	/**
	 * @return int
	 */
	public function getDurationLimit();


}