<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchQueryManager
{
	const BOOST_KEY = 'boost';
	const VALUE_KEY = 'value';
	const BODY_KEY = 'body';
	const BOOL_KEY = 'bool';
	const SHOULD_KEY = 'should';
	const FILTER_KEY = 'filter';
	const MULTI_MATCH_KEY = 'multi_match';
	const QUERY_KEY = 'query';
	const FIELDS_KEY = 'fields';
	const TYPE_KEY = 'type';
	const MOST_FIELDS = 'most_fields';
	const MATCH_KEY = 'match';
	const SORT_KEY = 'sort';
	const MUST_KEY = 'must';
	const MINIMUM_SHOULD_MATCH_KEY = 'minimum_should_match';
	const PREFIX_KEY = 'prefix';
	const TERM_KEY = 'term';
	const TERMS_KEY = 'terms';
	const RANGE_KEY = 'range';
	const ORDER_KEY = 'order';
	const ORDER_ASC_KEY = 'asc';
	const ORDER_DESC_KEY = 'desc';
	const GT_KEY = 'gt';
	const GTE_KEY = 'gte';
	const LT_KEY = 'lt';
	const LTE_KEY = 'lte';
	const EXISTS_KEY = 'exists';
	const FIELD_KEY = 'field';
	const NGRAMS_FIELD_SUFFIX = 'ngrams';
	const RAW_FIELD_SUFFIX = 'raw';
	const SYNONYM_FIELD_SUFFIX = 'synonym';
	const MATCH_PHRASE_KEY = 'match_phrase';
	const KALTURA_TEXT_PARTIAL_SEARCH_ANALYZER = 'kaltura_text_partial_search';
	const FROM_KEY = 'from';
	const SIZE_KEY = 'size';

	const DEFAULT_TRIGRAM_PERCENTAGE = 80;
	const RAW_FIELD_BOOST_FACTOR = 4;
	const LANGUAGE_FIELD_BOOST_FACTOR = 3;
	const MATCH_FIELD_BOOST_FACTOR = 2;
	const DEFAULT_BOOST_FACTOR = 1;
	const OP_AND = 'and';


	/**
	 * @param ESearchItem $searchItem
	 * @param string $fieldName
	 * @param ESearchQueryAttributes $queryAttributes
	 * @return array
	 */
	public static function getPartialQuery($searchItem, $fieldName, &$queryAttributes)
	{
		$partialQuery = new kESearchBoolQuery();
		$fieldBoostFactor = $searchItem::getFieldBoostFactor($fieldName);

		$matchQuery = new kESearchMatchQuery($fieldName, $searchItem->getSearchTerm());
		$multiMatchFieldBoostFactor = self::MATCH_FIELD_BOOST_FACTOR * $fieldBoostFactor;
		$matchQuery->setBoostFactor($multiMatchFieldBoostFactor);
		$matchQuery->setAnalyzer(self::KALTURA_TEXT_PARTIAL_SEARCH_ANALYZER);
		$shouldReduceResults = self::isPartnerShouldReduceResults(kBaseElasticEntitlement::$partnerId);
		if ($shouldReduceResults)
		{
			$matchQuery->setOperator(self::OP_AND);
			$matchQuery->setCutOffFreq(kConf::get('cutoff_frequency','elasticDynamicMap'));
		}
		$partialQuery->addToShould($matchQuery);

		$multiMatchQuery = new kESearchMultiMatchQuery();
		$multiMatchQuery->setQuery($searchItem->getSearchTerm());
		$rawBoostFactor = self::RAW_FIELD_BOOST_FACTOR * $fieldBoostFactor;
		$multiMatchQuery->addToFields($fieldName.'.'.self::RAW_FIELD_SUFFIX.'^'.$rawBoostFactor);
		if ($shouldReduceResults)
		{
			$multiMatchQuery->setOperator(self::OP_AND);
		}
		if($searchItem->getAddHighlight())
		{
			$queryAttributes->getQueryHighlightsAttributes()->addFieldToHighlight($fieldName,$fieldName.'.'.self::RAW_FIELD_SUFFIX);
			$queryAttributes->getQueryHighlightsAttributes()->addFieldToHighlight($fieldName, $fieldName);
		}

		if($searchItem->shouldAddLanguageSearch())
		{
			$languages = $queryAttributes->getPartnerLanguages();
			$shouldIgnoreSynonym = $queryAttributes->getIgnoreSynonymOnPartner();
			foreach ($languages as $language)
			{
				$mappingLanguageField = elasticSearchUtils::getAnalyzedFieldName($language, $fieldName, $searchItem->getItemMappingFieldsDelimiter());
				if($mappingLanguageField)
				{
					$languageFieldBoostFactor = self::LANGUAGE_FIELD_BOOST_FACTOR * $fieldBoostFactor;
					$multiMatchQuery->addToFields($mappingLanguageField.'^'.$languageFieldBoostFactor);
					if($searchItem->getAddHighlight())
						$queryAttributes->getQueryHighlightsAttributes()->addFieldToHighlight($fieldName, $mappingLanguageField);
					$synonymField = elasticSearchUtils::getSynonymFieldName($language,$mappingLanguageField,elasticSearchUtils::DOT_FIELD_DELIMITER);
					
					if(!$shouldIgnoreSynonym && $synonymField)
					{
						$multiMatchQuery->addToFields($synonymField);//don't boost
						if($searchItem->getAddHighlight())
							$queryAttributes->getQueryHighlightsAttributes()->addFieldToHighlight($fieldName, $synonymField);
					}
				}
			}
		}
		$partialQuery->addToShould($multiMatchQuery);

		$maxWordsForNgram = kConf::get('max_words_for_ngram','elasticDynamicMap');
		$splitedSearchTerms = preg_split('/\s+/', $searchItem->getSearchTerm());
		if (!$shouldReduceResults || ($shouldReduceResults && count($splitedSearchTerms) <= $maxWordsForNgram))
		{
			$trigramFieldName = $fieldName.'.'.self::NGRAMS_FIELD_SUFFIX;
			$matchQuery = new kESearchMatchQuery($trigramFieldName, $searchItem->getSearchTerm());
			$trigramPercentage = kConf::get('ngramPercentage', 'elastic', self::DEFAULT_TRIGRAM_PERCENTAGE);
			$matchQuery->setMinimumShouldMatch("$trigramPercentage%");
			if($searchItem->getAddHighlight())
				$queryAttributes->getQueryHighlightsAttributes()->addFieldToHighlight($fieldName, $trigramFieldName);
			$partialQuery->addToShould($matchQuery);

		}
		if ($searchItem->shouldAddSearchTermToSearchHistory($fieldName, $searchItem->getAddHighlight(), $queryAttributes))
		{
			$queryAttributes->addToSearchHistoryTerms($searchItem->getSearchTerm());
		}

		return $partialQuery;
	}

	protected static function isPartnerShouldReduceResults($partnerId)
	{
		$elasticReduceResultsPartners = kConf::get('reduced_results_partner_list','elasticDynamicMap');
		if (in_array($partnerId,$elasticReduceResultsPartners))
		{
			return true;
		}
		return false;
	}

	public static function getExactMatchQuery($searchItem, $fieldName, $allowedSearchTypes, &$queryAttributes)
	{
		if ($searchItem::shouldReplaceBooleanValue($fieldName))
		{
			$searchTerm = elasticSearchUtils::getBooleanValue($searchItem->getSearchTerm());
		}
		else
		{
			$searchTerm = elasticSearchUtils::formatSearchTerm($searchItem->getSearchTerm());
		}
		$fieldBoostFactor = $searchItem::getFieldBoostFactor($fieldName);
		$fieldSuffix = '';
		$queryObject = 'kESearchTermQuery';

		if(isset($allowedSearchTypes[$fieldName]) && in_array(ESearchItemType::PARTIAL, $allowedSearchTypes[$fieldName]))
			$queryObject = 'kESearchMatchPhraseQuery';
		
		$exactMatch = new $queryObject($fieldName, $searchTerm);
		$exactMatch->setBoostFactor($fieldBoostFactor);
		if($searchItem->getAddHighlight())
			$queryAttributes->getQueryHighlightsAttributes()->addFieldToHighlight($fieldName, $fieldName . $fieldSuffix);

		if ($searchItem->shouldAddSearchTermToSearchHistory($fieldName, $searchItem->getAddHighlight(), $queryAttributes))
		{
			$queryAttributes->addToSearchHistoryTerms($searchItem->getSearchTerm());
		}

		return $exactMatch;
	}

	public static function getPrefixQuery($searchItem, $fieldName, $allowedSearchTypes, &$queryAttributes)
	{
		$fieldSuffix = '';
		if(isset($allowedSearchTypes[$fieldName]) && in_array(ESearchItemType::PARTIAL, $allowedSearchTypes[$fieldName]))
			$fieldSuffix = '.'.self::RAW_FIELD_SUFFIX;

		$searchTerm = elasticSearchUtils::formatSearchTerm($searchItem->getSearchTerm());
		$fieldBoostFactor = $searchItem::getFieldBoostFactor($fieldName);
		$prefixQuery = new kESearchPrefixQuery($fieldName . $fieldSuffix, $searchTerm);
		$prefixQuery->setBoostFactor($fieldBoostFactor);
		if($searchItem->getAddHighlight())
			$queryAttributes->getQueryHighlightsAttributes()->addFieldToHighlight($fieldName, $fieldName . $fieldSuffix);
		return $prefixQuery;
	}

	public static function getRangeQuery($searchItem, $fieldName, $allowedSearchTypes, &$queryAttributes)
	{
		$rangeObject = $searchItem->getRange();
		if(!$rangeObject)
			return null;
		$rangeQuery = new kESearchRangeQuery($rangeObject, $fieldName);
		return $rangeQuery;
	}

	public static function getExistsQuery($searchItem, $fieldName, $allowedSearchTypes, &$queryAttributes)
	{
		$existsQuery = new kESearchExistsQuery($fieldName);
		return $existsQuery;
	}

	public static function getNestedQuery($query, &$queryAttributes)
	{
		/** @var  ESearchQueryAttributes $queryAttributes*/
		$nestedQuery = new kESearchNestedQuery();
		$nestedQuery->setPath($queryAttributes->getNestedOperatorPath());
		$nestedQuery->setInnerHitsSize($queryAttributes->getNestedOperatorInnerHitsSize());
		$nestedQuery->setInnerHitsSource(true);
		$highlight = new kESearchHighlightQuery($queryAttributes->getQueryHighlightsAttributes()->getFieldsToHighlight(), $queryAttributes->getNestedOperatorNumOfFragments());
		$nestedQuery->setHighlight($highlight->getFinalQuery());
		$nestedQuery->setQuery($query);
		$nestedQuery->setInnerHitsName($queryAttributes->getNestedQueryName());
		$nestedQuery->setSort($queryAttributes->getNestedQuerySortOrder());

		return $nestedQuery;
	}

}
