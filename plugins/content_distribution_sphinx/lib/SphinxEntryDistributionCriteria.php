<?php

class SphinxEntryDistributionCriteria extends SphinxCriteria
{
	public static $sphinxFields = array(
		EntryDistributionPeer::ID => 'entry_distribution_id',
		EntryDistributionPeer::CREATED_AT => 'created_at',
		EntryDistributionPeer::UPDATED_AT => 'updated_at',
		EntryDistributionPeer::SUBMITTED_AT => 'submitted_at',
		EntryDistributionPeer::ENTRY_ID => 'entry_id',
		EntryDistributionPeer::PARTNER_ID => 'partner_id',
		EntryDistributionPeer::DISTRIBUTION_PROFILE_ID => 'distribution_profile_id',
		EntryDistributionPeer::STATUS => 'entry_distribution_status',
		EntryDistributionPeer::DIRTY_STATUS => 'dirty_status',
		EntryDistributionPeer::THUMB_ASSET_IDS => 'thumb_asset_ids',
		EntryDistributionPeer::FLAVOR_ASSET_IDS => 'flavor_asset_ids',
		EntryDistributionPeer::SUNRISE => 'sunrise',
		EntryDistributionPeer::SUNSET => 'sunset',
		EntryDistributionPeer::REMOTE_ID => 'remote_id',
		EntryDistributionPeer::PLAYS => 'plays',
		EntryDistributionPeer::VIEWS => 'views',
		EntryDistributionPeer::ERROR_TYPE => 'error_type',
		EntryDistributionPeer::ERROR_NUMBER => 'error_number',
		EntryDistributionPeer::LAST_REPORT => 'last_report',
		EntryDistributionPeer::NEXT_REPORT => 'next_report',
	);
	
	public static $sphinxOrderFields = array(
		EntryDistributionPeer::CREATED_AT => 'created_at',
		EntryDistributionPeer::UPDATED_AT => 'updated_at',
		EntryDistributionPeer::SUBMITTED_AT => 'submitted_at',
		EntryDistributionPeer::SUNRISE => 'sunrise',
		EntryDistributionPeer::SUNSET => 'sunset',
		EntryDistributionPeer::PLAYS => 'plays',
		EntryDistributionPeer::VIEWS => 'views',
		EntryDistributionPeer::LAST_REPORT => 'last_report',
		EntryDistributionPeer::NEXT_REPORT => 'next_report',
	);

	/**
	 * @return criteriaFilter
	 */
	protected function getDefaultCriteriaFilter()
	{
		return EntryDistributionPeer::getCriteriaFilter();
	}
	
	/**
	 * @return string
	 */
	protected function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(EntryDistributionPeer::TABLE_NAME);;
	}
	
	/* (non-PHPdoc)
	 * @see SphinxCriteria::executeSphinx()
	 */
	protected function executeSphinx($index, $wheres, $orderBy, $limit, $maxMatches, $setLimit)
	{
		$sql = "SELECT entry_distribution_id FROM $index $wheres $orderBy LIMIT $limit OPTION max_matches=$maxMatches";
		
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
		KalturaLog::debug("Found " . count($ids) . " ids");
		
		foreach($this->keyToRemove as $key)
		{
			KalturaLog::debug("Removing key [$key] from criteria");
			$this->remove($key);
		}
		
		$this->addAnd(EntryDistributionPeer::ID, $ids, Criteria::IN);
		
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
					KalturaLog::debug("Sphinx query " . $metaItem['Variable_name'] . ': ' . $metaItem['Value']);
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
			$this->recordsCount = EntryDistributionPeer::doCount($c);
		}
	}

	/* (non-PHPdoc)
	 * @see propel/util/Criteria#getNewCriterion()
	 */
	public function getNewCriterion($column, $value, $comparison = null)
	{
		return new SphinxCriterion('SphinxEntryDistributionCriteria', $this, $column, $value, $comparison);
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
		
		$nc = new SphinxCriterion('SphinxEntryDistributionCriteria', $this, $p1, $value, $comparison);
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
		$nc = new SphinxCriterion('SphinxEntryDistributionCriteria', $this, $p1, $p2, $p3);
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
	
	public static function hasSphinxFieldName($fieldName)
	{
		return isset(self::$sphinxFields[$fieldName]);
	}
	
	public static function getSphinxFieldName($fieldName)
	{
		if(!isset(self::$sphinxFields[$fieldName]))
			return $fieldName;
			
		return self::$sphinxFields[$fieldName];
	}
	
	public static function getSphinxFieldType($fieldName)
	{
		if(!isset(self::$sphinxTypes[$fieldName]))
			return null;
			
		return self::$sphinxTypes[$fieldName];
	}
	
	public static function hasMatchableField($fieldName)
	{
		return in_array($fieldName, array("thumb_asset_ids", "flavor_asset_ids"));
	}
}