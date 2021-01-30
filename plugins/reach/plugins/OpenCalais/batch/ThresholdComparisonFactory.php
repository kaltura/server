<?php


class ThresholdComparisonFactory
{
    const THRSHLD_GT_PROP = 'gte';
    const THRSHLD_GTE_PROP = 'gte';
    const THRSHLD_LT_PROP = 'lt';
    const THRSHLD_LTE_PROP = 'lte';
    const THRSHLD_EQUAL_PROP = 'eq';
    const THRSHLD_NOT_EQUAL_PROP = 'neq';
    /**
     * @param $valToVerify
     * @param $valLimit
     * @param $comparisonType
     * @return bool
     */
    public static function verify($valToVerify, $valLimit, $comparisonType){
        if($valToVerify < $valLimit && $comparisonType == self::THRSHLD_GTE_PROP) { // check Greater&Equal threshold
            return FALSE;
        }
        if($valToVerify <= $valLimit && $comparisonType == self::THRSHLD_GT_PROP) { // check Greater threshold
            return FALSE;
        }
        elseif($valToVerify > $valLimit && $comparisonType == self::THRSHLD_LTE_PROP){// check Less&Equal threshold
            return FALSE;
        }
        elseif($valToVerify >= $valLimit && $comparisonType == self::THRSHLD_LT_PROP){// check Less threshold
            return FALSE;
        }
        elseif($valToVerify != $valLimit && $comparisonType == self::THRSHLD_EQUAL_PROP){// check equal threshold
            return FALSE;
        }
        elseif($valToVerify === $valLimit && $comparisonType == self::THRSHLD_NOT_EQUAL_PROP){// check not equal threshold
            return FALSE;
        }
        return TRUE;
    }
}