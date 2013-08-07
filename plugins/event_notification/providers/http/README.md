---
HTTP notification - Kaltura server plugin
---

## Setup

HTTP notification template configuration:

- URL - Defines the URL that will accept the notification.
- Data type - the type of sent data
 - **Free Text** - Sends free text as is in the HTTP POST data, could be used to send WSDL/SOAP envelop or other REST XML data.
 - **Fields** - Sends all content parameters as POST fields. 
 The text may contain place holders to the content parameter.
 For example, to use content parameter `entry_id` use `{entry_id}` in the text.  
 - **API Object** - Sends a single field called `data` with serialized [KalturaHttpNotification](http://www.kaltura.com/api_v3/testmeDoc/?object=KalturaHttpNotification) object.
 Make sure that you choose the object type that matches the object that triggers the event.
 The serialized data could be formatted to the following formats:
     - PHP - compatible with PHP5 client library only.
     - JSON -compatible with javascript. 
     - XML - compatible with all Kaltura client libraries except for PHP5.
 
 **Note:** *PHP 5.3* and *PHP Zend Framework* client libraries use the XML format. 

## Integration

### API - REST / WSDL / SOAP
In case your server already support accepting notifications using defined format, use the free text option to send the POST data as expected by your server.

### Working without client library
You might want to implement simple reaction to Kaltura events by fetching only the event content parameters as POST fields. This option assumes that the defined content parameters already supplies all the information you need.

### Working with Kaltura objects
In order to use Kaltura object you should use [Kaltura client libraries](http://www.kaltura.com/api_v3/testme/client-libs.php). The accepting end point should translate the serialized object that sent as POST field called `data` using the client library.
 
## Code samples

### *PHP5*

#### Free text
The entire POST raw data could be read directly from the standard input. 

	$rawPostData = file_get_contents("php://input");

#### Fields
To read all fields from the POST data as array simply use `$_POST` global variable.

    $fields = $_POST;

#### API Object
Make sure to include the relevant client library and unserialize the the posted `data` field.
The notification template must be defined to send API Object using PHP format.
 

    require_once('lib/KalturaClient.php');
    require_once('lib/KalturaPlugins/KalturaHttpNotificationClientPlugin.php');
    
    $object = unserialize($_POST['data']);

**Note:** Unserializing the object without including the client libraries will result an `stdClass` object.

### *Java*

#### Free text
The entire POST raw data could be read directly from the request `BufferedReader` object. 

	<%@ page import = "java.io.BufferedReader" %>
	<%
	BufferedReader reader = request.getReader();
	String rawSata = "";
	String line = reader.readLine();
	while (line != null){
		rawSata += new String(line.getBytes("ISO-8859-1"), "UTF-8");
		rawSata += "\n";
		line = reader.readLine();
	}
	reader.reset();
	out.println(rawSata);
	%>

#### Fields
To read all fields go over all parameters in the request object.

	<%@ page import = "java.util.Enumeration" %>
	<%
	Enumeration<String> parameters = request.getParameterNames();
	while(parameters.hasMoreElements()){
		String parameter = parameters.nextElement();
		String value = new String(request.getParameter(parameter).getBytes("ISO-8859-1"), "UTF-8");
		out.println(parameter + " => " + value);
	}
	%>


#### API Object
Make sure to include the relevant client library and unserialize the the posted `data` field.
The notification template must be defined to send API Object using XML format.
 
	<%@ page import = "java.util.Map.Entry" %>
	<%@ page import = "java.util.HashMap" %>
	<%@ page import = "org.w3c.dom.Element" %>
	<%@ page import = "com.kaltura.client.utils.ParseUtils" %>
	<%@ page import = "com.kaltura.client.utils.XmlUtils" %>
	<%@ page import = "com.kaltura.client.types.KalturaHttpNotification" %>
	<%
	String xmlData = request.getParameter("data");
	Element xmlElement = XmlUtils.parseXml(xmlData);
	KalturaHttpNotification httpNotification = ParseUtils.parseObject(KalturaHttpNotification.class, xmlElement);
	HashMap<String, String> params = httpNotification.toParams();
	for (Entry<String, String> itr : params.entrySet()) {
		out.println(itr.getKey() + " => " + itr.getValue());
	}
	%>

