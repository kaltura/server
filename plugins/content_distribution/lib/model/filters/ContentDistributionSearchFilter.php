<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.filters.advanced
 */
class ContentDistributionSearchFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected $condition = null;
	
	/**
	 * @var bool
	 */
	protected $noDistributionProfiles;
	
	/**
	 * @var int
	 */
	protected $distributionProfileId;
	
	/**
	 * enum from EntryDistributionSunStatus
	 * @var int
	 */
	protected $distributionSunStatus;
	
	/**
	 * enum from EntryDistributionFlag
	 * @var int
	 */
	protected $entryDistributionFlag;
	
	/**
	 * enum from KalturaEntryDistributionStatus
	 * @var int
	 */
	protected $entryDistributionStatus;
	
	/**
	 * @var bool
	 */
	protected $hasEntryDistributionValidationErrors;
	
	/**
	 * @var array
	 */
	protected $entryDistributionValidationErrors;
	
	/**
	 * @return the $noDistributionProfiles
	 */
	public function getNoDistributionProfiles()
	{
		return $this->noDistributionProfiles;
	}

	/**
	 * @return the $hasEntryDistributionValidationErrors
	 */
	public function getHasEntryDistributionValidationErrors()
	{
		return $this->hasEntryDistributionValidationErrors;
	}

	/**
	 * @return the $entryDistributionValidationErrors
	 */
	public function getEntryDistributionValidationErrors()
	{
		return $this->entryDistributionValidationErrors;
	}

	/**
	 * @param bool $noDistributionProfiles
	 */
	public function setNoDistributionProfiles($noDistributionProfiles)
	{
		$this->noDistributionProfiles = $noDistributionProfiles;
	}

	/**
	 * @param bool $hasEntryDistributionValidationErrors
	 */
	public function setHasEntryDistributionValidationErrors($hasEntryDistributionValidationErrors)
	{
		$this->hasEntryDistributionValidationErrors = $hasEntryDistributionValidationErrors;
	}

	/**
	 * @param array $entryDistributionValidationErrors
	 */
	public function setEntryDistributionValidationErrors(array $entryDistributionValidationErrors)
	{
		$this->entryDistributionValidationErrors = $entryDistributionValidationErrors;
	}

	public function setDistributionProfileId($distributionProfileId)
	{
		$this->distributionProfileId = $distributionProfileId;
	}
	
	public function setDistributionSunStatus($distributionSunStatus)
	{
		$this->distributionSunStatus = $distributionSunStatus;
	}
	
	public function setEntryDistributionFlag($entryDistributionFlag)
	{
		$this->entryDistributionFlag = $entryDistributionFlag;
	}
	
	public function setEntryDistributionStatus($entryDistributionStatus)
	{
		$this->entryDistributionStatus = $entryDistributionStatus;
	}
	
	public function getDistributionProfileId()
	{
		return $this->distributionProfileId;
	}
	
	public function getDistributionSunStatus()
	{
		return $this->distributionSunStatus;
	}
	
	public function getEntryDistributionFlag()
	{
		return $this->entryDistributionFlag;
	}
	
	public function getEntryDistributionStatus()
	{
		return $this->entryDistributionStatus;
	}
	
	public function getCondition()
	{
		if($this->condition)
			return $this->condition;
			
		$conditions = array();
		
		if(!is_null($this->noDistributionProfiles))
		{
			if($this->noDistributionProfiles)
				return kContentDistributionManager::getSearchStringNoDistributionProfiles();
		}
		
		if(!is_null($this->distributionProfileId))
			$conditions[] = '"' . kContentDistributionManager::getSearchStringDistributionProfile($this->distributionProfileId) . '"';
		else 
			$conditions[] = '"' . kContentDistributionManager::getSearchStringDistributionProfile() . '"';;
			
		
		if(!is_null($this->distributionSunStatus))
			$conditions[] = '"' . kContentDistributionManager::getSearchStringDistributionSunStatus($this->distributionSunStatus, $this->distributionProfileId, false) . '"';
		
		if(!is_null($this->entryDistributionFlag))
			$conditions[] = '"' . kContentDistributionManager::getSearchStringDistributionFlag($this->entryDistributionFlag, $this->distributionProfileId, false) . '"';
		
		if(!is_null($this->entryDistributionStatus))
			$conditions[] = '"' . kContentDistributionManager::getSearchStringDistributionStatus($this->entryDistributionStatus, $this->distributionProfileId, false) . '"';
			
		if(!is_null($this->hasEntryDistributionValidationErrors))
		{
			if($this->hasEntryDistributionValidationErrors)
				$conditions[] = '"' . kContentDistributionManager::getSearchStringDistributionHasValidationError($this->distributionProfileId, false) . '"';
			else
				$conditions[] = kContentDistributionManager::getSearchStringDistributionHasNoValidationError($this->distributionProfileId);
		}

		if(!is_null($this->entryDistributionValidationErrors))
			foreach($this->entryDistributionValidationErrors as $validationError)
				$conditions[] = '"' . kContentDistributionManager::getSearchStringDistributionValidationError($validationError, $this->distributionProfileId, false) . '"';
			
		if(!count($conditions))
			return null;
			
		$this->condition = implode(' ', $conditions);
		return $this->condition;
	}
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if ($query instanceof IKalturaIndexQuery){
			$condition = $this->getCondition();
			$key = '@' . ContentDistributionSphinxPlugin::getSphinxFieldName(ContentDistributionPlugin::SPHINX_EXPANDER_FIELD_DATA);
			$query->addMatch("($key $condition)");
		}
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('distributionProfileId', $this->distributionProfileId);
		$xmlElement->addAttribute('entryDistributionFlag', $this->entryDistributionFlag);
		$xmlElement->addAttribute('entryDistributionStatus', $this->entryDistributionStatus);
		$xmlElement->addAttribute('distributionSunStatus', $this->distributionSunStatus);
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
		$attr = $xmlElement->attributes();
		if(isset($attr['distributionProfileId']) && strlen($attr['distributionProfileId']))
			$this->distributionProfileId = (int) $attr['distributionProfileId'];
		if(isset($attr['entryDistributionFlag']) && strlen($attr['entryDistributionFlag']))
			$this->entryDistributionFlag = (int) $attr['entryDistributionFlag'];
		if(isset($attr['entryDistributionStatus']) && strlen($attr['entryDistributionStatus']))
			$this->entryDistributionStatus = (int) $attr['entryDistributionStatus'];
		if(isset($attr['distributionSunStatus']) && strlen($attr['distributionSunStatus']))
			$this->distributionSunStatus = (int) $attr['distributionSunStatus'];
	}
}
