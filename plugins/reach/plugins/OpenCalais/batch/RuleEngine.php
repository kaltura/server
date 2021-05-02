<?php
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'OpenCalaisConstants.php');
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'ThresholdComparisonFactory.php');

class RuleEngine extends OpenCalaisConstants
{
    /**
     * @var array
     */
    private $kmsIntelliTagRules;
    /**
     * @var
     */
    private $textTimes;

    /**
     * @param array $kmsIntelliTagRules
     * @param string $textTimes
     */
    public function __construct(array $kmsIntelliTagRules, $textTimes)
    {
        $this->textTimes = json_decode($textTimes, true);
        $this->kmsIntelliTagRules = $this->setRulesWithKeys($kmsIntelliTagRules);
    }
    /**
     * @param array $kmsIntelliTagRules
     * @return array
     */
    protected function setRulesWithKeys(array $kmsIntelliTagRules)
    {
        $kmsIntelliTagRulesKeys = array();
        foreach ($kmsIntelliTagRules as $kmsIntelliTagRule)
        {
            $kmsIntelliTagRulesKeys[$kmsIntelliTagRule[self::RULE_OCM_GROUPROP][self::RULE_ENTT_KEY_PROP]][] = $kmsIntelliTagRule;
        }
        return $kmsIntelliTagRulesKeys;
    }
    /**
     * @param string $response
     * @return array
     */
    public function getValuesFromApiResponse($response)
    {
        $responseArray = json_decode($response, true);
        $result = array();
        foreach ($responseArray as $key => $apiItemDetails)
        {
            if(!$this->passedRuleConditions($apiItemDetails))
            {
                continue;
            }
            $rules = $this->getRulesForEntity($apiItemDetails);
            foreach ($rules as $rule)
            {
                if(isset($rule[self::RULE_OCM_GROUPROP][self::RULE_THRSHLD_PROP]) && !$this->passedRuleConditionThresholds($rule[self::RULE_OCM_GROUPROP][self::RULE_THRSHLD_PROP], $apiItemDetails))
                {
                    continue;
                }
                $result = array_merge($result, $this->getValuesByRuleAndApiResponse($rule, $apiItemDetails));
            }
        }
        return $result;
    }
    /**
     * @param array $rule
     * @param array $apiItemDetails
     * @return array[]
     */
    protected function getValuesByRuleAndApiResponse(array $rule, array $apiItemDetails)
    {
        $cuePoints = $this->getValuesForCuePoints($apiItemDetails);
        $resolutions[] = $apiItemDetails;
        if(isset($apiItemDetails[self::OPCAL_RESOLUTIONS]) && !empty($apiItemDetails[self::OPCAL_RESOLUTIONS]))
        {
            $resolutions = $apiItemDetails[self::OPCAL_RESOLUTIONS];
        }
        $result = $this->getFieldsArrayUsingRule($resolutions, $rule, $apiItemDetails);

        if(!empty($cuePoints))
        {
            $result[0][self::CUEPOINTS_LIST] = $cuePoints;
        }
        return $result;
    }
    /**
     * @param array $items
     * @param array $rule
     * @param string $fullItem
     * @return array
     */
    protected function getFieldsArrayUsingRule(array $items, array $rule, $fullItem)
    {
        $result = array();
        foreach ($items as $item)
        {
            $result[] =  array(
                self::ENTRY_METADATA_SHOW_TAXONOMY => array(
                    $rule[self::RULE_KALTURA_GROUPROP][self::RULE_SHO_TAX_ELE_PROP] => $item[$rule[self::RULE_OCM_GROUPROP][self::RULE_ENTT_ID_PROP]],
                ),
                self::DYNAMIC_METADATA => array(
                    'fieldName' => $rule[self::RULE_KALTURA_GROUPROP][self::RULE_SHO_TAX_ELE_PROP],
                    'objectId' => isset($item[$rule[self::RULE_OCM_GROUPROP][self::RULE_ENTT_ID_PROP]]) ? $item[$rule[self::RULE_OCM_GROUPROP][self::RULE_ENTT_ID_PROP]] : '',
                    'addIfNotExist' => isset($rule[self::RULE_KALTURA_GROUPROP][self::OP_OCM_ADD_DYNAMIC_OBJECT]),
                    'fullItem' => $fullItem,
                )
            );
        }
        return $result;
    }
    /**
     * @param array $apiItemdetails
     * @return array
     */
    protected function getValuesForCuePoints(array $apiItemdetails)
    {
        if(!isset($apiItemdetails[self::OPCAL_INSTANCES_PROP]) || count($apiItemdetails[self::OPCAL_INSTANCES_PROP]) < 1)
        {
            return array();
        }
        $name = $apiItemdetails[self::OPCAL_ENTT_NAME];
        $nameArray = explode(' ', $name);
        $textTimes = $this->textTimes;
        $n = 0;
        $cuePoints = array();
        foreach ($textTimes as $textWordTime)
        {
            if($textWordTime['w'] == $nameArray[0])
            {
                $this->handleCuePoints($cuePoints, $n, $nameArray);
            }
            $n++;
        }
        return $cuePoints;
    }
    /**
     * @param array $cuePoints
     * @param int $key
     * @param array $nameArray
     */
    protected function handleCuePoints(array &$cuePoints, $key, array $nameArray)
    {
        $textTimes = $this->textTimes;
        $fullName = '';
        $startFrom = $textTimes[$key]['s'];
        $endTo = $textTimes[$key]['e'];
        for($i = 0; $i < count($nameArray); $i++)
        {
            if($textTimes[$key]['w'] == $nameArray[$i])
            {
                $endTo = $textTimes[$key]['e'];
                $fullName .= $textTimes[$key++]['w'].' ';
            }
            else{
                return;
            }
        }
        $fullName = trim($fullName);
        $cuePoints[] = array('title' => $fullName, 'startTime' => $startFrom, 'endTime' => $endTo);
    }
    /**
     * @param array $apiItemDetails
     * @return bool
     */
    protected function passedRuleConditions(array $apiItemDetails)
    {
        if($this->getTypeFromItemInApi($apiItemDetails) == '')
        { // no type property
            return FALSE;
        }
        if(empty($this->getRulesForEntity($apiItemDetails)))
        { // this type not exists on KMS Rules
            return FALSE;
        }
        return TRUE;
    }
    /**
     * @param array $apiItemDetails
     * @return mixed|string
     */
    protected function getRulesForEntity(array $apiItemDetails)
    {
        $type = $this->getTypeFromItemInApi($apiItemDetails);
        if(!isset($this->kmsIntelliTagRules[$type]))
        { // this type not exists on KMS Rules
            return array();
        }
        return $this->kmsIntelliTagRules[$type];
    }
    /**
     * @param array $apiItemDetails
     * @return mixed|string
     */
    protected function getTypeFromItemInApi(array $apiItemDetails)
    {
        if(isset($apiItemDetails[self::OPCAL_TYPE_PROP]))
        {
            return $apiItemDetails[self::OPCAL_TYPE_PROP];
        }
        if(isset($apiItemDetails[self::OPCAL_TYPE_GROUP_PROP]))
        {
            return $apiItemDetails[self::OPCAL_TYPE_GROUP_PROP];
        }
        return '';
    }
    /**
     * @param array $thresholds
     * @param array $apiItemdetails
     * @return bool
     */
    protected function passedRuleConditionThresholds(array $thresholds, array $apiItemdetails)
    {
        $thresholds = isset($thresholds[self::THRSHLD_GROUPPROP][self::THRSHLD_NAME_PROP]) ? $thresholds : $thresholds[self::THRSHLD_GROUPPROP];
        foreach ($thresholds as $threshold)
        {
            if(!isset($apiItemdetails[$threshold[self::THRSHLD_NAME_PROP]]))
            {  // no such item in the API response
                return false;
            }
            if(ThresholdComparisonFactory::verify($apiItemdetails[$threshold[self::THRSHLD_NAME_PROP]], $threshold[self::THRSHLD_VAL_PROP], $threshold[self::THRSHLD_COMPARISON_PROP]) === FALSE)
            {
                return false;
            }
        }
        return true;
    }
}
