<?php
/**
 * @package plugins.captionSphinx
 * @subpackage DB
 */
class SphinxCaptionAssetItemCriteria extends SphinxCriteria 
{
	public static $sphinxFields = array(
		CaptionAssetItemPeer::ENTRY_ID => 'entry_id',
		CaptionAssetItemPeer::ID => 'caption_asset_id',
		CaptionAssetItemPeer::TAGS => 'tags',
		CaptionAssetItemPeer::CONTENT => 'content',
		CaptionAssetItemPeer::PARTNER_DESCRIPTION => 'partner_description',
		CaptionAssetItemPeer::LANGUAGE => 'language',
		CaptionAssetItemPeer::LABEL => 'label',
		CaptionAssetItemPeer::CONTAINER_FORMAT => 'format',
		
		CaptionAssetItemPeer::INT_ID => 'int_caption_asset_id',
		CaptionAssetItemPeer::FLAVOR_PARAMS_ID => 'caption_params_id',
		CaptionAssetItemPeer::PARTNER_ID => 'partner_id',
		CaptionAssetItemPeer::VERSION => 'version',
		CaptionAssetItemPeer::STATUS => 'caption_asset_status',
		CaptionAssetItemPeer::SIZE => 'size',
		CaptionAssetItemPeer::IS_DEFAULT => 'is_default',
		CaptionAssetItemPeer::START_TIME => 'start_time',
		CaptionAssetItemPeer::END_TIME => 'end_time',
		
		CaptionAssetItemPeer::CREATED_AT => 'created_at',
		CaptionAssetItemPeer::UPDATED_AT => 'updated_at',
	);
	
	public static $sphinxOrderFields = array(
		CaptionAssetItemPeer::SIZE => 'size',
		CaptionAssetItemPeer::START_TIME => 'start_time',
		CaptionAssetItemPeer::END_TIME => 'end_time',
		
		CaptionAssetItemPeer::CREATED_AT => 'created_at',
		CaptionAssetItemPeer::UPDATED_AT => 'updated_at',
	);
	
	/**
	 * @return criteriaFilter
	 */
	protected function getDefaultCriteriaFilter()
	{
		return assetPeer::getCriteriaFilter();
	}
	
	public function getSphinxOrderFields()
	{
		return self::$sphinxOrderFields;
	}
	
	/**
	 * @return string
	 */
	protected function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(CaptionSearchPlugin::INDEX_NAME);
	}

	/* (non-PHPdoc)
	 * @see SphinxCriteria::executeSphinx()
	 */
	protected function executeSphinx($index, $wheres, $orderBy, $limit, $maxMatches, $setLimit)
	{
		$sql = "SELECT int_id FROM $index $wheres $orderBy LIMIT $limit OPTION max_matches=$maxMatches";
	
		//debug query
		//echo $sql."\n"; die;
		$pdo = DbManager::getSphinxConnection();
		$stmt = $pdo->query($sql);
		if(!$stmt)
		{
			KalturaLog::err("Invalid sphinx query [$sql]");
			return;
		}
		
		$ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 2);
		$ids = $this->applyIds($ids);
		$this->setFetchedIds($ids);
		KalturaLog::log("Found " . count($ids) . " ids");
		
		foreach($this->keyToRemove as $key)
		{
			KalturaLog::log("Removing key [$key] from criteria");
			$this->remove($key);
		}
		
		$this->addAnd(CaptionAssetItemPeer::ID, $ids, Criteria::IN);
		
		$this->recordsCount = 0;
		
		if(!$this->doCount)
			return;
			
		if($setLimit)
		{
			$this->setOffset(0);
			
			$sql = "show meta";
			$stmt = $pdo->query($sql);
			$meta = $stmt->fetchAll(PDO::FETCH_NAMED);
			if(count($meta))
			{
				foreach($meta as $metaItem)
				{
					KalturaLog::log("Sphinx query " . $metaItem['Variable_name'] . ': ' . $metaItem['Value']);
					if($metaItem['Variable_name'] == 'total_found')
						$this->recordsCount = (int)$metaItem['Value'];
				}
			}
		}
		else
		{
			$c = clone $this;
			$c->setLimit(null);
			$c->setOffset(null);
			$this->recordsCount = CaptionAssetItemPeer::doCount($c);
		}
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#getNewCriterion()
	 */
	public function getNewCriterion($column, $value, $comparison = null)
	{
		return new SphinxCriterion('SphinxCaptionAssetItemCriteria', $this, $column, $value, $comparison);
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#add()
	 */
	public function add($p1, $value = null, $comparison = null)
	{
		if ($p1 instanceof Criterion)
		{
			$oc = $this->getCriterion($p1->getColumn());
			if(!is_null($oc) && $oc->getValue() == $p1->getValue() && $oc->getComparison() != $p1->getComparison())
				return $this;
				
			return parent::add($p1);
		}
		
		$nc = new SphinxCriterion('SphinxCaptionAssetItemCriteria', $this, $p1, $value, $comparison);
		return parent::add($nc);
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#addAnd()
	 */
	public function addAnd($p1, $p2 = null, $p3 = null)
	{
		if (is_null($p3)) 
			return parent::addAnd($p1, $p2, $p3);
			
		// addAnd(column, value, comparison)
		$nc = new SphinxCriterion('SphinxCaptionAssetItemCriteria', $this, $p1, $p2, $p3);
		$oc = $this->getCriterion($p1);
		
		if ( !is_null($oc) )
		{
			// no need to add again
			if($oc->getValue() != $p2 || $oc->getComparison() != $p3)
				$oc->addAnd($nc);
				
			return $this;
		}
			
		return $this->add($nc);
	}
	
	public function hasSphinxFieldName($fieldName)
	{
		return isset(self::$sphinxFields[$fieldName]);
	}
	
	public function getSphinxFieldName($fieldName)
	{
		if(!isset(self::$sphinxFields[$fieldName]))
			return $fieldName;
			
		return self::$sphinxFields[$fieldName];
	}
	
	public function getSphinxFieldType($fieldName)
	{
		$sphinxTypes = CaptionSphinxPlugin::getSphinxSchemaFields();
		if(!isset($sphinxTypes[$fieldName]))
			return null;
			
		return kSphinxSearchManager::getSphinxDataType($sphinxTypes[$fieldName]);
	}
	
	public function hasMatchableField($fieldName)
	{
		return in_array($fieldName, array(
			"entry_id", 
			"caption_asset_id", 
			"tags", 
			"content", 
			"partner_description",
			"language",
			"label",
			"format",
		));
	}
}