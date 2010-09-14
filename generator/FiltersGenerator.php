<?php
class FiltersGenerator extends ClientGeneratorFromPhp 
{
	private $_txt = "";
	
	protected function writeHeader()
	{
		
	}
	
	protected function writeFooter()
	{
		
	}
	
	protected function writeBeforeServices()
	{
		
	}
	
	protected function writeBeforeService(KalturaServiceReflector $serviceReflector)
	{
		
	}
	
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector)
	{
		
	}
	
	protected function writeAfterService(KalturaServiceReflector $serviceReflector)
	{
		
	}
	
	protected function writeAfterServices()
	{
		
	}
	
	protected function writeBeforeTypes()
	{
		
	}

	protected function writeType(KalturaTypeReflector $type)
	{
		if ($type->isFilterable())
		{
			$this->writeFilterForType($type);
			$this->writeOrderByEnumForType($type);
		}
	}
	
	private function writeFilterForType(KalturaTypeReflector $type)
	{
		$map = KAutoloader::getClassMap();
		if(!isset($map[$type->getType()]))
			return;
		
		$filterClassName = $type->getType() . "Filter";
		
		$filterPath = dirname($map[$type->getType()]) . "/filters/$filterClassName.php";
		$this->_txt = "";
			
		
		$parentType = $type;
		while(1)
		{
			$parentType = $parentType->getParentTypeReflector();
			if ($parentType === null || $parentType->isFilterable())
				break;			
		}
		
		$partnetClassName = ($parentType ? $parentType->getType() . "Filter" : "KalturaFilter");
		
		$this->appendLine("<?php");
		$this->appendLine("/**");
		$this->appendLine(" * @package api");
		$this->appendLine(" * @subpackage filters");
		$this->appendLine(" */");
		$this->appendLine("class $filterClassName extends $partnetClassName");
		$this->appendLine("{");
		$this->appendLine("	private \$map_between_objects = array");
		$this->appendLine("	(");
		
		// properies map
		foreach($type->getCurrentProperties() as $prop)
		{
			$filters = $prop->getFilters();
			foreach($filters as $filter)
			{
				if ($filter != "order")
				{
					$propertyName = $this->formatFilterPropertyName($filter, $prop->getName());
					$filterName = $this->formatFilterPropertyValue($filter, $prop->getName());
					$this->appendLine("		\"".$propertyName."\" => \"$filterName\",");
				}
			}
		}
		
		// extra filters properties map
		$extraFilters = null;
		$reflectionClass = new ReflectionClass($type->getType());
		// invoke getExtraFilter only if it was defined in the current class
		if ($reflectionClass->getMethod("getExtraFilters")->getDeclaringClass()->getName() === $reflectionClass->getName()) 
		{
			$extraFilters = $type->getInstance()->getExtraFilters();
			if (!$extraFilters)
				$extraFilters = array();
				
			foreach($extraFilters as $filterFields)
			{
				if (isset($filterFields["filter"]))
				{
					$filter = $filterFields["filter"];
					$fields = $filterFields["fields"];
					$propertyName = $this->formatFilterPropertyNameForFields($filter, $fields);
					$filterName = $this->formatFilterPropertyValueForFields($filter, $fields);
					$this->appendLine("		\"".$propertyName."\" => \"$filterName\",");
				}
			}
		}
		$this->appendLine("	);");
		
		$this->appendLine("");
		
		// order by map
		$this->appendLine("	private \$order_by_map = array");
		$this->appendLine("	(");
		foreach($type->getCurrentProperties() as $prop)
		{
			$filters = $prop->getFilters();
			foreach($filters as $filter)
			{
				if ($filter == "order")
				{
					$propertyName = $prop->getName();
					$orderFieldName = $this->formatOrderPropertyValue($propertyName);
					$this->appendLine("		\"+".$propertyName."\" => \"+$orderFieldName\",");
					$this->appendLine("		\"-".$propertyName."\" => \"-$orderFieldName\",");
				}
			}
		}
		if (is_array($extraFilters))
		{
			foreach($extraFilters as $filterFields)
			{
				if (isset($filterFields["order"]))
				{
					$propertyName = $filterFields["order"];
					$orderFieldName = $this->formatOrderPropertyValue($propertyName);
					$this->appendLine("		\"".$propertyName."\" => \"$orderFieldName\",");
				}
			}
		}
		$this->appendLine("	);");
		
		$this->appendLine("");
		
		$this->appendLine("	public function getMapBetweenObjects()");
		$this->appendLine("	{");
		$this->appendLine("		return array_merge(parent::getMapBetweenObjects(), \$this->map_between_objects);");
		$this->appendLine("	}");
		$this->appendLine();
		$this->appendLine("	public function getOrderByMap()");
		$this->appendLine("	{");
		$this->appendLine("		return array_merge(parent::getOrderByMap(), \$this->order_by_map);");
		$this->appendLine("	}");
		
		// class properties
		foreach($type->getCurrentProperties() as $prop)
		{
			$filters = $prop->getFilters();
			foreach($filters as $filter)
			{
				if ($filter != "order")
				{
					$filterProp = $this->formatFilterPropertyName($filter, $prop->getName());
					$filterPropType = $prop->getType();
					if (in_array($filter, array(baseObjectFilter::IN, baseObjectFilter::MATCH_OR, baseObjectFilter::MATCH_AND)))
						$filterPropType = "string";
						
					$this->appendLine();
					$this->appendLine("	/**");
					$this->appendLine("	 * " . $this->getDocForFilter($type->getInstance(), $filterProp));
					$this->appendLine("	 * ");
					$this->appendLine("	 * @var ".$filterPropType);
					$this->appendLine("	 */");
					$this->appendLine("	public \$".$filterProp.";");
				}
			}
		}
		
		// extra filters for class properties
		if ($extraFilters !== null)
		{
			foreach($extraFilters as $filterFields)
			{
				if (isset($filterFields["filter"]))
				{
					$this->appendLine();
					$filter = $filterFields["filter"];
					$fields = $filterFields["fields"];
					$this->appendLine("	/**");
					$this->appendLine("	 * @var string");
					$this->appendLine("	 */");
					$this->appendLine("	public \$".$this->formatFilterPropertyNameForFields($filter, $fields).";");
				}
			}
		}
		
		$this->appendLine("}");
		
		$this->writeToFile($filterPath, $this->_txt);
	}
	
	private function writeOrderByEnumForType(KalturaTypeReflector $type)
	{
		$map = KAutoloader::getClassMap();
		if(!isset($map[$type->getType()]))
			return;
		
		$this->_txt = "";
		
		$parentType = $type;
		while(1)
		{
			$parentType = $parentType->getParentTypeReflector();
			if ($parentType === null || $parentType->isFilterable())
				break;			
		}
		
		$partnetClassName = ($parentType ? $parentType->getType() . "OrderBy" : "KalturaStringEnum");
		
		$enumName = $type->getType() . "OrderBy";
		$enumPath = dirname($map[$type->getType()]) . "/filters/orderEnums/$enumName.php";
			
		$this->appendLine("<?php");
		$this->appendLine("/**");
		$this->appendLine(" * @package api");
		$this->appendLine(" * @subpackage enum");
		$this->appendLine(" */");
		$this->appendLine("class $enumName extends $partnetClassName");
		$this->appendLine("{");

		foreach($type->getCurrentProperties() as $prop)
		{
			$filters = $prop->getFilters();
			foreach($filters as $filter)
			{
				if ($filter == "order")
				{
					$this->appendLine("	const ".$this->getOrderByConst($prop->getName())."_ASC = \"+".$prop->getName()."\";");
					$this->appendLine("	const ".$this->getOrderByConst($prop->getName())."_DESC = \"-".$prop->getName()."\";");
				}
			}
		}
		$this->appendLine("}");
		
		$this->writeToFile($enumPath, $this->_txt);
	}
	
	private function formatFilterPropertyName($filterType, $propertyName)
	{
		$map = array (
			baseObjectFilter::LT => "LessThan",
			baseObjectFilter::LTE => "LessThanOrEqual",
			baseObjectFilter::GT => "GreaterThan",
			baseObjectFilter::GTE => "GreaterThanOrEqual",
			baseObjectFilter::LT_OR_NULL => "LessThanOrNull",
			baseObjectFilter::LTE_OR_NULL => "LessThanOrEqualOrNull",
			baseObjectFilter::GT_OR_NULL => "GreaterThanOrNull",
			baseObjectFilter::GTE_OR_NULL => "GreaterThanOrEqualOrNull",
			baseObjectFilter::EQ => "Equal",
			baseObjectFilter::LIKE => "Like",
			baseObjectFilter::MULTI_LIKE_OR => "MultiLikeOr",
			baseObjectFilter::MULTI_LIKE_AND => "MultiLikeAnd",
			baseObjectFilter::XLIKE => "EndsWith",
			baseObjectFilter::LIKEX => "StartsWith",
			baseObjectFilter::IN => "In",
			baseObjectFilter::NOT_IN => "NotIn",
			baseObjectFilter::NOT => "NotEqual",
			baseObjectFilter::BIT_AND => "BitAnd",
			baseObjectFilter::BIT_OR => "BitOr",
			baseObjectFilter::MATCH_OR => "MatchOr",
			baseObjectFilter::MATCH_AND => "MatchAnd"
		);
		
		
		if (!array_key_exists($filterType, $map))
			throw new Exception("Filter type " . $filterType . " not found");
		
		return $propertyName.$map[$filterType];
	}
	
	private function getOrderByConst($propertyName)
	{
		$pattern = '/(.)([A-Z])/'; 
		$replacement = '\1_\2'; 
		return strtoupper(preg_replace($pattern, $replacement, $propertyName));
	}
	
	private function formatOrderPropertyValue($orderProperty)
	{
		$pattern = '/(.)([A-Z])/'; 
		$replacement = '\1_\2'; 
		return strtolower(preg_replace($pattern, $replacement, $orderProperty));
	}
	
	private function formatFilterPropertyValue($filterType, $propertyName)
	{
		$pattern = '/(.)([A-Z])/'; 
		$replacement = '\1_\2'; 
		return "_".$filterType."_".strtolower(preg_replace($pattern, $replacement, $propertyName));
	}
	
	private function formatFilterPropertyNameForFields($filterType, $fields)
	{
		foreach($fields as &$field)
			$field = ucfirst($field);
				
		$fieldsStr = implode("", $fields);
		$fieldsStr[0] = strtolower($fieldsStr[0]);
		return $this->formatFilterPropertyName($filterType, $fieldsStr);
	}
					
	private function formatFilterPropertyValueForFields($filterType, $fields)
	{
		foreach($fields as &$field)
		{
			$pattern = '/(.)([A-Z])/'; 
			$replacement = '\1_\2';
			$field = strtolower(preg_replace($pattern, $replacement, $field));
		} 
		
		
		return "_".$filterType."_".implode("-", $fields);
	}
	
	private function getDocForFilter(IFilterable $filterableObject, $filterPropName)
	{
		$filterDocs = $filterableObject->getFilterDocs();
		if (isset($filterDocs[$filterPropName]))
			return $filterDocs[$filterPropName];
		else
			return "";
	}
	
	protected function writeAfterTypes()
	{
		
	}
	
	private function appendLine($txt = "")
	{
		$this->_txt .= $txt ."\n";
	}
	
	private function writeToFile($fileName, $contents)
	{
		$dirname = dirname($fileName);
		if(!file_exists($dirname))
			mkdir($dirname, 777, true);
			
		$handle = fopen($fileName, "w");
		fwrite($handle, $contents);
		fclose($handle);
	}
}