<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:output method="xml" indent="yes" version="1.0" />

<xsl:template match="/">

	<mrss xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" >
		<channel>
		 	

    	<xsl:for-each select="publisher-upload-manifest/title">
	    	
	    	<item>
				<!--xsl:for-each select="/publisher-upload-manifest/title[ @video-full-refid = $AssetRef ]"-->
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
				<startDate>
					<xsl:call-template name="FormatDate">
						<xsl:with-param name="DateTime" select="@start-date"/>
					</xsl:call-template>
				</startDate>
				<endDate>
					<xsl:call-template name="FormatDate">
						<xsl:with-param name="DateTime" select="@end-date"/>
					</xsl:call-template>
				</endDate>
				<!--xsl:value-of select="../title[ @video-full-refid = 'airport_gibbous_full.flv-52aaedfc6f568c82df324050f' ]/@name"/-->
				<media>
					<mediaType>1</mediaType> 
				</media>
				
				<xsl:if test="@video-full-refid">
					<xsl:call-template name="HandleVideoAsset">
		  				<xsl:with-param name="TitleRefIdField" select="@refid"/>
		  				<xsl:with-param name="TitleRef" select="@video-full-refid"/>
					</xsl:call-template>
			    </xsl:if>
			    
			    <xsl:variable name="TitleRefId">
      				<xsl:value-of select="@refid" />
    			</xsl:variable>
				
				<xsl:for-each select="./rendition-refid">
					<xsl:call-template name="HandleVideoAsset">
		  				<xsl:with-param name="TitleRef" select="."/>
		  				<xsl:with-param name="TitleRefIdField" select="$TitleRefId"/>
					</xsl:call-template>
				</xsl:for-each>	
    		</item>
    	</xsl:for-each>

		</channel>
	</mrss>
  
</xsl:template>



<xsl:template name="HandleVideoAsset">
	<xsl:param name="TitleRefIdField" select="'Undef'"/>
	<xsl:param name="TitleRef" select="'Undef'"/>
	<!-- <title><xsl:value-of select="$TitleRef"/></title> -->
	<xsl:for-each select="/publisher-upload-manifest/asset[@refid = $TitleRef]">
		<xsl:variable name="FlavorWithDash">
      		<xsl:value-of select="substring-after(@refid,$TitleRefIdField)" />
    	</xsl:variable>
		<xsl:variable name="Flavor">
      		<xsl:value-of select="substring-after($FlavorWithDash,'_')" />
    	</xsl:variable>
		<content>
			<xsl:attribute name="flavorParams"><xsl:value-of select="$Flavor"/></xsl:attribute>
			<serverFileContentResource>
			<xsl:attribute name="filePath"><xsl:value-of select="@filename"/></xsl:attribute>
				<!--fileSize>2743980</fileSize-->
			</serverFileContentResource>
		</content>		
	</xsl:for-each>
	
</xsl:template>





  <xsl:template name="FormatDate">
    <xsl:param name="DateTime" />
    <!-- from date format 10/02/2011 7:45 AM -->
    <!-- to date format 2006-01-14T08:55:22 -->
    <xsl:variable name="mo">
      <xsl:value-of select="substring-before($DateTime,'/')" />
    </xsl:variable>
    <xsl:variable name="DayTemp">
      <xsl:value-of select="substring-after($DateTime,'/')" />
    </xsl:variable>
    <xsl:variable name="day">
      <xsl:value-of select="substring-before($DayTemp,'/')" />
    </xsl:variable>
    <xsl:variable name="YearTemp">
      <xsl:value-of select="substring-after($DayTemp,'/')" />
    </xsl:variable>
    <xsl:variable name="year">
      <xsl:value-of select="substring-before($YearTemp,' ')" />
    </xsl:variable>
    
    
    <xsl:variable name="HourMinute">
      <xsl:value-of select="substring-after($DateTime,' ')" />
    </xsl:variable>
    <xsl:variable name="ampm">
      <xsl:value-of select="substring-after($HourMinute,' ')" />
    </xsl:variable>
    
    <xsl:variable name="hh">
      <xsl:value-of select="substring-before($HourMinute,':')" />
    </xsl:variable>
    <xsl:variable name="Minute">
      <xsl:value-of select="substring-before($HourMinute,' ')" />
    </xsl:variable>
    <xsl:variable name="mm">
      <xsl:value-of select="substring-after($Minute,':')" />
    </xsl:variable>
    
    <xsl:value-of select="$year"/>
    <xsl:value-of select="'-'"/>
    <xsl:if test="(string-length($mo) = '1')">
      <xsl:value-of select="0"/>
    </xsl:if>
    <xsl:value-of select="$mo"/>
    <xsl:value-of select="'-'"/>
    <xsl:if test="(string-length($day) = '1')">
      <xsl:value-of select="0"/>
    </xsl:if>
    <xsl:value-of select="$day"/>
    <xsl:value-of select="'T'"/>
    <xsl:if test="($ampm = 'AM')">
    	<xsl:if test="(string-length($hh) = '1')">
      		<xsl:value-of select="0"/>
    	</xsl:if>
    	<xsl:value-of select="$hh"/>
    </xsl:if>
    <xsl:if test="($ampm = 'PM')">
	    <xsl:choose>
	    	<xsl:when test="$hh = '12'"><xsl:value-of select="'00'"/></xsl:when>
	    	<xsl:otherwise><xsl:value-of select="string(sum($hh) + 12)"/></xsl:otherwise>
	    </xsl:choose>
    </xsl:if>
    <xsl:value-of select="':'"/>
    <xsl:value-of select="$mm"/>
    <xsl:value-of select="':'"/>
    <xsl:value-of select="'00'"/>
  </xsl:template>


</xsl:stylesheet>
