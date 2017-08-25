<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class kESearchQueryManager
{
	const DEFAULT_TRIGRAM_PERCENTAGE = 80;

	public static function getMultiMatchQuery($searchItem, $fieldName, $shouldAddLanguageFields = false)
	{
		$multiMatch = array();
		$multiMatch['bool']['should'][0]['multi_match']['query'] = $searchItem->getSearchTerm();
		$multiMatch['bool']['should'][0]['multi_match']['fields'] = array(
			$fieldName.'.raw^3',
			$fieldName.'^2',
		);
		$multiMatch['bool']['should'][0]['multi_match']['type'] = 'most_fields';

		if($shouldAddLanguageFields)
			$multiMatch['bool']['should'][0]['multi_match']['fields'][] = $fieldName.'_*^2';

		$trigramFieldName = $fieldName.'.trigrams';
		$multiMatch['bool']['should'][1]['match'][$trigramFieldName]['query'] = $searchItem->getSearchTerm();
		$trigramPercentage = kConf::get('trigramPercentage', 'elastic', self::DEFAULT_TRIGRAM_PERCENTAGE);
		$multiMatch['bool']['should'][1]['match'][$trigramFieldName]['minimum_should_match'] = "$trigramPercentage%";
		$multiMatch['bool']['minimum_should_match'] = 1;

		return $multiMatch;
	}

	public static function getExactMatchQuery($searchItem, $fieldName, $allowedSearchTypes)
	{
		$exactMatch = array();
		$queryType = 'term';
		$fieldSuffix = '';

		if (in_array(ESearchItemType::PARTIAL, $allowedSearchTypes[$fieldName]))
			$fieldSuffix = '.raw';

		$searchTerm = elasticSearchUtils::formatSearchTerm($searchItem->getSearchTerm());
		$exactMatch[$queryType] = array( $fieldName . $fieldSuffix => $searchTerm);
		return $exactMatch;
	}

	public static function getPrefixQuery($searchItem, $fieldName, $allowedSearchTypes)
	{
		$prefixQuery = array();
		$queryType = 'prefix';
		$fieldSuffix = '';

		if(in_array(ESearchItemType::PARTIAL, $allowedSearchTypes[$fieldName]))
			$fieldSuffix = '.raw';

		$searchTerm = elasticSearchUtils::formatSearchTerm($searchItem->getSearchTerm());
		$prefixQuery[$queryType] = array( $fieldName . $fieldSuffix => $searchTerm);

		return $prefixQuery;
	}

	public static function getRangeQuery($searchItem, $fieldName, $allowedSearchTypes)
	{
		$rangeObject = $searchItem->getRange();
		if(!$rangeObject)
			return null;
		/**@var $rangeObject ESearchRange*/
		$rangeSubQuery = array();
		$rangeQuery =  array();
		$queryType = 'range';
		if(is_numeric($rangeObject->getGreaterThan()))
			$rangeSubQuery['gt'] = $rangeObject->getGreaterThan();
		if(is_numeric($rangeObject->getGreaterThanOrEqual()))
			$rangeSubQuery['gte'] = $rangeObject->getGreaterThanOrEqual();
		if(is_numeric($rangeObject->getLessThan()))
			$rangeSubQuery['lt'] = $rangeObject->getLessThan();
		if(is_numeric($rangeObject->getLessThanOrEqual()))
			$rangeSubQuery['lte'] = $rangeObject->getLessThanOrEqual();

		$rangeQuery[$queryType][$fieldName] = $rangeSubQuery;
		return $rangeQuery;
	}

	public static function getExistsQuery($searchItem, $fieldName, $allowedSearchTypes)
	{
		$ExistsQuery = array();
		$queryType = 'exists';
		$ExistsQuery[$queryType]['field'] = $fieldName;
		return $ExistsQuery;
	}

}
