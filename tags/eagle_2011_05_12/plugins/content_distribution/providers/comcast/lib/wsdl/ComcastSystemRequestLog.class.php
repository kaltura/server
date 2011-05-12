<?php


class ComcastSystemRequestLog extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfSystemRequestLogField';
			case 'failedAverageResponseTimes':
				return 'ComcastArrayOflong';
			case 'failedRequestCounts':
				return 'ComcastArrayOflong';
			case 'failureRates':
				return 'ComcastArrayOffloat';
			case 'requestCounts':
				return 'ComcastArrayOflong';
			case 'successfulAverageResponseTimes':
				return 'ComcastArrayOflong';
			case 'systemRequestType':
				return 'ComcastSystemRequestType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfSystemRequestLogField
	 **/
	public $template;
				
	/**
	 * @var dateTime
	 **/
	public $currentDate;
				
	/**
	 * @var long
	 **/
	public $failedAverageResponseTime;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $failedAverageResponseTimes;
				
	/**
	 * @var long
	 **/
	public $failedRequestCount;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $failedRequestCounts;
				
	/**
	 * @var float
	 **/
	public $failedRequestsPerHour;
				
	/**
	 * @var float
	 **/
	public $failedRequestsPerMinute;
				
	/**
	 * @var float
	 **/
	public $failedRequestsPerSecond;
				
	/**
	 * @var float
	 **/
	public $failureRate;
				
	/**
	 * @var ComcastArrayOffloat
	 **/
	public $failureRates;
				
	/**
	 * @var long
	 **/
	public $requestCount;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $requestCounts;
				
	/**
	 * @var float
	 **/
	public $requestsPerHour;
				
	/**
	 * @var float
	 **/
	public $requestsPerMinute;
				
	/**
	 * @var float
	 **/
	public $requestsPerSecond;
				
	/**
	 * @var dateTime
	 **/
	public $sampleEndDate;
				
	/**
	 * @var long
	 **/
	public $sampleLength;
				
	/**
	 * @var dateTime
	 **/
	public $sampleStartDate;
				
	/**
	 * @var string
	 **/
	public $serverAddress;
				
	/**
	 * @var string
	 **/
	public $serverName;
				
	/**
	 * @var long
	 **/
	public $successfulAverageResponseTime;
				
	/**
	 * @var ComcastArrayOflong
	 **/
	public $successfulAverageResponseTimes;
				
	/**
	 * @var ComcastSystemRequestType
	 **/
	public $systemRequestType;
				
	/**
	 * @var long
	 **/
	public $totalFailedRequestCount;
				
	/**
	 * @var long
	 **/
	public $totalRequestCount;
				
	/**
	 * @var long
	 **/
	public $totalSuccessfulRequestCount;
				
}


