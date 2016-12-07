<?php

class kPlaybackContextDataParams
{

	/**
	 * @var array
	 */
	private $flavors;

	/**
	 * @var DeliveryProfile
	 */
	private $deliveryProfile;

	/**
	 * @return array
	 */
	public function getFlavors()
	{
		return $this->flavors;
	}

	/**
	 * @param array $flavors
	 */
	public function setFlavors($flavors)
	{
		$this->flavors = $flavors;
	}

	/**
	 * @return DeliveryProfile
	 */
	public function getDeliveryProfile()
	{
		return $this->deliveryProfile;
	}

	/**
	 * @param DeliveryProfile $deliverProfile
	 */
	public function setDeliveryProfile($deliveryProfile)
	{
		$this->deliveryProfile = $deliveryProfile;
	}

}