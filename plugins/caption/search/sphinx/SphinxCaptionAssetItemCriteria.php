<?php
/**
 * @package plugins.captionSphinx
 * @subpackage DB
 */
class SphinxCaptionAssetItemCriteria extends SphinxCriteria implements ICaptionAssetItemCriteria 
{
	/**
	 * @var array<CaptionAssetItem>
	 */
	protected $captionAssetItems = array();
	
	public static $sphinxFields = array(
		assetPeer::ID => 'entry_distribution_id',
		assetPeer::ENTRY_ID => 'entry_id',
		assetPeer::ID => 'caption_asset_id',
		assetPeer::TAGS => 'tags',
		assetPeer::CONTENT => 'content',
		assetPeer::PARTNER_DESCRIPTION => 'partner_description',
		assetPeer::LANGUAGE => 'language',
		assetPeer::LABEL => 'label',
		assetPeer::CONTAINER_FORMAT => 'format',
		
		assetPeer::INT_ID => 'int_caption_asset_id',
		assetPeer::FLAVOR_PARAMS_ID => 'caption_params_id',
		assetPeer::PARTNER_ID => 'partner_id',
		assetPeer::VERSION => 'version',
		assetPeer::STATUS => 'caption_asset_status',
		assetPeer::SIZE => 'size',
		assetPeer::IS_DEFAULT => 'is_default',
		assetPeer::START_TIME => 'start_time',
		assetPeer::END_TIME => 'end_time',
		
		assetPeer::CREATED_AT => 'created_at',
		assetPeer::UPDATED_AT => 'updated_at',
		assetPeer::DELETED_AT => 'deleted_at',
	);
	
	public static $sphinxOrderFields = array(
		assetPeer::SIZE => 'size',
		assetPeer::START_TIME => 'start_time',
		assetPeer::END_TIME => 'end_time',
		
		assetPeer::CREATED_AT => 'created_at',
		assetPeer::UPDATED_AT => 'updated_at',
		assetPeer::DELETED_AT => 'deleted_at',
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
		return kSphinxSearchManager::getSphinxIndexName(CaptionSphinxPlugin::INDEX_NAME);
	}

	/* (non-PHPdoc)
	 * @see ICaptionAssetItemCriteria::getCaptionAssetItems()
	 */
	public function getCaptionAssetItems()
	{
		$this->applyFilters();
		return $this->captionAssetItems;
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::executeSphinx()
	 */
	protected function executeSphinx($index, $wheres, $orderBy, $limit, $maxMatches, $setLimit)
	{
		$fields = array(
			'str_entry_id',
			'str_caption_asset_id',
			'int_caption_asset_id',
			'start_time',
			'end_time',
			'str_content',
		);
		$fields = implode(',', $fields);
		
		$sql = "SELECT $fields FROM $index $wheres $orderBy LIMIT $limit OPTION max_matches=$maxMatches";
		
		//debug query
		//echo $sql."\n"; die;
		$pdo = DbManager::getSphinxConnection();
		$stmt = $pdo->query($sql);
		if(!$stmt)
		{
			KalturaLog::err("Invalid sphinx query [$sql]");
			return;
		}
		
		$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
		KalturaLog::log("Found " . count($items) . " items");
		
		$entryPKs = array();
		$assetPKs = array();
		foreach($items as $item)
		{
			$entryPKs[] = $item['entry_id'];
			$assetPKs[] = $item['int_caption_asset_id'];
		}
		
		// Load all entry and asset objects to the instance pool
		entryPeer::retrieveByPKs($entryPKs);
		assetPeer::retrieveByPKs($assetPKs);
		
		$this->captionAssetItems = array();
		foreach($items as $item)
		{
			$asset = assetPeer::retrieveByPK($item['int_caption_asset_id']);
			$this->captionAssetItems[] = new CaptionAssetItem($asset, $item['start_time'], $item['end_time'], $item['str_content']);
		}
		
		$this->recordsCount = 0;
		if(!$this->doCount || !$setLimit)
			return;
			
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