<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
	<xsl:output omit-xml-declaration="no" method="xml" />
	
	<xsl:template match="item">
		<video xmlns="urn:schemas-microsoft-com:msnvideo:catalog">
		  <uuid></uuid>
		  <providerId>1_f_101118_nflonfox_curt</providerId>
		  <csId>Fox Sports</csId>
		  <source>Fox_Franks picks_Curts pick</source>
		  <pageGroup></pageGroup>
		  <title><xsl:value-of select="title"/></title>
		  <description><xsl:value-of select="description"/></description>
		  <durationSecs><xsl:value-of select="floor(sum(media/duration) div 1000)"/></durationSecs>
		  <activeEndDate>2012-11-17T05:00:00Z</activeEndDate>
		  <searchableEndDate>2012-11-17T05:00:00Z</searchableEndDate>
		  <archiveEndDate>2012-11-17T05:00:00Z</archiveEndDate>
		  <tags>
		    <tag market="us" namespace="MSNVideo_Cat">Franks picks_Curts pick</tag>
		    <tag market="us" namespace="MSNVideo_Top">Fox Sports</tag>
		    <tag market="us" namespace="MSNVideo_Top_Cat">Fox Sports_Franks picks_Curts pick</tag>
		    <tag market="us" namespace="Public">Fox Sports</tag>
		  </tags>
		  <videoFiles>
		    <videoFile formatCode="1001">
		      <uri>http://msn-pickup.foxsports.com/1_f_101118_nflonfox_curt.mov</uri>
		    </videoFile>
		    <videoFile formatCode="1003">
		      <uri>http://msn-pickup.foxsports.com/1_f_101118_nflonfox_curt_msn.flv</uri>
		    </videoFile>
		    <videoFile formatCode="1002">
		      <uri>http://msn-pickup.foxsports.com/1_f_101118_nflonfox_curt.wmv</uri>
		    </videoFile>
		  </videoFiles>
		  <files>
		    <file formatCode="2009">
		      <uri>http://o.legacy.foxsports.com/oid/11047980_1.jpg</uri>
		    </file>
		  </files>
		  <extendedXml>
		    <relatedLinks>
		      <link url="http://www.foxsports.com">FOXSports.com on MSN</link>
		      <link url="http://msn.foxsports.com/other/page/fox-flash">Watch latest sports news and highlights</link>
		      <link url="http://msn.foxsports.com/video">More FOXSports.com video</link>
		    </relatedLinks>
		  </extendedXml>
		</video>
	</xsl:template>
</xsl:stylesheet>
