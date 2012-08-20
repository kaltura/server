<?php
include_once "../../infra/general/XmlRpcWrap.php";
include_once "../../plugins/inlet_armada/lib/InletAPIWrap.php";

//include_once "XmlRpcWrap.php";
//include_once "InletAPIWrap.php";

$post_string = '<?xml version="1.0"?>
<methodCall>
</methodCall>';

$rpc_response1 = '<?xml version="1.0"?>
<methodResponse>
  <params>
    <param>
      <value>
        <struct>
          <member>
            <name>session_id</name>
            <value>
              <string>user2-772817899</string>
            </value>
          </member>
          <member>
            <name>user_permissions</name>
            <value>
              <i4>1</i4>
            </value>
          </member>
          <member>
            <name>response</name>
            <value>
              <string>ok</string>
            </value>
          </member>
        </struct>
      </value>
    </param>
  </params>
</methodResponse>';
/*
$fHd=fopen("nodeList.log","r");
$str=fread($fHd,1000000);
	$rv=xmlrpc_parse_response($str);
	print_r($rv);
return; 
*/
$serverUrl="http://encode-stage.alldigital.com:8060/server";
$serverUrl="http://192.168.69.75:8060/server";
$serverUrl="http://199.88.61.76:8060/server";
$serverUrl="http://192.168.251.121:8060/server";
$inlet = new InletAPIWrap($serverUrl);
	print_r($inlet);
	$rvObj=new XmlRpcData;
	$rv=$inlet->userLogon("admin", "password",$rvObj);
//$rv=$inlet->userLogon("user2", "user2011",$rvObj);
	echo "userLogon->rv(".print_r($rv,1)."), rvOvj(".print_r($rvObj,1).")";
	if(!$rv)
		return;
/*
	$rv=$inlet->jobListCompleted(2, "20110308", $rvObj);
	echo "after jobListCompleted($rv)\n";
	print_r($rvObj);
	return;
*/
	$rv=$inlet->templateGroupList($rvObj);
	echo "templateGroupList->rv(".print_r($rv,1)."), rvOvj(".print_r($rvObj,1).")";
	$rv=$inlet->userLogoff();
	echo "userLogoff->rv(".print_r($rv,1)."), rvOvj(".print_r($rvObj,1).")";
	return;
	
	$rv=$inlet->jobAdd(			
			516,			// job template id
			'c:\tmp\try1.mp4',		// String job_source_file, 
			'f:\output\zzz.mp4',		// String job_destination_file, 
			5,				// Int priority, 
			"try", array(),"",
			$rvObj);						// String description, 
	print_r($rv);
echo "=========\n";
	print_r($rvObj);
	$jobId = $rvObj->job_id;

echo "=========\n";
	while (1) {
		sleep(2);
		$rv=$inlet->jobList(array($jobId),$rvObj);
	echo "rv(".print_r($rv,1)."), rvOvj(".print_r($rvObj,1).")";
		if($rvObj->job_list[0]->job_state==InletArmadaJobStatus::CompletedSuccess
		|| $rvObj->job_list[0]->job_state==InletArmadaJobStatus::CompletedUnknown
		|| $rvObj->job_list[0]->job_state==InletArmadaJobStatus::CompletedFailure){
			break;
		}
	}

/*
	$rv=$inlet->jobListActive();
	print_r($rv);
*/
	$rv=$inlet->userLogoff();
	echo "after logoff";
	print_r($rv);
	return;



?>
