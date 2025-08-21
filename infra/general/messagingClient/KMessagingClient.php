<?php
/**
 * @package infra
 * @subpackage messagingClient
 */

class KMessagingClient
{
	const APP_REGISTRY_SERVICE = 'app-registry';
	const EMAIL_TEMPLATE_SERVICE = 'email-template';
	const MESSAGING_SERVICE = 'message';
	const EMAIL_PROVIDER_SERVICE = 'email-provider';

	const DEFAULT_APP_CUSTOM_ID = 'kaltura-server';
	const DEFAULT_APP_TYPE = 'test';
	const DEFAULT_PAGE_SIZE = 10;
	const DEFAULT_FROM_EMAIL = 'customer_service@kaltura.com';
	const DEFAULT_SENDER = 'no-reply@kaltura.com';


	/**
	 * @var string
	 */
	protected $endPointUrl;

	/**
	 * @var string
	 */
	protected $appRegistryEndPointUrl;

	/**
	 * @var string
	 */
	protected $appCustomId;

	/**
	 * @var string
	 */
	protected $appCustomName;

	/**
	 * @var string
	 */
	protected $appType;

	/**
	 * @var int
	 */
	protected $pageSize;

	/**
	 * @var string
	 */
	protected $fromEmail;

	/**
	 * @var string
	 */
	protected $defaultSender;

	/**
	 * @var string
	 */
	protected $ks;

	/**
	 * @param string $ks
	 * @throws Exception
	 */
	public function __construct($ks)
	{
		if (!$ks)
		{
			throw new Exception("KMessagingClient requires a valid KS");
		}
		$this->ks = $ks;
		$this->initClient();
	}

	protected function initClient()
	{
		$configuration = kConf::get('messaging_client', kConfMapNames::LOCAL_SETTINGS, array());
		KalturaLog::info("Loading messaging client configurations: " . print_r($configuration, true));

		$requiredClientConfig = array(
			'endPointUrl',
			'appRegistryEndPointUrl',
		);

		foreach ($requiredClientConfig as $configKey)
		{
			if (!isset($configuration[$configKey]))
			{
				throw new Exception("Messaging client configuration is missing [$configKey]");
			}
			$this->$configKey = $configuration[$configKey];
		}

		$this->appCustomId = $configuration['appCustomId'] ?? self::DEFAULT_APP_CUSTOM_ID;
		$this->appCustomName = $configuration['appCustomName'] ?? self::DEFAULT_APP_CUSTOM_ID;
		$this->appType = $configuration['appType'] ?? self::DEFAULT_APP_TYPE;
		$this->pageSize = $configuration['pageSize'] ?? self::DEFAULT_PAGE_SIZE;
		$this->fromEmail = $configuration['fromEmail'] ?? self::DEFAULT_FROM_EMAIL;
		$this->defaultSender = $configuration['defaultSender'] ?? self::DEFAULT_SENDER;
	}

    protected function curlServiceAction($service, $action, $data, $format = 1)
    {
        $endPoint = $this->getRequestEndpoint($service);

        $url = "$endPoint/$service/$action?format=$format";
        $headers = array(
            'Content-Type: application/json',
            'Authorization: KS ' . $this->ks,
        );

        KalturaLog::info("Sending request [$url], using KS [$this->ks], with body: " . print_r($data, true));

        $curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_POST, 1);
		$curlWrapper->setOpt(CURLOPT_RETURNTRANSFER, 1);
		$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $headers);
        $curlWrapper->setOpt(CURLOPT_POSTFIELDS, json_encode($data));
        $response = $curlWrapper->exec($url);
		if (!$response)
		{
			throw new Exception("Failed to execute curl to $url");
		}

		KalturaLog::info("Response: " . print_r($response, true));
        return json_decode($response, true);
    }

	protected function curlServiceListAction($service, $data)
	{
		if (!isset($data['pager']))
		{
			$data['pager'] = array('pageIndex' => 0, 'pageSize' => self::DEFAULT_PAGE_SIZE);
		}

		$listObjects = array();
		do
		{
			$data['pager']['pageIndex']++;
			$response = $this->curlServiceAction($service, 'list', $data);
		} while ($this->validateListResponseAndContinuation($listObjects, $response));

		KalturaLog::info('Retrieved [' . count($listObjects) . '] objects');
		return $listObjects;
	}

	protected function validateListResponseAndContinuation(&$objects, $listResponse)
	{
		if (!isset($listResponse['totalCount']) || !isset($listResponse['objects']) || !is_array($listResponse['objects']))
		{
			throw new Exception('Invalid list response');
		}

		foreach ($listResponse['objects'] as $object)
		{
			$objects[] = $object;
		}

		return (count($objects) < $listResponse['totalCount']) && (count($listResponse['objects']) == self::DEFAULT_PAGE_SIZE);
	}

    protected function getRequestEndpoint($service)
    {
        switch ($service)
		{
			case self::EMAIL_TEMPLATE_SERVICE:
            case self::EMAIL_PROVIDER_SERVICE:
            case self::MESSAGING_SERVICE:
                return $this->endPointUrl;

			case self::APP_REGISTRY_SERVICE:
                return $this->appRegistryEndPointUrl;

            default:
                throw new Exception("Unable to retrieve endpoint for unknown service [$service]");
        }
    }


	/**
	 * Public Messaging Client Functions
	 * ---------------------------------
	 */

	/**
	 * @param int $partnerId
	 * @return string
	 * @throws Exception
	 */
	public function addAppGuid($partnerId)
	{
		KalturaLog::info("Creating new appGuid for partner id [$partnerId]");
		$appRegistrationData = array(
			'appCustomId' => $this->appCustomId,
			'appCustomName' => $this->appCustomName,
			'appType' => $this->appType,
		);

		$appRegistration = $this->curlServiceAction(self::APP_REGISTRY_SERVICE, 'add', $appRegistrationData);
		if (!isset($appRegistration['id']))
		{
			throw new Exception("Failed to create new appGuid for partner ($partnerId)");
		}

		return $appRegistration['id'];
	}

	/**
	 * @param int $partnerId
	 * @param bool $registerIfNotExist
	 * @return string
	 * @throws Exception
	 */
	public function getAppGuid($partnerId, $registerIfNotExist = true)
	{
		$data = array(
			'partnerId' => $partnerId,
			'filter' => array(
				'appCustomIdIn' => array(self::DEFAULT_APP_CUSTOM_ID),
				'status' => 'enabled',
			),
			'pager' => array(
				'pageSize' => self::DEFAULT_PAGE_SIZE,
				'pageIndex' => 1,
			),
		);

		$appGuids = $this->curlServiceListAction(self::APP_REGISTRY_SERVICE, $data);
		if (isset($appGuids[0]['id']))
		{
			return $appGuids[0]['id'];
		}

		if ($registerIfNotExist)
		{
			return $this->addAppGuid($partnerId);
		}

		return '';
	}

	/**
	 * @param MessagingClientEmailTemplate $emailTemplate
	 * @return array
	 * @throws Exception
	 */
	public function addEmailTemplate(MessagingClientEmailTemplate $emailTemplate)
	{
		KalturaLog::info('Adding email template: ' . print_r($emailTemplate, true));
		$emailTemplateObject = $this->curlServiceAction(self::EMAIL_TEMPLATE_SERVICE, 'add', $emailTemplate);
		if (!isset($emailTemplateObject['id']))
		{
			throw new Exception("Unable to add new email template for partner id [$emailTemplate->partnerId]");
		}
		return $emailTemplateObject;
	}

	/**
	 * @param int $partnerId
	 * @param int $id
	 * @return array
	 */
	public function getEmailTemplate($partnerId, $id)
    {
        $data = array(
			'partnerId' => $partnerId,
			'filter' => array('idIn' => array($id)),
		);
        $emailTemplates = $this->curlServiceListAction(self::EMAIL_TEMPLATE_SERVICE, $data);
        return $emailTemplates ? $emailTemplates[0] : array();
    }

	/**
	 * @param int $id
	 * @param MessagingClientEmailTemplate $emailTemplate
	 * @return array
	 * @throws Exception
	 */
	public function updateEmailTemplate($id, MessagingClientEmailTemplate $emailTemplate)
    {
        $emailTemplate->id = $id;
        $updatedEmailTemplateObject = $this->curlServiceAction(self::EMAIL_TEMPLATE_SERVICE, 'update', $emailTemplate);
        if (!$updatedEmailTemplateObject)
		{
            throw new Exception("Unable to update email template [$id] for partner id [$emailTemplate->partnerId]");
        }
        return $updatedEmailTemplateObject;
    }

	/**
	 * @param MessagingClientEmailData $data
	 * @return string
	 * @throws Exception
	 */
	public function sendEmail(MessagingClientEmailData $data)
    {
        $emailObject = $this->curlServiceAction(self::MESSAGING_SERVICE, 'send', $data);
        if (!$emailObject || !isset($emailObject['bulkId']))
		{
            throw new Exception("Unable to send email for template id [$data->templateId], partner id [$data->partnerId]");
        }
        return $emailObject['bulkId'];
    }

	/**
	 * @param int $partnerId
	 * @param string $appGuid
	 * @return string
	 * @throws Exception
	 */
	public function getDefaultSender($partnerId, $appGuid)
    {
        if (!$appGuid)
		{
            throw new Exception("AppGuid is required for email template of partner id [$partnerId]");
        }

        $data = array(
            'partnerId' => $partnerId,
            'appGuid' => $appGuid,
        );

        $emailProvider = $this->curlServiceAction(self::EMAIL_PROVIDER_SERVICE, 'lookup', $data);
        if (!isset($emailProvider['defaultSender']) || $emailProvider['defaultSender'] == self::DEFAULT_SENDER)
		{
            return $this->fromEmail;
        }

        return $emailProvider['defaultSender'];
    }

	/**
	 * @param string $string
	 * @return array
	 */
	public static function getMessageParamsFromString($string)
	{
		$params = array();
		preg_match_all('/{([a-zA-Z0-9_]+)}/', $string, $matches);

		if (isset($matches[1]))
		{
			foreach ($matches[1] as $paramName)
			{
				$paramNameConverted = str_replace('_', '', $paramName);
				$params[$paramNameConverted] = array('type' => 'String');
			}
		}

		return $params;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public static function formatMessageParamsInString($string)
	{
		return preg_replace_callback('/{([a-zA-Z0-9_]+)}/',
			function($matches) { return '{' . str_replace('_', '', $matches[1]) . '}'; },
			$string);
	}
}
