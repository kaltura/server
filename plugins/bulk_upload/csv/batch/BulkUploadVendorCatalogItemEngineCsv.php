<?php
/**
 * Class which parses the bulk upload CSV and creates the objects listed in it.
 * This engine class parses CSVs which describe vendor catalog item.
 *
 * @package plugins.bulkUploadCsv
 * @subpackage batch
 */
class BulkUploadVendorCatalogItemEngineCsv extends BulkUploadEngineCsv
{
	const OBJECT_TYPE_TITLE = 'vendor catalog item';
	const MANDATORY_COLUMN_MISSING = 'Mandatory Column missing from CSV';
	const ENUM_VALUE_NOT_FOUND = 'Enum value not found ';
	const PRICE_VALUES_MISSING = 'Cannot add/update only one of the values: pricePerUnit/priceFunction';
	const EXCEEDED_MAX_RECORDS = 'Exceeded max records count per bulk';
	const NA = 'N\A';
	const PRICING_PER_UNIT = 'pricing:pricePerUnit';
	const PRICING_FUNCTION = 'pricing:priceFunction';
	const UTF = 'UTF-8';



	/**
	 * (non-PHPdoc)
	 * @see BulkUploadGeneralEngineCsv::createUploadResult()
	 */
	protected function createUploadResult($values, $columns)
	{
		$bulkUploadResult = parent::createUploadResult($values, $columns);
		if (!$bulkUploadResult)
		{
			return;
		}

		$bulkUploadResult->bulkUploadResultObjectType = KalturaBulkUploadObjectType::VENDOR_CATALOG_ITEM;
		array_walk($values, array('BulkUploadVendorCatalogItemEngineCsv', 'trimArray'));
		$this->setResultValues($columns, $values, $bulkUploadResult);
		if($bulkUploadResult->status == KalturaBulkUploadResultStatus::ERROR)
		{
			$this->addBulkUploadResult($bulkUploadResult);
			return;
		}

		$bulkUploadResult->status = KalturaBulkUploadResultStatus::IN_PROGRESS;
		$bulkUploadResult->objectStatus = KalturaVendorCatalogItemStatus::ACTIVE;

		if (!$bulkUploadResult->action)
		{
			$bulkUploadResult->action = KalturaBulkUploadAction::ADD;
		}

		$bulkUploadResult = $this->validateBulkUploadResult($bulkUploadResult);
		if($bulkUploadResult)
		{
			$this->bulkUploadResults[] = $bulkUploadResult;
		}
	}

	protected function setResultValues($columns, $values, &$bulkUploadResult)
	{
		$shouldConvertValueToEnum = array('serviceFeature', 'serviceType', 'turnAroundTime', 'outputFormat');
		$pricing = null;

		foreach($columns as $index => $column)
		{
			if(!is_numeric($index) || $values[$index] === self::NA || $values[$index] === '')
			{
				continue;
			}
			if (in_array($column, $shouldConvertValueToEnum) && isset($values[$index]))
			{
				$this->handleEnumColumns($values[$index], $column, $bulkUploadResult);
			}
			else if(($column === self::PRICING_PER_UNIT || $column === self::PRICING_FUNCTION) && isset($values[$index]))
			{
				self::handlePriceColumns($pricing, $bulkUploadResult, $column, $values[$index]);
			}
			else if ($column === 'id' && isset($values[$index]))
			{
				$bulkUploadResult->vendorCatalogItemId = $values[$index];
				KalturaLog::info("Set value vendorCatalogItemId [{$bulkUploadResult->vendorCatalogItemId}]");
			}
			else if(iconv_strlen($values[$index], self::UTF))
			{
				$bulkUploadResult->$column = $values[$index];
				KalturaLog::info("Set value $column [{$bulkUploadResult->$column}]");
			}
			else
			{
				KalturaLog::info("Value $column is empty");
			}
		}
	}

	protected static function handlePriceColumns(&$pricing, $bulkUploadResult, $column, $value)
	{
		if (!$pricing)
		{
			$pricing = new KalturaVendorCatalogItemPricing();
			$bulkUploadResult->pricing = $pricing;
		}

		$columnName = substr($column,8); //removing 'pricing:'
		$bulkUploadPricing = $bulkUploadResult->pricing;
		$bulkUploadPricing->$columnName = $value;
		KalturaLog::info("Set value $column [{$bulkUploadPricing->$columnName}]");
	}

	protected function handleEnumColumns($value, $column, $bulkUploadResult)
	{
		switch($column)
		{
			case 'serviceFeature':
				$enumValue = self::getEnumValue('KalturaVendorServiceFeature', $value);
				break;

			case 'serviceType':
				$enumValue = self::getEnumValue('KalturaVendorServiceType', $value);
				break;

			case 'turnAroundTime':
				$enumValue = self::getEnumValue('KalturaVendorServiceTurnAroundTime', $value);
				break;

			case 'outputFormat':
				$enumValue = self::getEnumValue('KalturaVendorCatalogItemOutputFormat', $value);
				break;

			default:
				$enumValue = null;
		}
		if ($enumValue === null || $enumValue === '')
		{
			$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::ENUM_VALUE_NOT_FOUND . $column . ':' . $value);
		}
		else
		{
			$bulkUploadResult->$column = $enumValue;
			KalturaLog::info("Set value $column [{$bulkUploadResult->$column}]");
		}
	}

	protected function validateBulkUploadResult(KalturaBulkUploadResultVendorCatalogItem $bulkUploadResult)
	{
		$this->validateBulkUploadResultByAction($bulkUploadResult);

		if($this->maxRecords && $this->lineNumber > $this->maxRecords) // check max records
		{
			$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::EXCEEDED_MAX_RECORDS);
		}

		if($bulkUploadResult->status == KalturaBulkUploadResultStatus::ERROR)
		{
			$this->addBulkUploadResult($bulkUploadResult);
			return null;
		}
		return $bulkUploadResult;
	}

	protected function validateBulkUploadResultByAction($bulkUploadResult)
	{
		if ($bulkUploadResult->action == KalturaBulkUploadAction::ADD || $bulkUploadResult->action == KalturaBulkUploadAction::UPDATE)
		{
			if (!$bulkUploadResult->serviceFeature)
			{
				return $this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::MANDATORY_COLUMN_MISSING .' :serviceFeature');
			}
			if ( (isset($bulkUploadResult->pricing->pricePerUnit) && !isset($bulkUploadResult->pricing->priceFunction)) ||
				(!isset($bulkUploadResult->pricing->pricePerUnit) && isset($bulkUploadResult->pricing->priceFunction)) )
			{
				return $this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::PRICE_VALUES_MISSING);
			}
		}

		switch ($bulkUploadResult->action)
		{
			case KalturaBulkUploadAction::ADD:
				if (!$bulkUploadResult->vendorPartnerId && !$bulkUploadResult->serviceType &&
					!$bulkUploadResult->turnAroundTime && !$bulkUploadResult->pricing)
				{
					$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::MANDATORY_COLUMN_MISSING .' :vendorPartnerId, serviceType, turnAroundTime, pricing');
				}
				self::validateResultsByServiceFeature($bulkUploadResult);
				break;

			case KalturaBulkUploadAction::UPDATE:
				if (!$bulkUploadResult->vendorPartnerId)
				{
					$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::MANDATORY_COLUMN_MISSING . ' : vendorPartnerId');
				}
				break;

			default:
				break;
		}
	}

	protected function validateResultsByServiceFeature(KalturaBulkUploadResultVendorCatalogItem $bulkUploadResult)
	{
		switch ($bulkUploadResult->serviceFeature)
		{
			case VendorServiceFeature::CAPTIONS:
			case VendorServiceFeature::ALIGNMENT:
			case VendorServiceFeature::CHAPTERING:
				if (!$bulkUploadResult->sourceLanguage)
				{
					$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::MANDATORY_COLUMN_MISSING .' : sourceLanguage');
				}
				break;
			case VendorServiceFeature::TRANSLATION:
				if (!$bulkUploadResult->targetLanguage)
				{
					$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::MANDATORY_COLUMN_MISSING .' : targetLanguage');
				}
				break;

			case VendorServiceFeature::AUDIO_DESCRIPTION:
				if (!$bulkUploadResult->sourceLanguage || !$bulkUploadResult->flavorParamsId || !$bulkUploadResult->clearAudioFlavorParamsId)
				{
					$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::APP, self::MANDATORY_COLUMN_MISSING.': sourceLanguage, flavorParamsId, clearAudioFlavorParamsId');
				}
				break;

			default:
				break;
		}
	}

	/**
	 *
	 * Create the vendor catalog items from the given bulk upload results
	 */
	protected function createObjects()
	{
		KBatchBase::$kClient->startMultiRequest();

		KalturaLog::info("job[{$this->job->id}] start handling vendor catalog items");
		$bulkUploadResultChunk = array();

		foreach($this->bulkUploadResults as $bulkUploadResult)
		{
			/* @var $bulkUploadResult KalturaBulkUploadResultVendorCatalogItem */
			switch ($bulkUploadResult->action)
			{
				case KalturaBulkUploadAction::ADD:
					$bulkUploadResultChunk[] = $bulkUploadResult;
					$vendorCatalogItem = $this->createVendorCatalogItemFromResult($bulkUploadResult);
					KBatchBase::$kClient->vendorCatalogItem->add($vendorCatalogItem);
					break;

				case KalturaBulkUploadAction::UPDATE:
					$bulkUploadResultChunk[] = $bulkUploadResult;
					$vendorCatalogItem = $this->createVendorCatalogItemFromResult($bulkUploadResult);
					KBatchBase::$kClient->vendorCatalogItem->update($bulkUploadResult->vendorCatalogItemId, $vendorCatalogItem);
					break;

				case KalturaBulkUploadAction::UPDATE_STATUS:
					$bulkUploadResultChunk[] = $bulkUploadResult;
					KBatchBase::$kClient->vendorCatalogItem->updateStatus($bulkUploadResult->vendorCatalogItemId, KalturaVendorCatalogItemStatus::DEPRECATED);
					break;

				default:
					$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
					$bulkUploadResult->errorDescription = 'Unknown action passed: ['.$bulkUploadResult->action .']';
					break;
			}

			if(KBatchBase::$kClient->getMultiRequestQueueSize() >= $this->multiRequestSize)
			{
				$requestResults = KBatchBase::$kClient->doMultiRequest();
				$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
				$this->checkAborted();
				KBatchBase::$kClient->startMultiRequest();
				$bulkUploadResultChunk = array();
			}
		}

		$requestResults = KBatchBase::$kClient->doMultiRequest();

		if(count($requestResults))
		{
			$this->updateObjectsResults($requestResults, $bulkUploadResultChunk);
		}

		KalturaLog::info("job[{$this->job->id}] finish handling vendor catalog items");
	}

	protected function updateObjectsResults(array $requestResults, array $bulkUploadResults)
	{
		KalturaLog::info('Updating ' . count($requestResults) . ' results');
		$multiRequestResults = array();
		foreach($requestResults as $index => $requestResult)
		{
			$bulkUploadResult = $bulkUploadResults[$index];
			$this->handleMultiRequest($multiRequestResults);
			if(is_array($requestResult) && isset($requestResult['code']))
			{
				$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::KALTURA_API, $requestResult['message']);
				$bulkUploadResult->objectStatus = $requestResult['code'];
			}
			else if($requestResult instanceof Exception)
			{
				$this->handleResultError($bulkUploadResult, KalturaBatchJobErrorTypes::KALTURA_API, $requestResult->getMessage());
			}

			$this->addBulkUploadResult($bulkUploadResult);
		}
		$this->handleMultiRequest($multiRequestResults,true);
	}

	protected function handleMultiRequest(&$ret, $finish = false)
	{
		$count = KBatchBase::$kClient->getMultiRequestQueueSize();

		if($count)
		{
			if (($count % $this->multiRequestSize) == 0 || $finish)
			{
				$result = KBatchBase::$kClient->doMultiRequest();
				if (count($result))
				{
					$ret = array_merge($ret, $result);
				}
				if (!$finish)
				{
					KBatchBase::$kClient->startMultiRequest();
				}
			}
		}
		elseif (!$finish)
		{
			KBatchBase::$kClient->startMultiRequest();
		}
	}

	/**
	 * Function to create a new vendor catalog item from bulk upload result.
	 * @param KalturaBulkUploadResultVendorCatalogItem $bulkUploadResult
	 */
	protected function createVendorCatalogItemFromResult (KalturaBulkUploadResultVendorCatalogItem $bulkUploadResult)
	{
		$bulkUploadResultParams = array('vendorPartnerId', 'name', 'systemName', 'serviceType', 'turnAroundTime',
			'sourceLanguage', 'targetLanguage', 'outputFormat', 'enableSpeakerId', 'fixedPriceAddons',
			'pricing', 'flavorParamsId', 'clearAudioFlavorParamsId', 'allowResubmission');

		$kalturaVendorCatalogItem = self::getObjectByServiceFeature($bulkUploadResult->serviceFeature);

		foreach ($bulkUploadResultParams as $param)
		{
			if (isset($bulkUploadResult->$param))
			{
				$kalturaVendorCatalogItem->$param = $bulkUploadResult->$param;
			}
		}
		return $kalturaVendorCatalogItem;
	}

	protected static function getObjectByServiceFeature($serviceFeature)
	{
		$object = null;
		switch ($serviceFeature)
		{
			case VendorServiceFeature::CAPTIONS:
				$object = new KalturaVendorCaptionsCatalogItem();
				break;

			case VendorServiceFeature::TRANSLATION:
				$object = new KalturaVendorTranslationCatalogItem();
				break;

			case VendorServiceFeature::ALIGNMENT:
				$object = new KalturaVendorAlignmentCatalogItem();
				break;

			case VendorServiceFeature::AUDIO_DESCRIPTION:
				$object = new KalturaVendorAudioDescriptionCatalogItem();
				break;

			case VendorServiceFeature::CHAPTERING:
				$object = new KalturaVendorChapteringCatalogItem();
				break;

			default:
				$object = new KalturaVendorCaptionsCatalogItem();
				break;
		}
		return $object;
	}

	/**
	 *
	 * Gets the columns for V1 csv file
	 */
	protected function getColumns()
	{
		return array(
			'action',
			'id',
			'vendorPartnerId',
			'name',
			'systemName',
			'serviceFeature',
			'serviceType',
			'turnAroundTime',
			'sourceLanguage',
			'targetLanguage',
			'outputFormat',
			'enableSpeakerId',
			'fixedPriceAddons',
			'pricing:pricePerUnit',
			'pricing:priceFunction',
			'flavorParamsId',
			'clearAudioFlavorParamsId',
			'allowResubmission',
		);
	}

	protected function getUploadResultInstance ()
	{
		return new KalturaBulkUploadResultVendorCatalogItem();
	}

	protected function getUploadResultInstanceType()
	{
		return KalturaBulkUploadObjectType::VENDOR_CATALOG_ITEM;
	}

	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}

	static function getEnumValue($peer, $value)
	{
		$reflectionClass = new ReflectionClass($peer);
		$allConsts = $reflectionClass->getConstants();
		foreach($allConsts as $key => $enumVal)
		{
			if ($value === $key)
			{
				return $enumVal;
			}
		}
		return '';
	}
}