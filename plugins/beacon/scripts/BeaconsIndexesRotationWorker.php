<?php
/**
 * @package plugins.beacon
 * @subpackage scripts
 */
class BeaconsIndexesRotationWorker extends ElasticIndexRotationWorker
{
	const INDEX_NAME_DATE_DELTA = '-1 month';

	/**
	 * @param $currentSearchingIndices array
	 * @param $aliasesToRemove array
	 * @param $aliasesToAdd array
	 */
	protected function handleCurrentSearchIndices($currentSearchingIndices, &$aliasesToRemove, &$aliasesToAdd)
	{
		$count = 0;
		foreach ($currentSearchingIndices as $index)
		{
			$count++;
			if ($count >= $this->maxNumberOfIndices) //remove old search aliases and old indexes we assume maxNumberOfIndices > 1
			{
				$aliasesToRemove[] = new ElasticIndexAlias($index, $this->indexPattern . kBeacon::ELASTIC_INDEX_OLD_POSTFIX . ($count - 1));
				$aliasesToRemove[] = new ElasticIndexAlias($index, $this->searchAlias);
			}
			else //Add old indices allies
			{
				$aliasesToAdd[]  = new ElasticIndexAlias($index, $this->indexPattern . kBeacon::ELASTIC_INDEX_OLD_POSTFIX . $count);
			}
		}
	}

	protected function getIndexesToCreate()
	{
		$indexesName = array();
		$date = new DateTime();
		for ($i = 1; $i <= $this->maxNumberOfIndices; $i++)
		{
			$yearMonth = $date->format($this->indexDateFormat);
			array_unshift($indexesName, $this->indexPattern . '-' . $yearMonth);
			$date->modify(self::INDEX_NAME_DATE_DELTA);
		}

		return $indexesName;
	}

	protected function createIndexFromScratch()
	{
		KalturaLog::info('Creating new index from scratch mode');
		$indexesToCreate = $this->getIndexesToCreate();
		$aliasesToRemove = array();
		$aliasesToAdd = array();
		foreach($indexesToCreate as $newIndex)
		{
			if ($this->dryRun)
			{
				KalturaLog::debug("Dry run - creating index $newIndex");
			}
			else
			{
				$this->createNewIndex($newIndex);
			}

			$aliasesToAdd[] = new ElasticIndexAlias($newIndex, $this->searchAlias);
		}

		$this->handleCurrentSearchIndices($indexesToCreate, $aliasesToRemove, $aliasesToAdd);
		$aliasesToRemove = array();
		$newIndex = end($indexesToCreate);
		$aliasesToAdd[] = new ElasticIndexAlias($newIndex, $this->indexAlias);
		$this->changeAliases($aliasesToAdd, $aliasesToRemove);
	}
}
