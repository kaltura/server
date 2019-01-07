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
	 * @var string
	 */
	private $type;

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
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
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