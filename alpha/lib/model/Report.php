<?php


/**
 * Skeleton subclass for representing a row from the 'report' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class Report extends BaseReport implements IBaseObject
{
	public function getParameters()
	{
		$params = array();
		if (preg_match_all('/\:([[A-Za-z_0-9]*)/', $this->getQuery(), $matches))
		{
			foreach($matches[1] as $param)
			{
				$params[] = $param;
			}
		}
		
		return $params;
	}

} // Report
