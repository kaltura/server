<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib
 */

class elasticSearchUtils
{
    /**
     * return the analyzed language field name
     * @param $language
     * @param $fieldName
     * @return null|string
     */
    public static function getAnalyzedFieldName($language, $fieldName)
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
            return $fieldName.'_'.$fieldMap[$language];

        return null;
    }
    
}