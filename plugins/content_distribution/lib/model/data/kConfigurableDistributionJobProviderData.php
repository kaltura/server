<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model.data
 */
abstract class kConfigurableDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var array
	 */
	public $fieldValues;
	
	
	/**
     * @return the $fieldValues
     */
    public function getFieldValues ()
    {
        return $this->fieldValues;
    }

	/**
     * @param array $fieldValues
     */
    public function setFieldValues ($fieldValues)
    {
        $this->fieldValues = $fieldValues;
    }
    
}
