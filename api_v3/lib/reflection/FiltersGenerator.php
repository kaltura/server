<?php
class FiltersGenerator extends ClientGeneratorFromPhp 
{
	private $_txt = "";
	const ABSTRACT_FILTER = 'abstractFilter';

	protected function writeHeader()
	{
		
	}
	
	protected function writeFooter()
	{
		
	}
	
	protected function writeBeforeServices()
	{
		
	}
	
	protected function writeBeforeService(KalturaServiceActionItem $serviceReflector)
	{
		
	}
	
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector)
	{
		
	}
	
	protected function writeAfterService(KalturaServiceActionItem $serviceReflector)
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
			$this->writeBaseFilterForType($type);
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
		$filterBaseClassName = $type->getType() . "BaseFilter";
		
		$filterPath = dirname($map[$type->getType()]) . "/filters/$filterClassName.php";
		if(file_exists($filterPath))
		{
			KalturaLog::notice("Filter already exists [$filterPath]");
			return;
		}
		$this->_txt = "";
			
		
		$parentType = $type;
		while(1)
		{
			$parentType = $parentType->getParentTypeReflector();
			if ($parentType === null || $parentType->isFilterable())
				break;			
		}
		
		$partnetClassName = ($parentType ? $parentType->getType() . "Filter" : "KalturaFilter");
		
		$subpackage = ($type->getPackage() == 'api' ? '' : 'api.') . 'filters';
		$this->appendLine("<?php");
		$this->appendLine("/**");
		$this->appendLine(" * @package " . $type->getPackage());
		$this->appendLine(" * @subpackage $subpackage");
		$this->appendLine(" */");
		$comments = $type->getComments();
		$isAbstractFilter = strpos($comments, self::ABSTRACT_FILTER);
		if ($isAbstractFilter !== false)
		{
			$this->appendLine("abstract class $filterClassName extends $filterBaseClassName");
		}
		else
		{
			$this->appendLine("class $filterClassName extends $filterBaseClassName");
		}
		$this->appendLine("{");
		$this->appendLine("}");
		
		$this->writeToFile($filterPath, $this->_txt);
	}

	/**
	 * @param KalturaTypeReflector $type
	 * @throws ReflectionException
	 * @throws Exception
	 */
	private function writeBaseFilterForType(KalturaTypeReflector $type)
	{
		$map = KAutoloader::getClassMap();
		if(!isset($map[$type->getType()]))
			return;
		
		$filterClassName = $type->getType() . "BaseFilter";
		
		$filterPath = dirname($map[$type->getType()]) . "/filters/base/$filterClassName.php";
		$this->_txt = "";
			
		
		$parentType = $type;
		while(1)
		{
			$parentType = $parentType->getParentTypeReflector();
			if ($parentType === null || $parentType->isFilterable())
				break;			
		}
		
		$partnetClassName = ($parentType ? $parentType->getType() . "Filter" : ($type->isRelatedFilterable() ? "KalturaRelatedFilter" : "KalturaFilter"));
		$relatedService = $this->getRelatedService($type);

		$subpackage = ($type->getPackage() == 'api' ? '' : 'api.') . 'filters.base';
		$this->appendLine("<?php");
		$this->appendLine("/**");
		$this->appendLine(" * @package " . $type->getPackage());
		if ($type->isRelatedFilterable() && !$relatedService)
			throw new Exception('did not find @relatedService annotation  '. PHP_EOL .
				' in comments for type:' . $type->getType());
		if ($type->isRelatedFilterable())
			$this->appendLine(" * @relatedService " . $relatedService);
		$this->appendLine(" * @subpackage $subpackage");
		$this->appendLine(" * @abstract");
		$this->appendLine(" */");
		$this->appendLine("abstract class $filterClassName extends $partnetClassName");
		$this->appendLine("{");
		$this->appendLine("	static private \$map_between_objects = array");
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
		if (!$type->isAbstract() && $reflectionClass->getMethod("getExtraFilters")->getDeclaringClass()->getName() === $reflectionClass->getName()) 
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
		$this->appendLine("	static private \$order_by_map = array");
		$this->appendLine("	(");
		foreach($type->getCurrentProperties() as $prop)
		{
			/* @var $prop KalturaPropertyInfo */
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
					$this->appendLine("		\"+$propertyName\" => \"+$orderFieldName\",");
					$this->appendLine("		\"-$propertyName\" => \"-$orderFieldName\",");
				}
			}
		}
		$this->appendLine("	);");
		
		$this->appendLine("");
		
		$this->appendLine("	public function getMapBetweenObjects()");
		$this->appendLine("	{");
		$this->appendLine("		return array_merge(parent::getMapBetweenObjects(), self::\$map_between_objects);");
		$this->appendLine("	}");
		$this->appendLine();
		$this->appendLine("	public function getOrderByMap()");
		$this->appendLine("	{");
		$this->appendLine("		return array_merge(parent::getOrderByMap(), self::\$order_by_map);");
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
					if($filterPropType == 'bool' || $filter == baseObjectFilter::IS_EMPTY)
						$filterPropType = 'KalturaNullableBoolean';

					if ($prop->isTime())
						$filterPropType = 'time';
					$filterDynamicType = null;
					if (in_array($filter, array(baseObjectFilter::IN, baseObjectFilter::NOT_IN, baseObjectFilter::MATCH_OR, baseObjectFilter::MATCH_AND)))
					{
						if($prop->isDynamicEnum())
							$filterDynamicType = $filterPropType;
							
						$filterPropType = "string";
					}
						
					$this->appendLine();
					$this->appendLine("	/**");
					
					if(!$type->isAbstract())
					{
						$filterableObject = $type->getInstance();
						$filterDocs = $filterableObject->getFilterDocs();
						if (isset($filterDocs[$filterProp]))
						{
							$filterDocs = explode("\n", $filterDocs[$filterProp]);
							foreach($filterDocs as $filterDoc)
								$this->appendLine("	 * $filterDoc");
								
							$this->appendLine("	 * ");
						}
					}
						
					if($filterDynamicType)
						$this->appendLine("	 * @dynamicType $filterDynamicType");
						
					$this->appendLine("	 * @var $filterPropType");
					$this->appendLine("	 */");
					$this->appendLine("	public \$$filterProp;");
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
			
		$subpackage = ($type->getPackage() == 'api' ? '' : 'api.') . 'filters.enum';
		$this->appendLine("<?php");
		$this->appendLine("/**");
		$this->appendLine(" * @package " . $type->getPackage());
		$this->appendLine(" * @subpackage $subpackage");
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
		
		$reflectionClass = new ReflectionClass($type->getType());
		if (!$type->isAbstract() && $reflectionClass->getMethod("getExtraFilters")->getDeclaringClass()->getName() === $reflectionClass->getName()) 
		{
			$extraFilters = $type->getInstance()->getExtraFilters();
			if ($extraFilters)
			{
				foreach($extraFilters as $filterFields)
				{
					if (!isset($filterFields["order"]))
						continue;
						
					$fieldName = $filterFields["order"];
					$fieldConst = $this->getOrderByConst($fieldName);
					$this->appendLine("	const {$fieldConst}_ASC = \"+$fieldName\";");
					$this->appendLine("	const {$fieldConst}_DESC = \"-$fieldName\";");
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
			baseObjectFilter::MATCH_AND => "MatchAnd",
			baseObjectFilter::NOT_CONTAINS => "NotContains",
			baseObjectFilter::IS_EMPTY => "Empty",
			baseObjectFilter::EQ_OR_NULL => "EqualOrNull",
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
	
	protected function writeAfterTypes()
	{
		
	}
	
	private function appendLine($txt = "")
	{
		$this->_txt .= "$txt\n";
	}
	
	private function writeToFile($fileName, $contents)
	{
		$dirname = dirname($fileName);
		if(!file_exists($dirname))
			mkdir($dirname, 0777, true);
			
		$handle = fopen($fileName, "w");
		fwrite($handle, $contents);
		fclose($handle);
	}

	/**
	 * @param KalturaTypeReflector $type
	 * @return mixed
	 * @throws Exception
	 */
	private function getRelatedService(KalturaTypeReflector $type)
	{
		$relatedService = '';
		$relatedServiceType = $type;
		if ($type->isRelatedFilterable()) {
			do {
				$result = null;
				if (preg_match("/\\@relatedService (.*)/", $relatedServiceType->getComments(), $result)) {
					$relatedService = $result[1];
					break;
				}
				$relatedServiceType = $relatedServiceType->getParentTypeReflector();
			} while ($relatedServiceType && !$relatedService);
		}
		return $relatedService;
	}
}