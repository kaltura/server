<?php
$generalError = array(
	KalturaErrors::INTERNAL_SERVERL_ERROR => array(),
	KalturaErrors::MISSING_KS => array(),
	KalturaErrors::INVALID_KS => array("%KS%", "%KS_ERROR_CODE%", "%KS_ERROR_DESCRIPTION%"),
	KalturaErrors::SERVICE_NOT_SPECIFIED => array(),
	KalturaErrors::SERVICE_DOES_NOT_EXISTS => array("%SERVICE_NAME%"),
	KalturaErrors::ACTION_NOT_SPECIFIED => array(),
	KalturaErrors::ACTION_DOES_NOT_EXISTS => array("%ACTION_NAME%", "%SERVICE_NAME%"),
	KalturaErrors::MISSING_MANDATORY_PARAMETER => array("%PARAMETER_NAME%"),
	KalturaErrors::INVALID_OBJECT_TYPE => array("%OBJECT_TYPE%"),
	KalturaErrors::INVALID_ENUM_VALUE => array("%GIVEN_VALUE%", "%PARAMETER_NAME%", "%ENUM_TYPE"),
	KalturaErrors::INVALID_PARTNER_ID => array("%PARTNER_ID%"),
	KalturaErrors::INVALID_SERVICE_CONFIGURATION => array("%SERVICE_NAME%", "%ACTION_NAME%"),
	KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL => array("%PROPERTY_NAME%"),
	KalturaErrors::PROPERTY_VALIDATION_MIN_LENGTH => array("%PROPERTY_NAME%", "%MININUM_LENGTH%"),
	KalturaErrors::PROPERTY_VALIDATION_MAX_LENGTH => array("%PROPERTY_NAME%", "%MAXIMUM_LENGTH%"),
	KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE => array("%PROPERTY_NAME%"),
	KalturaErrors::INVALID_USER_ID => array()
); 
?>
<h2>Request/Response structure</h2>

<h3>Request Structure</h3>
<p>
Kaltura API requests are standard HTTP POST/GET, URL encoded requests targeted to a specific API method. Each API method location is concatenated out of base URI, service and action identifiers strings, according to the following format:
</p>
<pre>
http://www.kaltura.com/api_v3/?service=[SERVICENAME]&action=[ACTIONNAME]
</pre>
<p>
where[SERVICENAME] represents the specific service and [ACTIONNAME] represent an action to be applied on the specific service.
</p>
<p>
For example, a request to activate the “list” action of the “media” service should be posted to the following URL:
</p>
<pre>
http://www.kaltura.com/api_v3/?service=media&action=list
</pre>

<h3>Request Input Parameters</h3> 
<p>
Each API method receives a different set of input parameters. For all request types:
</p>
<ul>
	<li>Input parameters should be sent as a standard URL encoded key-value string.</li> 
	<li>When input parameter is an object, it must be flattened to pairs of ObjectName:Param keys.</li>
	<li>When date value is being passed as input parameter it should follow the YYYY-MM-DD HH:MM:SS format.</li>
</ul>
<p>
Example:
</p>
<pre>
id=abc12&name=name%20with%20spaces&entry:tag=mytag&entry:description=mydesc&cdate=2001-02-04%2003:11:32
</pre>
<p>
Within this example the following parameters are being URL encoded and passed as API input parameters:
</p>
<pre>
id = “abc”
name = “name with spaces”
entry {
	tag = “mytag”,
	description = “mydesc”	
}
cdate = “2001-02-04 03:11:32”
</pre>
<h3>Response Structure</h3>
<p>
Kaltura API response content is gziped by default (assuming client specifies it supports gzip).
Every response content is wrapped in an XML formatted container object, holding a &lt;result&gt; element.
</p>  

<h3>Successful Response</h3>
<p>
When Kaltura server executes an API request successfully, the &lt;result&gt; element within response’s body will hold a structure of parameters relevant for the specific request. Response’s &lt;result&gt; structure could be simple, holding one or few parameters, or complex, holding many parameters including nested objects.
</p>
Example of Successful Response:

<pre>
<?php 
echo htmlentities('
<?xml version="1.0" encoding="utf-8" ?> 
<xml>
  <result>
    <objectType>KalturaMediaEntry</objectType>
	<id>vcnp8h76m8</id>
	<name>Demo Video</name>
	<description/>
	<partnerId>1</partnerId>
	<userId/>
	<tags/>
	<adminTags>demo</adminTags>
	<status>2</status>
	<type>1</type>
	<createdAt>1240844664</createdAt> 
  </result>
  <executionTime>0.08957796096802</executionTime> 
</xml>
');
?>
</pre>

<h3>Error Response</h3>
<p>
When Kaltura server fails to execute a specific API request, an &lt;error&gt; element will be nested within response’s &lt;result&gt; element. The &lt;error&gt; element will hold information on response error code and the equivalent error message.
</p>
<p>
The following table lists few possible general API error codes and their equivalent messages:
</p>

<table>
<tr>
	<th>Error Code</th>
	<th>Error message</th>
</tr>
<?php $odd = true; ?>
<?php foreach($generalError as $error => $errorParams): ?>
<tr class="<?php echo ($odd) ? "odd" : ""; ?>">
<?php
	$ex = new KalturaAPIException(null); 
	call_user_func_array(array($ex, 'KalturaAPIException'), array_merge(array($error), $errorParams));  
?>
	<td><?php echo $ex->getCode(); ?></td>
	<td><?php echo $ex->getMessage(); ?></td>
</tr>
<?php $odd = !$odd; ?>
<?php endforeach; ?>
</table>

<p>
Example of an error Response
</p>
<pre>
<?php 
echo htmlentities('
<?xml version="1.0" encoding="utf-8" ?> 
<xml>
  <result>
    <error>
      <code>MISSING_KS</code>
      <message>Missing KS. Session not established</message>
    </error>
  </result>
  <executionTime>0.011207971573</executionTime>
</xml>
');
?>

</pre>
