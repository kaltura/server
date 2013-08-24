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