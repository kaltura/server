<?php

require(__DIR__ . '/IndexableField.php');
require(__DIR__ . '/IndexableObject.php');
require(__DIR__ . '/IndexableOptimization.php');
require(__DIR__ . '/IndexableCacheInvalidationKey.php');
require(__DIR__ . '/IndexGeneratorBase.php');

require_once(__DIR__ . '/../../../../api_v3/bootstrap.php');

KalturaTypeReflector::setClassInheritMapPath(KAutoloader::buildPath(kConf::get("cache_root_path"), "api_v3", "KalturaClassInheritMap.cache"));

class ApiSearchObjectsGenerator extends IndexGeneratorBase
{
	public function generateEnumFiles($keys)
	{
		foreach($keys as $key) {
			$this->handleSingleEnumFile($key);
		}
	}

	public function generateSearchObjectFiles($keys)
	{
		foreach($keys as $key) {
			$this->handleSingleSearchObjectFile($key);
		}
	}
	
	private function handleSingleEnumFile($key) {
		/** @var IndexableObject $object */
		$object = $this->searchableObjects[$key];
		if (!$object->apiName)
			return;

		$typeReflector = KalturaTypeReflectorCacher::get($object->apiName);
		/** @var array($apiType => $apiParentType) $apiTypes */
		$apiTypes = array($object->apiName => null);
		$subTypes = $typeReflector->getSubTypesNames();
		foreach($subTypes as $subType)
		{
			$subTypeReflector = KalturaTypeReflectorCacher::get($subType);
			$apiTypes[$subType] = $subTypeReflector->getParentTypeReflector()->getType();
		}

		$enumsToGenerate = array();
		$matchAttributes = $this->getMatchAttributes($key);
		$compareAttributes = $this->getCompareAttributes($key);
		foreach($apiTypes as $apiType => $parentApiType)
		{
			$typeReflector = KalturaTypeReflectorCacher::get($apiType);
			$enumsToGenerate[$apiType.'MatchAttribute'] = array(
				$apiType,
				$this->filterAttributeByClass($typeReflector, $matchAttributes),
				($parentApiType) ? $parentApiType.'MatchAttribute' : 'KalturaStringEnum',
			);
			$enumsToGenerate[$apiType.'CompareAttribute'] = array(
				$apiType,
				$this->filterAttributeByClass($typeReflector, $compareAttributes),
				($parentApiType) ? $parentApiType.'CompareAttribute' : 'KalturaStringEnum'
			);
		}

		if (count($matchAttributes))
			$this->attributesError($matchAttributes, $object->apiName);

		if (count($compareAttributes))
			$this->attributesError($compareAttributes, $object->apiName);

		foreach($enumsToGenerate as $enumClass => $additionalData)
		{
			$apiObjectClass = $additionalData[0];
			$constants = $additionalData[1];
			$parentEnumClass = $additionalData[2];

			$filePath = $this->getFilePathForEnum($apiObjectClass, $enumClass);
			if (!file_exists(dirname($filePath)))
				mkdir(dirname($filePath), 0777, true);
			$fp = fopen($filePath, 'w+');
			if(!$fp)
			{
				KalturaLog::err("Failed to open file " . $filePath);
				exit(1);
			}

			print "\tGenerating enum $enumClass\n";
			$this->createEnumFileHeader($fp, $enumClass, $parentEnumClass);
			$this->generateConstants($fp, $constants);
			$this->createEnumFileFooter($fp, $enumClass);
			fclose($fp);
		}
	}

	private function handleSingleSearchObjectFile($key) {
		/** @var IndexableObject $object */
		$object = $this->searchableObjects[$key];
		if (!$object->apiName)
			return;

		$typeReflector = KalturaTypeReflectorCacher::get($object->apiName);

		$apiTypes = array_merge(array($object->apiName), $typeReflector->getSubTypesNames());
		$classesToGenerate = array();
		foreach($apiTypes as $apiType)
		{
			$classesToGenerate[$apiType.'MatchAttributeCondition'] = array($apiType, $apiType.'MatchAttribute', 'KalturaSearchMatchAttributeCondition');
			$classesToGenerate[$apiType.'CompareAttributeCondition'] = array($apiType, $apiType.'CompareAttribute', 'KalturaSearchComparableAttributeCondition');
		}

		foreach($classesToGenerate as $className => $additionalData)
		{
			$apiObjectClass = $additionalData[0];
			$enumType = $additionalData[1];
			$parentClass = $additionalData[2];

			$filePath = $this->getFilePathForSearchObject($apiObjectClass, $className);
			if (!file_exists(dirname($filePath)))
				mkdir(dirname($filePath), 0777, true);
			$fp = fopen($filePath, 'w+');
			if(!$fp)
			{
				KalturaLog::err("Failed to open file " . $filePath);
				exit(1);
			}

			print "\tGenerating API object $className\n";
			$this->createClassFileHeader($fp, $className, $parentClass, $apiObjectClass, $enumType);
			$this->generateClass($fp, $className, $enumType, $key.'Index');
			$this->createClassFileFooter($fp, $className);
			fclose($fp);
		}
	}
	
	private function createEnumFileHeader($fp, $class, $parentEnumClass) {
		$this->printToFile($fp, "<?php");
		$this->printToFile($fp, "");
		$this->printToFile($fp, "/**");
		$this->printToFile($fp, " * Auto-generated enum class");
		$this->printToFile($fp, "*/");
		$this->printToFile($fp, "class $class extends $parentEnumClass");
		$this->printToFile($fp, "{");
	}
	
	private function createEnumFileFooter($fp, $class) {
		$this->printToFile($fp, "}");
		$this->printToFile($fp, "");
	}

	private function generateConstants($fp, $constants) {
		foreach($constants as $constant => $value) {
			$const = strtoupper(preg_replace('/([a-z])([A-Z])/', '$1_$2', $value->apiName));
			$this->printToFile($fp, "const " . $const . " = \"{$value->apiName}\";", 1);
		}
	}

	private function createClassFileHeader($fp, $className, $parentClass, $apiObjectClass, $enumType) {
		$this->printToFile($fp, "<?php");
		$this->printToFile($fp, "");
		$this->printToFile($fp, "/**");
		$this->printToFile($fp, " * Auto-generated class.");
		$this->printToFile($fp, " * ");
		$this->printToFile($fp, " * Used to search $apiObjectClass attributes. Use $enumType enum to provide attribute name.");
		$this->printToFile($fp, "*/");
		$this->printToFile($fp, "class $className extends $parentClass");
		$this->printToFile($fp, "{");
	}

	private function createClassFileFooter($fp, $className) {
		$this->printToFile($fp, "}");
		$this->printToFile($fp, "");
	}

	private function generateClass($fp, $className, $enumType, $indexClass) {
		$this->printToFile($fp, "	/**");
		$this->printToFile($fp, "	 * @var $enumType");
		$this->printToFile($fp, "	 */");
		$this->printToFile($fp, "	public \$attribute;");
		$this->printToFile($fp, "");
		$this->printToFile($fp, "	private static \$mapBetweenObjects = array");
		$this->printToFile($fp, "	(");
		$this->printToFile($fp, "		\"attribute\" => \"attribute\",");
		$this->printToFile($fp, "	);");
		$this->printToFile($fp, "");
		$this->printToFile($fp, "	public function getMapBetweenObjects()");
		$this->printToFile($fp, "	{");
		$this->printToFile($fp, "		return array_merge(parent::getMapBetweenObjects() , self::\$mapBetweenObjects);");
		$this->printToFile($fp, "	}");
		$this->printToFile($fp, "");
		$this->printToFile($fp, "	protected function getIndexClass()");
		$this->printToFile($fp, "	{");
		$this->printToFile($fp, "		return '$indexClass';");
		$this->printToFile($fp, "	}");
	}
	
	private function getCompareAttributes($class)
	{
		$attributes = array();
		foreach($this->searchableFields[$class] as $key => $value)
		{
			/** @var IndexableField $value */
			if($value->apiName && in_array($value->type, array('int', 'bint', 'datetime')))
				$attributes[$key] = $value;
		}
		return $attributes;
	}

	private function getMatchAttributes($class)
	{
		$attributes = array();
		foreach($this->searchableFields[$class] as $key => $value)
		{
			/** @var IndexableField $value */
			if($value->apiName && ($value->type == 'string' || $value->matchable))
				$attributes[$key] = $value;
		}
		return $attributes;
	}

	private function filterAttributeByClass(KalturaTypeReflector $typeReflector, array &$attributes)
	{
		$attributesForClass = array();
		$attributesLeft = array();
		foreach($attributes as $key => $value)
		{
			/** @var IndexableField $value */
			$property = $this->getProperty($typeReflector, $value->apiName);
			if ($property)
				$attributesForClass[$key] = $value;
			else
				$attributesLeft[$key] = $value;
		}
		$attributes = $attributesLeft;
		return $attributesForClass;
	}

	private function getProperty(KalturaTypeReflector $typeReflector, $name)
	{
		$properties = $typeReflector->getCurrentProperties();
		foreach($properties as $property)
		{
			/** @var KalturaPropertyInfo $property */
			if ($property->getName() == $name)
				return $property;
		}
		return null;
	}

	private function getFilePathForEnum($apiObjectClass, $enumClass)
	{
		$apiObjectFilePath = $this->getClassFilePath($apiObjectClass);
		$path = dirname($apiObjectFilePath) . "/filters/attributeEnums/$enumClass.php";
		return $path;
	}

	private function getFilePathForSearchObject($apiObjectClass, $enumClass)
	{
		$apiObjectFilePath = $this->getClassFilePath($apiObjectClass);
		$path = dirname($apiObjectFilePath) . "/filters/advanced/$enumClass.php";
		return $path;
	}

	private function getClassFilePath($class)
	{
		$map = KAutoloader::getClassMap();
		if(!isset($map[$class]))
			throw new Exception("File path was not found for [$class]");
		return $map[$class];
	}

	private function attributesError($attributes, $class)
	{
		echo 'Attributes '.implode(', ', array_keys($attributes)). ' could not be found on class "'.$class.'" or one of it\'s child classes."'.PHP_EOL;
		exit(0);
	}
}

function main($argv) 
{
	if(count($argv) < 2)
	{
		KalturaLog::err("Illegal command. use IndexObjectsGenerator <indexFile>\n");
		exit(1);
	}

	$generator = new ApiSearchObjectsGenerator();

	$args = array_slice($argv, 1);
	foreach($args as $arg) {
		$indexFile = $arg;
		KalturaLog::info("Handling Index file $indexFile");
		$keys = $generator->load($indexFile);
		$generator->generateEnumFiles($keys);
		$generator->generateSearchObjectFiles($keys);
	}
}

main($argv);
exit(0);
