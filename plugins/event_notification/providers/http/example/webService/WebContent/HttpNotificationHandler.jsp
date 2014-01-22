<%@page import="java.io.BufferedReader"%>
<%@page import="org.w3c.dom.Element"%>
<%@page import="com.kaltura.client.utils.XmlUtils"%>
<%@page import="lib.Kaltura.HttpNotificationHandler"%>
<%@page import="com.kaltura.client.types.KalturaHttpNotification"%>
<%@page import="com.kaltura.client.utils.ParseUtils"%>
<%@page import="lib.Kaltura.RequestHandler"%>
<%

BufferedReader reader = request.getReader();
StringBuffer sb = new StringBuffer("");
String line;
while ((line = reader.readLine()) != null){
	sb.append(new String(line.getBytes("ISO-8859-1"), "UTF-8"));
}
reader.reset();

String xml = sb.toString();
String signature = request.getHeader("x-kaltura-signature");
RequestHandler.validateSignature(xml, SessionConfig.KALTURA_ADMIN_SECRET, signature);

int dataOffset = xml.indexOf("data=");
if(dataOffset < 0) {
	System.out.println("Couldn't find data");
}

String xmlData = xml.substring(5);
Element xmlElement = XmlUtils.parseXml(xmlData);
KalturaHttpNotification httpNotification = ParseUtils.parseObject(KalturaHttpNotification.class, xmlElement);

HttpNotificationHandler handler = new HttpNotificationHandler();
handler.handle(httpNotification);
handler.finalize();

%>