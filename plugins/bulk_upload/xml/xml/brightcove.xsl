<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:output method="xml" indent="yes" version="1.0" />

<xsl:template match="/">

	<mrss xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" >
		<channel>
		 	

    	<xsl:for-each select="publisher-upload-manifest/asset[@type='VIDEO_FULL' or @type='FLV_BUMPER' or @type='FLV_FULL']">
	    	<item>
	
				<!--xsl:value-of select="../title[ @video-full-refid = 'airport_gibbous_full.flv-52aaedfc6f568c82df324050f' ]/@name"/-->
	
				<xsl:call-template name="insidetitle">
	  				<xsl:with-param name="TitleRef" select="@refid"/>
	  				<xsl:with-param name="FileName" select="@filename"/>
				</xsl:call-template>
    		</item>
    	</xsl:for-each>

		</channel>
	</mrss>
  
</xsl:template>



<xsl:template name="insidetitle">
	<xsl:param name="TitleRef" select="'Undef'"/>
	<xsl:param name="FileName" select="'Undef'"/>
	<!--title><xsl:value-of select="$TitleRef"/></title-->

	<!--xsl:for-each select="/publisher-upload-manifest/title[ @video-full-refid = $TitleRef ]"-->



	

	<xsl:for-each select="/publisher-upload-manifest/title">

		<xsl:if test="./rendition-refid = string($TitleRef)">
			<action>add</action>
			<!--licenseType>-1</licenseType-->
			<!--userId>test1</userId-->
			<!--partnerData>my own data</partnerData-->
			<type>1</type>
			<name><xsl:value-of select="@name"/></name>
			<description><xsl:value-of select="./long-description"/></description>
			<tags>
				<xsl:for-each select="./tag">
					<tag><xsl:value-of select="."/></tag>
				</xsl:for-each>
			</tags>
			<!--accessControl>Roni</accessControl-->
			<!--ingestionProfile>Roni_Conversion</ingestionProfile-->
			<startDate><xsl:value-of select="@start-date"/></startDate>
			<endDate><xsl:value-of select="@end-date"/></endDate>
			<media>
				<mediaType>1</mediaType> 
			</media>
			<content>
				<xsl:attribute name="id">content1</xsl:attribute>
				<xsl:attribute name="flavorParamsId">0</xsl:attribute>
				<localFileContentResource>
				<xsl:attribute name="filePath"><xsl:value-of select="$FileName"/></xsl:attribute>
					<!--fileSize>2743980</fileSize-->
				</localFileContentResource>
			</content>
		</xsl:if>

	</xsl:for-each>

	
</xsl:template>


  <xsl:template name="FormatDate">
    <xsl:param name="DateTime" />
    <!-- new date format 2006-01-14T08:55:22 -->
    <xsl:variable name="mo">
      <xsl:value-of select="substring($DateTime,1,3)" />
    </xsl:variable>
    <xsl:variable name="day-temp">
      <xsl:value-of select="substring-after($DateTime,'-')" />
    </xsl:variable>
    <xsl:variable name="day">
      <xsl:value-of select="substring-before($day-temp,'-')" />
    </xsl:variable>
    <xsl:variable name="year-temp">
      <xsl:value-of select="substring-after($day-temp,'-')" />
    </xsl:variable>
    <xsl:variable name="year">
      <xsl:value-of select="substring($year-temp,1,4)" />
    </xsl:variable>
    <xsl:variable name="time">
      <xsl:value-of select="substring-after($year-temp,' ')" />
    </xsl:variable>
    <xsl:variable name="hh">
      <xsl:value-of select="substring($time,1,2)" />
    </xsl:variable>
    <xsl:variable name="mm">
      <xsl:value-of select="substring($time,4,2)" />
    </xsl:variable>
    <xsl:variable name="ss">
      <xsl:value-of select="substring($time,7,2)" />
    </xsl:variable>
    <xsl:value-of select="$year"/>
    <xsl:value-of select="'-'"/>
    <xsl:choose>
      <xsl:when test="$mo = 'Jan'">01</xsl:when>
      <xsl:when test="$mo = 'Feb'">02</xsl:when>
      <xsl:when test="$mo = 'Mar'">03</xsl:when>
      <xsl:when test="$mo = 'Apr'">04</xsl:when>
      <xsl:when test="$mo = 'May'">05</xsl:when>
      <xsl:when test="$mo = 'Jun'">06</xsl:when>
      <xsl:when test="$mo = 'Jul'">07</xsl:when>
      <xsl:when test="$mo = 'Aug'">08</xsl:when>
      <xsl:when test="$mo = 'Sep'">09</xsl:when>
      <xsl:when test="$mo = 'Oct'">10</xsl:when>
      <xsl:when test="$mo = 'Nov'">11</xsl:when>
      <xsl:when test="$mo = 'Dec'">12</xsl:when>
    </xsl:choose>
    <xsl:value-of select="'-'"/>
    <xsl:if test="(string-length($day) &lt; 2)">
      <xsl:value-of select="0"/>
    </xsl:if>
    <xsl:value-of select="$day"/>
    <xsl:value-of select="'T'"/>
    <xsl:value-of select="$hh"/>
    <xsl:value-of select="':'"/>
    <xsl:value-of select="$mm"/>
    <xsl:value-of select="':'"/>
    <xsl:value-of select="$ss"/>
  </xsl:template>


</xsl:stylesheet>
