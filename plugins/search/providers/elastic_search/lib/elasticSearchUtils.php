<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */
class elasticSearchUtils
{
	const UNDERSCORE_FIELD_DELIMITER ='_';
	const DOT_FIELD_DELIMITER = '.';
	
	private static $html_chars_to_replace = array('<br />', '<br>',
		'<br/>', '<div>', '</div>', '<div/>', '<p>', '</p>', '<p/>');

	protected static $elastic_negation_booleans = array(0, '0', false, 'false', 'off', 'no');

    /**
     * return the analyzed language field name
     * @param $language
     * @param $fieldName
	 * @param $delimiter
     * @return null|string
     */
    public static function getAnalyzedFieldName($language, $fieldName, $delimiter)
    {
        $fieldMap = array(
            'english' => 'english',
            'arabic' => 'arabic',
            'brazilian' => 'brazilian',
            'chinese' => 'cjk',
            'korean' => 'cjk',
            'japanese' => 'cjk',
            'danish' => 'danish',
            'dutch' => 'dutch',
            'finnish' => 'finnish',
            'french' => 'french',
            'german' => 'german',
            'greek' => 'greek',
            'hindi' => 'hindi',
            'indonesian' => 'indonesian',
            'italian' => 'italian',
            'norwegian' => 'norwegian',
            'portuguese' => 'portuguese',
            'russian' => 'russian',
            'spanish' => 'spanish',
            'swedish' => 'swedish',
            'turkish' => 'turkish',
            'thai' => 'thai',
        );

        $language = strtolower($language);
        if(isset($fieldMap[$language]))
            return $fieldName.$delimiter.$fieldMap[$language];

        return null;
    }

	public static function getSynonymFieldName($language, $fieldName, $delimiter)
	{
		$fieldMap = array(
			'english' => kESearchQueryManager::SYNONYM_FIELD_SUFFIX,
		);

		$language = strtolower($language);
		if(isset($fieldMap[$language]))
			return $fieldName.$delimiter.$fieldMap[$language];
	}

	public static function formatPartnerStatus($partnerId, $status)
	{
		return sprintf("p%ss%s", $partnerId, $status);
	}

	public static function formatCategoryIdStatus($categoryId, $status)
	{
		return sprintf("c%ss%s", $categoryId, $status);
	}

	public static function formatCategoryFullIdStatus($categoryId, $status)
	{
		return sprintf("s%sfid>%s", $status, $categoryId);
	}

	public static function formatParentCategoryIdStatus($categoryId, $status)
	{
		return sprintf("p%ss%s", $categoryId, $status);
	}

	public static function formatCategoryNameStatus($categoryName, $status)
	{
		return sprintf("s%sc>%s", $status, $categoryName);
	}

	public static function formatParentCategoryNameStatus($categoryName, $status)
	{
		return sprintf("s%sp>%s", $status, $categoryName);
	}

	public static function formatCategoryEntryStatus($status)
	{
		return sprintf("ces%s", $status);
	}

	public static function formatCategoryUserPermissionLevel($userId, $permissionLevel)
	{
		return sprintf("uid%spl%s", $userId, $permissionLevel);
	}

	public static function formatCategoryUserPermissionName($userId, $permissionName)
	{
		return sprintf("uid%spn%s", $userId, $permissionName);
	}

	public static function getCategoryUserAllPermissionLevels($userId)
	{
		$formatPermissions = array();
		$permissionLevelReflection = new ReflectionClass('CategoryKuserPermissionLevel');
		$permissionLevels = $permissionLevelReflection->getConstants();
		foreach ($permissionLevels as $permissionLevel)
			$formatPermissions[] = elasticSearchUtils::formatCategoryUserPermissionLevel($userId, $permissionLevel);

		return $formatPermissions;
	}

	public static function getCategoryUserAllPermissionNames($userId)
	{
		$formatPermissions = array();
		$permissionNames = array(
			PermissionName::CATEGORY_SUBSCRIBE,
			PermissionName::CATEGORY_CONTRIBUTE,
			PermissionName::CATEGORY_MODERATE,
			PermissionName::CATEGORY_EDIT,
			PermissionName::CATEGORY_VIEW,
		);

		foreach ($permissionNames as $permissionName)
			$formatPermissions[] = elasticSearchUtils::formatCategoryUserPermissionName($userId, $permissionName);

		return $formatPermissions;
	}

	public static function formatSearchTerm($searchTerm)
	{
		//remove extra spaces
		$term = preg_replace('/\s+/', ' ', $searchTerm);
		//lowercase and trim
		$term = strtolower($term);
		$term = trim($term);
		return $term;
	}

	public static function isMaster($elasticClient, $elasticHostName)
	{
		$masterInfo = $elasticClient->getMasterInfo();
		if(isset($masterInfo[0]['node']) && $masterInfo[0]['node'] == $elasticHostName)
			return true;

		return false;
	}

	public static function cleanEmptyValues(&$body)
	{
		foreach ($body as $key => $value)
		{
			if(is_null($value) || $value === '')
				unset($body[$key]);
			if(is_array($value) && ( count($value) == 0 || ( (count($value) == 1 && (isset($value[0])) && $value[0] === '' ) ) ))
				unset($body[$key]);
		}
	}
	
	public static function getNumOfFragmentsByConfigKey($highlightConfigKey)
	{
		$highlightConfig = kConf::get('highlights', 'elastic');
		//return null to use elastic default num of fragments
		$numOfFragments = isset($highlightConfig[$highlightConfigKey]) ? $highlightConfig[$highlightConfigKey] : null;
		return $numOfFragments;
	}

	public static function getBooleanValue($value)
	{
		if (in_array(self::formatSearchTerm($value), self::$elastic_negation_booleans ,true))
		{
			return false;
		}
		return true;
	}

	/**
	 * Go over the array and decode html and strip tags from all of its leafs
	 * @param array $cmd
	 */
	public static function prepareForInsertToElastic(&$cmd)
	{
		array_walk_recursive($cmd, array('elasticSearchUtils','prepareElasticLeafInput'));
	}

	public static function prepareElasticLeafInput(&$value, $key)
	{
		if(is_string($value))
		{
			self::filterHtmlFromLeaf($value);
			$value = trim($value);
			$value = @iconv('utf-8', 'utf-8//IGNORE', $value);
		}
	}

	public static function filterHtmlFromLeaf(&$value)
	{
		$value = html_entity_decode($value);
		$value = str_replace(self::$html_chars_to_replace, " ", $value);
		$value = strip_tags($value);
	}

	public static function handleSearchException($exception)
	{
		$code = $exception->getCode();
		$data = $exception->getData();
		switch ($code)
		{
			case kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD:
				throw new KalturaAPIException(KalturaESearchErrors::SEARCH_TYPE_NOT_ALLOWED_ON_FIELD, $data['itemType'], $data['fieldName']);
			case kESearchException::EMPTY_SEARCH_TERM_NOT_ALLOWED:
				throw new KalturaAPIException(KalturaESearchErrors::EMPTY_SEARCH_TERM_NOT_ALLOWED, $data['fieldName'], $data['itemType']);
			case kESearchException::SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH:
				throw new KalturaAPIException(KalturaESearchErrors::SEARCH_TYPE_NOT_ALLOWED_ON_UNIFIED_SEARCH, $data['itemType']);
			case kESearchException::EMPTY_SEARCH_ITEMS_NOT_ALLOWED:
				throw new KalturaAPIException(KalturaESearchErrors::EMPTY_SEARCH_ITEMS_NOT_ALLOWED);
			case kESearchException::UNMATCHING_BRACKETS:
				throw new KalturaAPIException(KalturaESearchErrors::UNMATCHING_BRACKETS);
			case kESearchException::MISSING_QUERY_OPERAND:
				throw new KalturaAPIException(KalturaESearchErrors::MISSING_QUERY_OPERAND);
			case kESearchException::UNMATCHING_QUERY_OPERAND:
				throw new KalturaAPIException(KalturaESearchErrors::UNMATCHING_QUERY_OPERAND);
			case kESearchException::CONSECUTIVE_OPERANDS_MISMATCH:
				throw new KalturaAPIException(KalturaESearchErrors::CONSECUTIVE_OPERANDS_MISMATCH);
			case kESearchException::INVALID_FIELD_NAME:
				throw new KalturaAPIException(KalturaESearchErrors::INVALID_FIELD_NAME, $data['fieldName']);
			case kESearchException::INVALID_METADATA_FORMAT:
				throw new kESearchException(KalturaESearchErrors::INVALID_METADATA_FORMAT);
			case kESearchException::INVALID_METADATA_FIELD:
				throw new kESearchException(KalturaESearchErrors::INVALID_METADATA_FIELD, $data['fieldName']);
			case kESearchException::INVALID_MIXED_SEARCH_TYPES:
				throw new kESearchException(KalturaESearchErrors::INVALID_MIXED_SEARCH_TYPES, $data['fieldName'], $data['fieldValue']);
			case kESearchException::MISSING_MANDATORY_PARAMETERS_IN_ORDER_ITEM:
				throw new KalturaAPIException(KalturaESearchErrors::MISSING_MANDATORY_PARAMETERS_IN_ORDER_ITEM);
			case kESearchException::MIXED_SEARCH_ITEMS_IN_NESTED_OPERATOR_NOT_ALLOWED:
				throw new KalturaAPIException(KalturaESearchErrors::MIXED_SEARCH_ITEMS_IN_NESTED_OPERATOR_NOT_ALLOWED);
			case kESearchException::MISSING_OPERATOR_TYPE:
				throw new KalturaAPIException(KalturaESearchErrors::MISSING_OPERATOR_TYPE);
			default:
				throw new KalturaAPIException(KalturaESearchErrors::INTERNAL_SERVERL_ERROR);
		}
	}

	/**
	 * @param $searchParams
	 * @param KalturaPager|null $pager
	 * @return array
	 * @throws KalturaAPIException
	 */
	public static function initSearchActionParams($searchParams, KalturaPager $pager = null)
	{
		$searchOperator = $searchParams->searchOperator;
		if (!$searchOperator)
		{
			throw new KalturaAPIException(KalturaESearchErrors::EMPTY_SEARCH_OPERATOR_NOT_ALLOWED);
		}

		if (!$searchOperator->operator)
		{
			$searchOperator->operator = KalturaSearchOperatorType::SEARCH_AND;
		}

		$coreSearchOperator = $searchOperator->toObject();

		$objectStatusesArr = array();
		if (!empty($searchParams->objectStatuses))
		{
			$objectStatusesArr = explode(',', $searchParams->objectStatuses);
		}

		$kPager = null;
		if ($pager)
		{
			$kPager = $pager->toObject();
		}

		$coreOrder = null;
		$order = $searchParams->orderBy;
		if ($order)
		{
			$coreOrder = $order->toObject();
		}

		return array($coreSearchOperator, $objectStatusesArr, $searchParams->objectId, $kPager, $coreOrder);
	}
}
