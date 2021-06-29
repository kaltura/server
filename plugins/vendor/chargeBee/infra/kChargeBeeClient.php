<?php

/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */
class kChargeBeeClient
{
	/** API */
	const API_CREATE_SUBSCRIPTION = '/v2/subscriptions';
	const API_RETRIEVE_SUBSCRIPTION = '/v2/subscriptions/@subscriptionId@';
	const API_UPDATE_FREE_TRIAL = '/v2/promotional_credits/add';
	const API_CREATE_INVOICE = '/v2/invoices/@invoiceId@/add_addon_charge';
	const API_UPDATE_INVOICE = '/v2/estimates/create_invoice';
	const API_CLOSE_INVOICE = '/v2/invoices/@invoiceId@/close';
	
	
	protected $chargeBeeBaseURL;
	protected $siteApiKey;
	
	
	/**
	 * kZoomClient constructor.
	 * @param $chargeBeeBaseURL
	 * @param $siteApiKey
	 * @throws KalturaAPIException
	 */
	public function __construct($chargeBeeBaseURL, $siteApiKey)
	{
		$this->chargeBeeBaseURL = $chargeBeeBaseURL;
		$this->siteApiKey = $siteApiKey;
	}

	public function createSubscription($planId, $autoCollection, $customerFirstName, $customerLastName, $customerEmail,
								   $billingFirstName, $billingLastName, $line1, $city, $state, $zip, $country)
	{
		$apiPath = self::API_CREATE_SUBSCRIPTION;
		$apiPath .= '?plan_id=' . $planId . '&auto_collection=' . $autoCollection . '&customer[first_name]=' . $customerFirstName . '&customer[last_name]=' . $customerLastName
			. '&customer[email]=' . $customerEmail . '&billing_address[first_name]=' . $billingFirstName . '&billing_address[last_name]=' . $billingLastName . '&billing_address[line1]=' . $line1
			. '&billing_address[city]=' . $city . '&billing_address[state]=' . $state. '&billing_address[zip]=' . $zip. '&billing_address[country]=' . $country;
		$options = array(CURLOPT_CUSTOMREQUEST => 'POST');
		return $this->callChargeBee($apiPath, $options);
	}
	
	public function retrieveSubscription($subscriptionId)
	{
		$apiPath = str_replace('@subscriptionId@', $subscriptionId, self::API_RETRIEVE_SUBSCRIPTION);
		return $this->callChargeBee($apiPath);
	}
	
	public function updateFreeTrial($customerId, $amount, $description)
	{
		$apiPath = self::API_UPDATE_FREE_TRIAL;
		$apiPath .= '?customer_id=' . $customerId . '&amount=' . $amount . '&description=' . $description;
		$options = array(CURLOPT_CUSTOMREQUEST => 'POST');
		return $this->callChargeBee($apiPath, $options);
	}
	
	public function createInvoice($invoiceId, $addonId, $addonQuantity)
	{
		$apiPath = str_replace('@invoiceId@', $invoiceId, self::API_CREATE_INVOICE);
		$apiPath .= '?addon_id=' . $addonId . '&addon_quantity=' . $addonQuantity;
		$options = array(CURLOPT_CUSTOMREQUEST => 'POST');
		return $this->callChargeBee($apiPath, $options);
	}
	
	
	public function updateInvoice($invoiceId, $chargesAmount, $chargesDescription)
	{
		$apiPath = self::API_UPDATE_INVOICE;
		$apiPath .= '?invoice[customer_id]=' . $invoiceId . '&charges[amount][0]=' . $chargesAmount . '&charges[description][0]=' . $chargesDescription;
		$options = array(CURLOPT_CUSTOMREQUEST => 'POST');
		return $this->callChargeBee($apiPath, $options);
	}
	
	
	public function closeInvoice($invoiceId)
	{
		$apiPath = str_replace('@invoiceId@', $invoiceId, self::API_CLOSE_INVOICE);
		$options = array(CURLOPT_CUSTOMREQUEST => 'POST');
		return $this->callChargeBee($apiPath, $options);
	}
	
	/**
	 * @param $response
	 * @param int $httpCode
	 * @param KCurlWrapper $curlWrapper
	 * @param $apiPath
	 */
	protected function handleCurlResponse(&$response, $httpCode, $curlWrapper)
	{
		if (!$response || KCurlHeaderResponse::isError($httpCode) || $curlWrapper->getError())
		{
			if (!$curlWrapper->getError())
			{
				$errMsg = json_decode($response, true)['message'];
			}
			else
			{
				$errMsg = $curlWrapper->getError();
			}
			$errMsg = "Charge Bee curl returned error, Error code : $httpCode, Error: {$errMsg} ";
			KalturaLog ::debug($errMsg);
		}
	}
	
	/**
	 * @param string $apiPath
	 * @return mixed
	 * @throws Exception
	 */
	public function callChargeBee(string $apiPath, array $options = array())
	{
		KalturaLog::info('Calling Charge Bee API: ' . $apiPath);
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpts($options);
		
		$url = $this->generateContextualUrl($apiPath);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER , array(
			"Authorization: Basic {$this->siteApiKey}:",
			"Content-Type: Application/Json"
		));
		$response = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getHttpCode();
		$this->handleCurlResponse($response, $httpCode, $curlWrapper);
		if (!$response)
		{
			$data = $curlWrapper->getErrorMsg();
		}
		else
		{
			$data = json_decode($response, true);
		}
		return $data;
	}
	
	protected function generateContextualUrl($apiPath)
	{
		$url = $this->chargeBeeBaseURL . $apiPath;

		return $url;
	}
}