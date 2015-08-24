<?php

/**
 * @package api
 * @subpackage filters
 */
class DynamicObjectSearchFilter extends AdvancedSearchFilterOperator
{
	/**
	 * @var
	 */
	protected $field;

	/**
	 * @var int
	 */
	protected $metadataProfileId;

	/**
	 * @return int
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}

	/**
	 * @param int $metadataProfileId
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}

	/**
	 * @return mixed
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @param mixed $field
	 */
	public function setField($field)
	{
		$this->field = $field;
	}

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterOperator::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query, $xPaths = null)
	{
		if (!($query instanceof IKalturaIndexQuery))
			return;
		$this->parentQuery = $query;

		if ($this->parentQuery instanceof MetadataSearchFilter || $this->parentQuery instanceof DynamicObjectSearchFilter)
			$this->metadataProfileId = $this->parentQuery->getMetadataProfileId();

		if (!$this->metadataProfileId)
		{
			KalturaLog::err('Metadata profile id was not found on parent query, or parent query is not instance of MetadataSearchFilter/DynamicObjectSearchFilter');
			return;
		}

		$field = $this->getField();
		if (!isset($xPaths[$field]))
		{
			$this->addCondition('1 <> 1');
			KalturaLog::ERR("Missing field: $field in xpath array: " . print_r($xPaths, true));
			return;
		}

		/** @var MetadataProfileField $metadataProfileField */
		$metadataProfileField = $xPaths[$field];
		if ($metadataProfileField->getType() !== MetadataSearchFilter::KMC_FIELD_TYPE_METADATA_OBJECT)
		{
			KalturaLog::ERR("Field $field is not set as a dynamic object type");
			return;
		}

		$pluginName = MetadataPlugin::PLUGIN_NAME;
		$fieldId = $metadataProfileField->getId();
		$relatedMetadataProfileId = $metadataProfileField->getRelatedMetadataProfileId();
		$innerXPaths = $this->loadFields($relatedMetadataProfileId);
		$prefix = "{$pluginName}_{$fieldId}";
		$suffix = kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}";

		$dataConditions = array();
		foreach($this->items as $item)
		{
			if ($item instanceof DynamicObjectSearchFilter)
			{
				$item->applyCondition($this, $innerXPaths);
				$dataConditions = $item->matchClause;
			}
			elseif ($item instanceof AdvancedSearchFilterCondition)
			{
				$innerField = $item->getField();
				if (!isset($innerXPaths[$innerField]))
				{
					$this->addCondition('1 <> 1');
					KalturaLog::ERR("Missing field: $innerField in inner xpath array: " . print_r($innerXPaths, true));
					continue;
				}
				$innerValue = SphinxUtils::escapeString($item->getValue());
				$innerFieldType = $innerXPaths[$innerField]->getType();
				$innerFieldId = $innerXPaths[$innerField]->getId();
				$innerPrefix = $pluginName .'_'. $innerFieldId;
				$innerSuffix = kMetadataManager::SEARCH_TEXT_SUFFIX . '_' . $innerFieldId;

				$dataCondition = "\"$innerPrefix $innerValue $innerSuffix\"";

				KalturaLog::debug("Inner condition: $dataCondition");
				$dataConditions[] = $dataCondition;
			}
		}

		if (count($dataConditions))
		{
			foreach($dataConditions as &$dataCondition)
			{
				$dataCondition = "( $prefix ( $dataCondition ) $suffix )";
				KalturaLog::debug("Wrapped condition: $dataCondition");
			}

			$glue = ($this->type == MetadataSearchFilter::SEARCH_AND ? ' ' : ' | ');
			$dataConditions = array_unique($dataConditions);
			$value = implode($glue, $dataConditions);
			KalturaLog::debug("Current $value");
			$this->addMatch($value);
		}
	}

	protected function loadFields($metadataProfileId)
	{
		$xPaths = array();
		$profileFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($metadataProfileId);
		foreach($profileFields as $profileField)
		{
			/** @var MetadataProfileField $profileField */
			$xPaths[$profileField->getXpath()] = $profileField;
		}
		return $xPaths;
	}

	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);

		$xmlElement->addAttribute('field', $this->field);
		// $this->metadataProfileId should always be retrieved dynamically
	}

	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);

		$attr = $xmlElement->attributes();
		if(isset($attr['field']))
			$this->field = (string)$attr['field'];
	}
}
