<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */
class elasticSearchUtils
{
	const UNDERSCORE_FIELD_DELIMITER ='_';
	const DOT_FIELD_DELIMITER = '.';

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
            'basque' => 'basque',
            'brazilian' => 'brazilian',
            'bulgarian' => 'bulgarian',
            'catalan' => 'catalan',
            'chinese' => 'cjk',
            'korean' => 'cjk',
            'japanese' => 'cjk',
            'czech' => 'czech',
            'danish' => 'danish',
            'dutch' => 'dutch',
            'finnish' => 'finnish',
            'french' => 'french',
            'galician' => 'galician',
            'german' => 'german',
            'greek' => 'greek',
            'hindi' => 'hindi',
            'hungarian' => 'hungarian',
            'indonesian' => 'indonesian',
            'irish' => 'irish',
            'italian' => 'italian',
            'latvian' => 'latvian',
            'lithuanian' => 'lithuanian',
            'norwegian' => 'norwegian',
            'persian' => 'persian',
            'portuguese' => 'portuguese',
            'romanian' => 'romanian',
            'russian' => 'russian',
            'sorani' => 'sorani',
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
			'english' => 'synonym',
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

}
