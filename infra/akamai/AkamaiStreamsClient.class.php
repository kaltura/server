<?php

	
class AkamaiStreamsClient extends AkamaiClient
{
	const WSDL_URL = 'https://control.akamai.com/webservices/services/Streams?wsdl';
	
	function __construct($username, $password)
	{
		parent::__construct(self::WSDL_URL, $username, $password);
	}
	
	
	public function getCPCodes()
	{
		$params = array();
		

		$result = $this->call("getCPCodes", $params);
		$this->logError();
		return $result;
	}
	
	public function getBitRates()
	{
		$params = array();
		

		$result = $this->call("getBitRates", $params);
		$this->logError();
		return $result;
	}
	
	public function getContinents()
	{
		$params = array();
		

		$result = $this->call("getContinents", $params);
		$this->logError();
		return $result;
	}
	
	public function getTrafficLocations()
	{
		$params = array();
		

		$result = $this->call("getTrafficLocations", $params);
		$this->logError();
		return $result;
	}
	
	public function getWMSStreamDetails($streamID)
	{
		$params = array();
		
		$params["streamID"] = $this->parseParam($streamID, 'xsd:int');

		$result = $this->call("getWMSStreamDetails", $params);
		$this->logError();
		return $result;
	}
	
	public function getWMSStreams()
	{
		$params = array();
		

		$result = $this->call("getWMSStreams", $params);
		$this->logError();
		return $result;
	}
	
	public function getRealStreamDetails($streamID)
	{
		$params = array();
		
		$params["streamID"] = $this->parseParam($streamID, 'xsd:int');

		$result = $this->call("getRealStreamDetails", $params);
		$this->logError();
		return $result;
	}
	
	public function getRealStreams()
	{
		$params = array();
		

		$result = $this->call("getRealStreams", $params);
		$this->logError();
		return $result;
	}
	
	public function getQTStreamDetails($streamID)
	{
		$params = array();
		
		$params["streamID"] = $this->parseParam($streamID, 'xsd:int');

		$result = $this->call("getQTStreamDetails", $params);
		$this->logError();
		return $result;
	}
	
	public function getQTStreams()
	{
		$params = array();
		

		$result = $this->call("getQTStreams", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionWMSStream($streamName, $primaryEncoderURL, $primaryEncoderContinent, $backupEncoderURL, $backupEncoderContinent, $bitRate, $trafficLocation, $expectedPeakTraffic, $cpcode, $comments, $emailID)
	{
		$params = array();
		
		$params["streamName"] = $this->parseParam($streamName, 'xsd:string');
		$params["primaryEncoderURL"] = $this->parseParam($primaryEncoderURL, 'xsd:string');
		$params["primaryEncoderContinent"] = $this->parseParam($primaryEncoderContinent, 'xsd:string');
		$params["backupEncoderURL"] = $this->parseParam($backupEncoderURL, 'xsd:string');
		$params["backupEncoderContinent"] = $this->parseParam($backupEncoderContinent, 'xsd:string');
		$params["bitRate"] = $this->parseParam($bitRate, 'xsd:int');
		$params["trafficLocation"] = $this->parseParam($trafficLocation, 'xsd:string');
		$params["expectedPeakTraffic"] = $this->parseParam($expectedPeakTraffic, 'xsd:int');
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["comments"] = $this->parseParam($comments, 'xsd:string');
		$params["emailID"] = $this->parseParam($emailID, 'xsd:string');

		$result = $this->call("provisionWMSStream", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionRealStream($streamName, $primaryEncoderIP, $primaryEncoderContinent, $primaryAutomaticReconnect, $primaryDomainName, $backupEncoderIP, $backupEncoderContinent, $backupAutomaticReconnect, $backupDomainName, $bitRate, $trafficLocation, $expectedPeakTraffic, $cpcode, $comments, $emailID)
	{
		$params = array();
		
		$params["streamName"] = $this->parseParam($streamName, 'xsd:string');
		$params["primaryEncoderIP"] = $this->parseParam($primaryEncoderIP, 'xsd:string');
		$params["primaryEncoderContinent"] = $this->parseParam($primaryEncoderContinent, 'xsd:string');
		$params["primaryAutomaticReconnect"] = $this->parseParam($primaryAutomaticReconnect, 'xsd:boolean');
		$params["primaryDomainName"] = $this->parseParam($primaryDomainName, 'xsd:string');
		$params["backupEncoderIP"] = $this->parseParam($backupEncoderIP, 'xsd:string');
		$params["backupEncoderContinent"] = $this->parseParam($backupEncoderContinent, 'xsd:string');
		$params["backupAutomaticReconnect"] = $this->parseParam($backupAutomaticReconnect, 'xsd:boolean');
		$params["backupDomainName"] = $this->parseParam($backupDomainName, 'xsd:string');
		$params["bitRate"] = $this->parseParam($bitRate, 'xsd:int');
		$params["trafficLocation"] = $this->parseParam($trafficLocation, 'xsd:string');
		$params["expectedPeakTraffic"] = $this->parseParam($expectedPeakTraffic, 'xsd:int');
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["comments"] = $this->parseParam($comments, 'xsd:string');
		$params["emailID"] = $this->parseParam($emailID, 'xsd:string');

		$result = $this->call("provisionRealStream", $params);
		$this->logError();
		return $result;
	}
	
	public function getStreamTypes()
	{
		$params = array();
		

		$result = $this->call("getStreamTypes", $params);
		$this->logError();
		return $result;
	}
	
	public function deleteStream($streamId, $deletePrimaryAndBackup)
	{
		$params = array();
		
		$params["streamId"] = $this->parseParam($streamId, 'xsd:int');
		$params["deletePrimaryAndBackup"] = $this->parseParam($deletePrimaryAndBackup, 'xsd:boolean');

		$result = $this->call("deleteStream", $params);
		$this->logError();
		return $result;
	}
	
	public function startStream($port)
	{
		$params = array();
		
		$params["port"] = $this->parseParam($port, 'xsd:int');

		$result = $this->call("startStream", $params);
		$this->logError();
		return $result;
	}
	
	public function stopStream($port)
	{
		$params = array();
		
		$params["port"] = $this->parseParam($port, 'xsd:int');

		$result = $this->call("stopStream", $params);
		$this->logError();
		return $result;
	}
	
	public function startWMSStreamForID($streamID)
	{
		$params = array();
		
		$params["streamID"] = $this->parseParam($streamID, 'xsd:int');

		$result = $this->call("startWMSStreamForID", $params);
		$this->logError();
		return $result;
	}
	
	public function stopWMSStreamForID($streamID)
	{
		$params = array();
		
		$params["streamID"] = $this->parseParam($streamID, 'xsd:int');

		$result = $this->call("stopWMSStreamForID", $params);
		$this->logError();
		return $result;
	}
	
	public function forceDeleteStream($streamId, $deletePrimaryAndBackup)
	{
		$params = array();
		
		$params["streamId"] = $this->parseParam($streamId, 'xsd:int');
		$params["deletePrimaryAndBackup"] = $this->parseParam($deletePrimaryAndBackup, 'xsd:boolean');

		$result = $this->call("forceDeleteStream", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionFlashLiveStream($cpcode, $name, $encoderIP, $backupEncoderIP, $encoderPassword, $emailId, $primaryContact, $secondaryContact, $endDate)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["encoderIP"] = $this->parseParam($encoderIP, 'xsd:string');
		$params["backupEncoderIP"] = $this->parseParam($backupEncoderIP, 'xsd:string');
		$params["encoderPassword"] = $this->parseParam($encoderPassword, 'xsd:string');
		$params["emailId"] = $this->parseParam($emailId, 'xsd:string');
		$params["primaryContact"] = $this->parseParam($primaryContact, 'xsd:string');
		$params["secondaryContact"] = $this->parseParam($secondaryContact, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:dateTime');

		$result = $this->call("provisionFlashLiveStream", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionFlashStream($cpcode, $name, $encoderIP, $encoderPassword, $emailId, $primaryContact, $secondaryContact, $endDate)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["encoderIP"] = $this->parseParam($encoderIP, 'xsd:string');
		$params["encoderPassword"] = $this->parseParam($encoderPassword, 'xsd:string');
		$params["emailId"] = $this->parseParam($emailId, 'xsd:string');
		$params["primaryContact"] = $this->parseParam($primaryContact, 'xsd:string');
		$params["secondaryContact"] = $this->parseParam($secondaryContact, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:dateTime');

		$result = $this->call("provisionFlashStream", $params);
		$this->logError();
		return $result;
	}
	
	public function deleteFlashStreams($cpcode, $streamNames)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["streamNames"] = $this->parseParam($streamNames, 'akastreamsdt:ArrayOfString');

		$result = $this->call("deleteFlashStreams", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionWMSStreamForPingableEncoder($streamName, $primaryEncoderURL, $pingableIP, $primaryEncoderContinent, $backupEncoderURL, $backupPingableIP, $backupEncoderContinent, $bitRate, $trafficLocation, $expectedPeakTraffic, $cpcode, $comments, $emailID)
	{
		$params = array();
		
		$params["streamName"] = $this->parseParam($streamName, 'xsd:string');
		$params["primaryEncoderURL"] = $this->parseParam($primaryEncoderURL, 'xsd:string');
		$params["pingableIP"] = $this->parseParam($pingableIP, 'xsd:string');
		$params["primaryEncoderContinent"] = $this->parseParam($primaryEncoderContinent, 'xsd:string');
		$params["backupEncoderURL"] = $this->parseParam($backupEncoderURL, 'xsd:string');
		$params["backupPingableIP"] = $this->parseParam($backupPingableIP, 'xsd:string');
		$params["backupEncoderContinent"] = $this->parseParam($backupEncoderContinent, 'xsd:string');
		$params["bitRate"] = $this->parseParam($bitRate, 'xsd:int');
		$params["trafficLocation"] = $this->parseParam($trafficLocation, 'xsd:string');
		$params["expectedPeakTraffic"] = $this->parseParam($expectedPeakTraffic, 'xsd:int');
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["comments"] = $this->parseParam($comments, 'xsd:string');
		$params["emailID"] = $this->parseParam($emailID, 'xsd:string');

		$result = $this->call("provisionWMSStreamForPingableEncoder", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionWMSStreamAutoStart($streamName, $primaryEncoderURL, $primaryEncoderContinent, $backupEncoderURL, $backupEncoderContinent, $bitRate, $trafficLocation, $expectedPeakTraffic, $cpcode, $comments, $emailID)
	{
		$params = array();
		
		$params["streamName"] = $this->parseParam($streamName, 'xsd:string');
		$params["primaryEncoderURL"] = $this->parseParam($primaryEncoderURL, 'xsd:string');
		$params["primaryEncoderContinent"] = $this->parseParam($primaryEncoderContinent, 'xsd:string');
		$params["backupEncoderURL"] = $this->parseParam($backupEncoderURL, 'xsd:string');
		$params["backupEncoderContinent"] = $this->parseParam($backupEncoderContinent, 'xsd:string');
		$params["bitRate"] = $this->parseParam($bitRate, 'xsd:int');
		$params["trafficLocation"] = $this->parseParam($trafficLocation, 'xsd:string');
		$params["expectedPeakTraffic"] = $this->parseParam($expectedPeakTraffic, 'xsd:int');
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["comments"] = $this->parseParam($comments, 'xsd:string');
		$params["emailID"] = $this->parseParam($emailID, 'xsd:string');

		$result = $this->call("provisionWMSStreamAutoStart", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionQTAnnounceStream($streamName, $primaryEncoderIP, $primaryEncoderContinent, $backupEncoderIP, $backupEncoderContinent, $bitRate, $trafficLocation, $expectedPeakTraffic, $cpcode, $comments, $emailID)
	{
		$params = array();
		
		$params["streamName"] = $this->parseParam($streamName, 'xsd:string');
		$params["primaryEncoderIP"] = $this->parseParam($primaryEncoderIP, 'xsd:string');
		$params["primaryEncoderContinent"] = $this->parseParam($primaryEncoderContinent, 'xsd:string');
		$params["backupEncoderIP"] = $this->parseParam($backupEncoderIP, 'xsd:string');
		$params["backupEncoderContinent"] = $this->parseParam($backupEncoderContinent, 'xsd:string');
		$params["bitRate"] = $this->parseParam($bitRate, 'xsd:int');
		$params["trafficLocation"] = $this->parseParam($trafficLocation, 'xsd:string');
		$params["expectedPeakTraffic"] = $this->parseParam($expectedPeakTraffic, 'xsd:int');
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["comments"] = $this->parseParam($comments, 'xsd:string');
		$params["emailID"] = $this->parseParam($emailID, 'xsd:string');

		$result = $this->call("provisionQTAnnounceStream", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionQTUnAnnounceStream($streamName, $primaryStreamType, $primaryEncoderACL, $primaryEncoderIP, $primaryEncoderContinent, $backupStreamType, $backupEncoderACL, $backupEncoderIP, $backupEncoderContinent, $bitRate, $trafficLocation, $expectedPeakTraffic, $cpcode, $comments, $emailID)
	{
		$params = array();
		
		$params["streamName"] = $this->parseParam($streamName, 'xsd:string');
		$params["primaryStreamType"] = $this->parseParam($primaryStreamType, 'xsd:string');
		$params["primaryEncoderACL"] = $this->parseParam($primaryEncoderACL, 'xsd:string');
		$params["primaryEncoderIP"] = $this->parseParam($primaryEncoderIP, 'xsd:string');
		$params["primaryEncoderContinent"] = $this->parseParam($primaryEncoderContinent, 'xsd:string');
		$params["backupStreamType"] = $this->parseParam($backupStreamType, 'xsd:string');
		$params["backupEncoderACL"] = $this->parseParam($backupEncoderACL, 'xsd:string');
		$params["backupEncoderIP"] = $this->parseParam($backupEncoderIP, 'xsd:string');
		$params["backupEncoderContinent"] = $this->parseParam($backupEncoderContinent, 'xsd:string');
		$params["bitRate"] = $this->parseParam($bitRate, 'xsd:int');
		$params["trafficLocation"] = $this->parseParam($trafficLocation, 'xsd:string');
		$params["expectedPeakTraffic"] = $this->parseParam($expectedPeakTraffic, 'xsd:int');
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["comments"] = $this->parseParam($comments, 'xsd:string');
		$params["emailID"] = $this->parseParam($emailID, 'xsd:string');

		$result = $this->call("provisionQTUnAnnounceStream", $params);
		$this->logError();
		return $result;
	}
	
	public function modifyWMSEncoderURL($StreamID, $encoderURL)
	{
		$params = array();
		
		$params["StreamID"] = $this->parseParam($StreamID, 'xsd:int');
		$params["encoderURL"] = $this->parseParam($encoderURL, 'xsd:string');

		$result = $this->call("modifyWMSEncoderURL", $params);
		$this->logError();
		return $result;
	}
	
	public function getAllStreamingContacts()
	{
		$params = array();
		

		$result = $this->call("getAllStreamingContacts", $params);
		$this->logError();
		return $result;
	}
	
	public function setStreamingContact($streamId, $primaryContact, $secondaryContact)
	{
		$params = array();
		
		$params["streamId"] = $this->parseParam($streamId, 'xsd:int');
		$params["primaryContact"] = $this->parseParam($primaryContact, 'xsd:string');
		$params["secondaryContact"] = $this->parseParam($secondaryContact, 'xsd:string');

		$result = $this->call("setStreamingContact", $params);
		$this->logError();
		return $result;
	}
	
	public function createLiveFlashConfiguration($cpcode, $name, $hostAliases, $authInfo, $SWFPath)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["hostAliases"] = $this->parseParam($hostAliases, 'akastreamsdt:ArrayOfString');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["SWFPath"] = $this->parseParam($SWFPath, 'xsd:string');

		$result = $this->call("createLiveFlashConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function createLiveFlashMBRConfiguration($cpcode, $name, $hostAliases, $authInfo, $SWFPath, $MBREnabled)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["hostAliases"] = $this->parseParam($hostAliases, 'akastreamsdt:ArrayOfString');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["SWFPath"] = $this->parseParam($SWFPath, 'xsd:string');
		$params["MBREnabled"] = $this->parseParam($MBREnabled, 'xsd:boolean');

		$result = $this->call("createLiveFlashMBRConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function editLiveFlashConfiguration($cpcode, $name, $hostAliases, $authInfo, $SWFPath)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["hostAliases"] = $this->parseParam($hostAliases, 'akastreamsdt:ArrayOfString');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["SWFPath"] = $this->parseParam($SWFPath, 'xsd:string');

		$result = $this->call("editLiveFlashConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function editLiveFlashMBRConfiguration($cpcode, $name, $hostAliases, $authInfo, $SWFPath, $MBREnabled)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["hostAliases"] = $this->parseParam($hostAliases, 'akastreamsdt:ArrayOfString');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["SWFPath"] = $this->parseParam($SWFPath, 'xsd:string');
		$params["MBREnabled"] = $this->parseParam($MBREnabled, 'xsd:boolean');

		$result = $this->call("editLiveFlashMBRConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveFlashConfiguration($cpcode)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');

		$result = $this->call("getLiveFlashConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function getLiveFlashMBRConfiguration($cpcode)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');

		$result = $this->call("getLiveFlashMBRConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function deleteLiveFlashConfiguration($cpcode)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');

		$result = $this->call("deleteLiveFlashConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function createVODFlashConfiguration($cpcode, $name, $vpaths, $hostAliases, $appNameAliases, $authInfo, $SWFPath)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["vpaths"] = $this->parseParam($vpaths, 'akastreamsdt:ArrayOfFlashVirtualPath');
		$params["hostAliases"] = $this->parseParam($hostAliases, 'akastreamsdt:ArrayOfString');
		$params["appNameAliases"] = $this->parseParam($appNameAliases, 'akastreamsdt:ArrayOfString');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["SWFPath"] = $this->parseParam($SWFPath, 'xsd:string');

		$result = $this->call("createVODFlashConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function editVODFlashConfiguration($cpcode, $name, $vpaths, $hostAliases, $appNameAliases, $authInfo, $SWFPath)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["vpaths"] = $this->parseParam($vpaths, 'akastreamsdt:ArrayOfFlashVirtualPath');
		$params["hostAliases"] = $this->parseParam($hostAliases, 'akastreamsdt:ArrayOfString');
		$params["appNameAliases"] = $this->parseParam($appNameAliases, 'akastreamsdt:ArrayOfString');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["SWFPath"] = $this->parseParam($SWFPath, 'xsd:string');

		$result = $this->call("editVODFlashConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function getVODFlashConfiguration($cpcode)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');

		$result = $this->call("getVODFlashConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function deleteVODFlashConfiguration($cpcode)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');

		$result = $this->call("deleteVODFlashConfiguration", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionFlashLiveDynamicAuthStream($cpcode, $name, $encoderIP, $backupEncoderIP, $encoderPassword, $authInfo, $emailId, $primaryContact, $secondaryContact, $endDate, $dynamic)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["encoderIP"] = $this->parseParam($encoderIP, 'xsd:string');
		$params["backupEncoderIP"] = $this->parseParam($backupEncoderIP, 'xsd:string');
		$params["encoderPassword"] = $this->parseParam($encoderPassword, 'xsd:string');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["emailId"] = $this->parseParam($emailId, 'xsd:string');
		$params["primaryContact"] = $this->parseParam($primaryContact, 'xsd:string');
		$params["secondaryContact"] = $this->parseParam($secondaryContact, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:dateTime');
		$params["dynamic"] = $this->parseParam($dynamic, 'xsd:boolean');

		$result = $this->call("provisionFlashLiveDynamicAuthStream", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionFlashLiveAuthStream($cpcode, $name, $encoderIP, $backupEncoderIP, $encoderPassword, $authInfo, $emailId, $primaryContact, $secondaryContact, $endDate)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["encoderIP"] = $this->parseParam($encoderIP, 'xsd:string');
		$params["backupEncoderIP"] = $this->parseParam($backupEncoderIP, 'xsd:string');
		$params["encoderPassword"] = $this->parseParam($encoderPassword, 'xsd:string');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["emailId"] = $this->parseParam($emailId, 'xsd:string');
		$params["primaryContact"] = $this->parseParam($primaryContact, 'xsd:string');
		$params["secondaryContact"] = $this->parseParam($secondaryContact, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:dateTime');

		$result = $this->call("provisionFlashLiveAuthStream", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionFlashAuthStream($cpcode, $name, $encoderIP, $encoderPassword, $authInfo, $emailId, $primaryContact, $secondaryContact, $endDate)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["encoderIP"] = $this->parseParam($encoderIP, 'xsd:string');
		$params["encoderPassword"] = $this->parseParam($encoderPassword, 'xsd:string');
		$params["authInfo"] = $this->parseParam($authInfo, 'akastreamsdt:AuthInfo');
		$params["emailId"] = $this->parseParam($emailId, 'xsd:string');
		$params["primaryContact"] = $this->parseParam($primaryContact, 'xsd:string');
		$params["secondaryContact"] = $this->parseParam($secondaryContact, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:dateTime');

		$result = $this->call("provisionFlashAuthStream", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionFlashLiveDynamicStream($cpcode, $name, $encoderIP, $backupEncoderIP, $encoderPassword, $emailId, $primaryContact, $secondaryContact, $endDate, $dynamic)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["name"] = $this->parseParam($name, 'xsd:string');
		$params["encoderIP"] = $this->parseParam($encoderIP, 'xsd:string');
		$params["backupEncoderIP"] = $this->parseParam($backupEncoderIP, 'xsd:string');
		$params["encoderPassword"] = $this->parseParam($encoderPassword, 'xsd:string');
		$params["emailId"] = $this->parseParam($emailId, 'xsd:string');
		$params["primaryContact"] = $this->parseParam($primaryContact, 'xsd:string');
		$params["secondaryContact"] = $this->parseParam($secondaryContact, 'xsd:string');
		$params["endDate"] = $this->parseParam($endDate, 'xsd:dateTime');
		$params["dynamic"] = $this->parseParam($dynamic, 'xsd:boolean');

		$result = $this->call("provisionFlashLiveDynamicStream", $params);
		$this->logError();
		return $result;
	}
	
	public function getFlashStreams($cpcode)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');

		$result = $this->call("getFlashStreams", $params);
		$this->logError();
		return $result;
	}
	
	public function getFlashDynamicStreams($cpcode)
	{
		$params = array();
		
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');

		$result = $this->call("getFlashDynamicStreams", $params);
		$this->logError();
		return $result;
	}
	
	public function provisionWMSPushStream($streamName, $primaryPingableIP, $primaryEncoderContinent, $backupPingableIP, $backupEncoderContinent, $bitRate, $trafficLocation, $expectedPeakTraffic, $cpcode, $comments, $emailID)
	{
		$params = array();
		
		$params["streamName"] = $this->parseParam($streamName, 'xsd:string');
		$params["primaryPingableIP"] = $this->parseParam($primaryPingableIP, 'xsd:string');
		$params["primaryEncoderContinent"] = $this->parseParam($primaryEncoderContinent, 'xsd:string');
		$params["backupPingableIP"] = $this->parseParam($backupPingableIP, 'xsd:string');
		$params["backupEncoderContinent"] = $this->parseParam($backupEncoderContinent, 'xsd:string');
		$params["bitRate"] = $this->parseParam($bitRate, 'xsd:int');
		$params["trafficLocation"] = $this->parseParam($trafficLocation, 'xsd:string');
		$params["expectedPeakTraffic"] = $this->parseParam($expectedPeakTraffic, 'xsd:int');
		$params["cpcode"] = $this->parseParam($cpcode, 'xsd:int');
		$params["comments"] = $this->parseParam($comments, 'xsd:string');
		$params["emailID"] = $this->parseParam($emailID, 'xsd:string');

		$result = $this->call("provisionWMSPushStream", $params);
		$this->logError();
		return $result;
	}
	
}		
	
