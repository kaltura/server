<?php
/**
 * @package plugins.searchHistory
 * @subpackage lib
 */
class kESearchHistoryManager implements kESearchSearchHistoryInfoEventConsumer
{

	const HISTORY_EXCHANGE_NAME = 'history_exchange';
	const HISTORY_QUEUE_NAME = 'history';
	const INPUT_KEY = 'input';
	const WEIGHT_KEY = 'weight';
	const INDEX_KEY = '_index';
	const TYPE_KEY = '_type';
	const ACTION_KEY = '_action';

	/**
	 * @param $object
	 * @return bool true if should continue to the next consumer
	 */
	public function consumeESearchSearchHistoryInfoEvent($object)
	{
		$body = $this->generateSearchHistoryDocument($object);
		$this->indexSearchHistoryDocument($object, $body);

		return true;
	}

	/**
	 * @param $object
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeESearchSearchHistoryInfoEvent($object)
	{
		if ($object instanceof ESearchSearchHistoryInfo)
		{
			return true;
		}

		return false;
	}

	protected function shouldIndexSearchHistoryObject(ESearchSearchHistoryInfo $object)
	{
		$collectObjects = kConf::get('search_history_collect_objects', 'elastic', array());
		if (!in_array($object->getSearchedObject(), $collectObjects))
		{
			return false;
		}
		if (!$object->getPartnerId() || !$object->getKUserId())
		{
			KalturaLog::log('Not indexing search history document, missing partner id or kuser id');
			return false;
		}
		return true;
	}

	protected function addIndexTypeAction(&$body)
	{
		$body[self::INDEX_KEY] = ESearchHistoryIndexMap::SEARCH_HISTORY_INDEX;
		$body[self::ACTION_KEY] =  ElasticMethodType::INDEX;
		$body[self::TYPE_KEY] = ESearchHistoryIndexMap::SEARCH_HISTORY_TYPE;
	}

	protected function generateSearchHistoryDocument(ESearchSearchHistoryInfo $eSearchSearchHistoryInfo)
	{
		$body = array(
			ESearchHistoryFieldName::PARTNER_ID => $eSearchSearchHistoryInfo->getPartnerId(),
			ESearchHistoryFieldName::SEARCHED_OBJECT => $eSearchSearchHistoryInfo->getSearchedObject(),
			//ESearchHistoryFieldName::SEARCH_TERMS is created using split filter in  logstash
			ESearchHistoryFieldName::SEARCH_TERM_COMPLETION => array(
				self::INPUT_KEY => $eSearchSearchHistoryInfo->getSearchTerms(),
				self::WEIGHT_KEY => searchHistoryUtils::getWeightFromTimestamp($eSearchSearchHistoryInfo->getTimestamp())
			),
			ESearchHistoryFieldName::KUSER_ID => $eSearchSearchHistoryInfo->getKUserId(),
			ESearchHistoryFieldName::PID_UID_CONTEXT_OBJECT => $eSearchSearchHistoryInfo->getPidUidContextObject(),
			ESearchHistoryFieldName::TIMESTAMP => $eSearchSearchHistoryInfo->getTimestamp(),
			ESearchHistoryFieldName::SEARCH_CONTEXT => $eSearchSearchHistoryInfo->getSearchContextArray()
		);

		return $body;
	}

	protected function indexSearchHistoryDocument($object, $body)
	{
		if (!$this->shouldIndexSearchHistoryDocument($object))
		{
			return;
		}
		$this->addIndexTypeAction($body);
		$document = json_encode($body);
		$this->indexDocumentWithRabbit($document);
	}

	protected function shouldIndexSearchHistoryDocument($object)
	{
		$searchHistoryConfig = kConf::get('search_history', 'elastic', array());
		$disableHistoryIndexing = isset($searchHistoryConfig['disableHistoryIndexing']) ? $searchHistoryConfig['disableHistoryIndexing'] : false;
		if ($disableHistoryIndexing)
		{
			return false;
		}
		//validate essential params
		if (!$object->getKUserId() || !$object->getPartnerId())
		{
			return false;
		}
		return true;
	}

	protected function indexDocumentWithRabbit($document)
	{
		try
		{
			$constructorArgs['exchangeName'] = self::HISTORY_EXCHANGE_NAME;
			$queueProvider = QueueProvider::getInstance(null, $constructorArgs);
			$queueProvider->send(self::HISTORY_QUEUE_NAME, $document);
		}
		catch (Exception $e)
		{
			//don't fail the search request, just log
			KalturaLog::err("cannot connect to rabbit");
		}
	}

}
