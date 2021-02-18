<?php
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'Constants.php');
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'ThresholdComparisonFactory.php');

class RuleEngine extends Constants
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
    public function __construct(array $kmsIntelliTagRules, $textTimes) {
        $this->textTimes = json_decode($textTimes, TRUE);
        $this->kmsIntelliTagRules = $this->setRulesWithKeys($kmsIntelliTagRules);
    }
    /**
     * @param array $kmsIntelliTagRules
     * @return array
     */
    private function setRulesWithKeys(array $kmsIntelliTagRules) {
        $kmsIntelliTagRulesKeys = array();
        foreach ($kmsIntelliTagRules as $kmsIntelliTagRule){
            $kmsIntelliTagRulesKeys[$kmsIntelliTagRule[self::RULE_OCM_GROUPROP][self::RULE_ENTT_KEY_PROP]] = $kmsIntelliTagRule;
        }
        return $kmsIntelliTagRulesKeys;
    }
    /**
     * @param string $response
     * @return array
     */
    public function getValuesFromApiResponse($response){
        $responseArray = json_decode($response, TRUE);
        $result = array();
        foreach ($responseArray as $key => $apiItemDetails){
            if(!$this->passedRuleConditions($apiItemDetails)){
                continue;
            }
            $rule = $this->getRule($apiItemDetails);
            if(isset($rule[self::RULE_OCM_GROUPROP][self::RULE_THRSHLD_PROP]) && !$this->passedRuleConditionThresholds($rule[self::RULE_OCM_GROUPROP][self::RULE_THRSHLD_PROP], $apiItemDetails)){
                continue;
            }
            $result = array_merge($result, $this->getValuesByRuleAndApiResponse($rule, $apiItemDetails));
        }
        return $result;
    }
    /**
     * @param array $rule
     * @param array $apiItemDetails
     * @return array[]
     */
    private function getValuesByRuleAndApiResponse(array $rule, array $apiItemDetails){
        $cuePoints = $this->getValuesForCuePoints($apiItemDetails);
        $items[] = $apiItemDetails;
        if(isset($apiItemDetails['resolutions']) && !empty($apiItemDetails['resolutions'])){
            $items = $apiItemDetails['resolutions'];
        }
        $result = $this->getFieldsArrayUsingRule($items, $rule, $this->getTypeFromItemInApi($apiItemDetails));
        if(!empty($cuePoints)){
            $result[0]['cuePoints_list'] = $cuePoints;
        }
        return $result;
    }
    /**
     * @param array $items
     * @param array $rule
     * @param string $systemName
     * @return array
     */
    private function getFieldsArrayUsingRule(array $items, array $rule, $systemName){
        $result = array();
        foreach ($items as $item){
            $result[] =  array(
                'entryMetadataShowTaxonomy' => array(
                    $rule[self::RULE_KALTURA_GROUPROP][self::RULE_SHO_TAX_ELE_PROP] => $item[self::OPCAL_PERM_ID],
                ),
                'dynamicMetadata' => array(
                    'systemName' => $this->getTypeFromItemInApi($item),
                    'objectId' => isset($item[$rule[self::RULE_OCM_GROUPROP][self::RULE_ENTT_ID_PROP]]) ? $item[$rule[self::RULE_OCM_GROUPROP][self::RULE_ENTT_ID_PROP]] : '',
                    'addIfNotExist' => isset($rule[self::RULE_KALTURA_GROUPROP][self::OP_OCM_ADD_DYNAMIC_OBJECT]),
                )
            );
        }
        return $result;
    }
    /**
     * @param array $apiItemdetails
     * @return array
     */
    private function getValuesForCuePoints(array $apiItemdetails){
        if(!isset($apiItemdetails[self::OPCAL_INSTANCES_PROP]) || count($apiItemdetails[self::OPCAL_INSTANCES_PROP]) < 1){
            return array();
        }
        $name = $apiItemdetails[self::OPCAL_ENTT_NAME];
        $nameArray = explode(' ', $name);
        $textTimes = $this->textTimes;
        $n = 0;
        $cuePoints = array();
        foreach ($textTimes as $textWordTime){
            if($textWordTime['w'] == $nameArray[0]){
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
    private function handleCuePoints(array &$cuePoints, $key, array $nameArray) {
        $textTimes = $this->textTimes;
        $fullName = '';
        $startFrom = $textTimes[$key]['s'];
        $endTo = $textTimes[$key]['e'];
        for($i = 0; $i < count($nameArray); $i++){
            if($textTimes[$key]['w'] == $nameArray[$i]){
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
    private function passedRuleConditions(array $apiItemDetails) {
        if($this->getTypeFromItemInApi($apiItemDetails) == ''){ // no type property
            return FALSE;
        }
        if($this->getRule($apiItemDetails) == ''){ // this type not exists on KMS Rules
            return FALSE;
        }
        return TRUE;
    }
    /**
     * @param array $apiItemDetails
     * @return mixed|string
     */
    private function getRule(array $apiItemDetails) {
        $type = $this->getTypeFromItemInApi($apiItemDetails);
        if(!isset($this->kmsIntelliTagRules[$type])){ // this type not exists on KMS Rules
            return '';
        }
        return $this->kmsIntelliTagRules[$type];
    }
    /**
     * @param array $apiItemDetails
     * @return mixed|string
     */
    private function getTypeFromItemInApi(array $apiItemDetails){
        if(isset($apiItemDetails[self::OPCAL_TYPE_PROP])){
            return $apiItemDetails[self::OPCAL_TYPE_PROP];
        }
        if(isset($apiItemDetails[self::OPCAL_TYPE_GROUP_PROP])){
            return $apiItemDetails[self::OPCAL_TYPE_GROUP_PROP];
        }
        return '';
    }
    /**
     * @param array $thresholds
     * @param array $apiItemdetails
     * @return bool
     */
    private function passedRuleConditionThresholds(array $thresholds, array $apiItemdetails) {
        $thresholds = isset($thresholds[self::THRSHLD_GROUPPROP][self::THRSHLD_NAME_PROP]) ? $thresholds : $thresholds[self::THRSHLD_GROUPPROP];
        foreach ($thresholds as $threshold){
            if(!isset($apiItemdetails[$threshold[self::THRSHLD_NAME_PROP]])){  // no such item in the API response
                return false;
            }
            if(ThresholdComparisonFactory::verify($apiItemdetails[$threshold[self::THRSHLD_NAME_PROP]], $threshold[self::THRSHLD_VAL_PROP], $threshold[self::THRSHLD_COMPARISON_PROP]) === FALSE){
                return false;
            }
        }
        return true;
    }
}
