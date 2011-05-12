<?php
class XmlRpcData {};
/***********************
 *	function xmlrpc_build_method_call($name, array $params=null)
 */
function xmlrpc_build_method_call($name, array $params=null)
{
$emptyMethodXmlStr = '<?xml version="1.0"?>
<methodCall>
</methodCall>';
//	$xml = simplexml_load_string($emptyMethodXmlStr);
//	var_dump($xml);
//	echo $name;
	$xml = new SimpleXMLElement($emptyMethodXmlStr);
	$rpcMthd=$xml->addChild('methodName',$name);
	if($params) {
		$rpcParams=$xml->addChild("params");
		foreach ($params as $paramName=>$param){
			$rpcParam=$rpcParams->addChild("param");
			xmlrpc_build_value($rpcParam, $param);
		}
	}
//	var_dump($xml);
	return $xml->asXML();
}

/***********************
 *	function xmlrpc_build_value($parent, $val, $type="string")
 */
function xmlrpc_build_value($parent, $value, $type="string")
{
	if(is_array($value)) {
		$val = $value[0];
		$type=$value[1];
	}
	else
		$val = $value;

	$rpcValue=$parent->addChild("value");	
	if(is_array($val)){
		$rpcType=$rpcValue->addChild($type);
		switch($type) {
		case 'array':
			$rpcAux=$rpcType->AddChild("data");
			foreach($val as $member){
				xmlrpc_build_value($rpcAux, $member);
			}
			break;
		case 'struct':
			foreach($val as $memberName=>$member){
				$rpcMember=$rpcType->AddChild("member");
				$rpcMember->AddChild("name", $memberName);
				xmlrpc_build_value($rpcMember, $member);
			}
			break;
		}
	}
	else{
		$rpcType=$rpcValue->addChild($type, $val);
	}
//	$rpcType->addChild($val);
}

/***********************
 *	function xmlrpc_parse_response($rpc_response_str)
 */
function xmlrpc_parse_response($rpc_response_str)
{
$response_obj=new XmlRpcData;
//	$xml = new SimpleXMLElement($rpc_response_str);
//echo $rpc_response_str;
	try {
		$xml = new SimpleXMLElement($rpc_response_str);
	}
	catch(Exception $e){
		$response_obj->description="empty response";
		$response_obj->response="failed";
		return $response_obj;
	}
	if($xml->params[0]){
		if($xml->params[0]->param[0]){
			$response_obj = xmlrpc_parse_struct($xml->params[0]->param[0]->value[0]->struct[0]);
		}
		else{
		;
			echo "--------------\n";
			print_r($xml->params[0]);
			echo "^^^^^^^^^^^^^^\n";
		}
	}
	else if($xml->fault[0])
		$response_obj = xmlrpc_parse_struct($xml->fault[0]->value[0]->struct[0]);
	return $response_obj;
}

/***********************
 *	function xmlrpc_parse_struct($sxe)
 */
function xmlrpc_parse_struct($sxe)
{
//return;
//echo "\nin->".__METHOD__."\n";
$rpcStruct=new XmlRpcData;
	foreach($sxe as $member)
	{
//print_r($member);
		$key = (string)$member->name[0];
		if($member->value[0]->string[0]){
			$rpcStruct->$key=(string)$member->value[0]->string[0];
		}
		else if($member->value[0]->int[0]){
			$rpcStruct->$key=(int)$member->value[0]->int[0];
		}
		else if($member->value[0]->i4[0]){
			$rpcStruct->$key=(int)$member->value[0]->i4[0];
		}
		else if($member->value[0]->boolean[0]){
			$rpcStruct->$key=(int)$member->value[0]->boolean[0];
		}
		else if($member->value[0]->double[0]){
			$rpcStruct->$key=(float)$member->value[0]->double[0];
		}
		else if($member->value[0]->base64[0]){
			$rpcStruct->$key=(int)$member->value[0]->base64[0];
		}
		else if($member->value[0]->struct[0]){
//echo "\n------$key-------\n";
			$rpcStruct->$key=xmlrpc_parse_struct($member->value[0]->struct[0]);
//echo "\n=================\n";
		}
		else if($member->value[0]->array[0]){
//echo "\n------$key-------\n";
			$rpcStruct->$key=xmlrpc_parse_array($member->value[0]->array[0]->data);
//print_r($rpcStruct->$key);
//echo "\n=================\n";
		}
		else if($member->value[0]->{'dateTime.iso8601'}[0]){
//		echo "key($key)\n";
			$rpcStruct->$key=(string)$member->value[0]->{'dateTime.iso8601'}[0];
		}
		else {
			$rpcStruct->$key="";
		}
//		print_r($member);
//		echo "\nbaaaaaaaaaaa\n";
	}
//	print_r($rpcStruct);
	return $rpcStruct;
//	print_r($xml->params[0]->param[0]->value[0]->struct[0]);
}

/***********************
 *	function xmlrpc_parse_array($sxe)
 */
function xmlrpc_parse_array($sxe)
{
//echo "\nin->".__METHOD__."\n";
$rpcArr=array();
	if($sxe==null)
		return $rpcArr;
//	print_r($sxe);
	foreach($sxe->value as $member)
	{
		if($member){
//print_r($member->value);
			if($member->struct[0]) {
//print_r($member->value[0]->struct);
				$rpcArr[]=xmlrpc_parse_struct($member->struct[0]);
			}
		}
	}
	return $rpcArr;
} 

/***********************
 *
 */
function xmlrpc_call_method($url, $name, array $params=null)
{/*
echo "name($name)\n";
if($params) {
echo "params(";
print_r($params);
}
echo ")";
*/
	$xml_rpc_str = xmlrpc_build_method_call($name, $params);
	$ch = curl_init();

//	var_dump($xml_rpc_str);

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$xml_rpc_str);

// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$server_rv = curl_exec ($ch);
//	echo $server_rv;
	curl_close ($ch);

	return xmlrpc_parse_response($server_rv);
//return;
}
