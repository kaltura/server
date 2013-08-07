<%@ page import = "java.util.Enumeration" %>
<%
Enumeration<String> parameters = request.getParameterNames();
while(parameters.hasMoreElements()){
	String parameter = parameters.nextElement();
	String value = new String(request.getParameter(parameter).getBytes("ISO-8859-1"), "UTF-8");
	out.println(parameter + " => " + value);
}
%>