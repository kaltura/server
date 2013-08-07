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