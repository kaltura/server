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

}
