<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/" xmlns:bc="http://www.brightcove.tv/link">
<xsl:output method="xml" encoding="UTF-8" omit-xml-declaration="no"/>
<xsl:variable name="distributionProfileId" />
<xsl:variable name="metadataProfileId" />

  <xsl:template match="item">
    <FWCoreContainer bvi_xsd_version="1" contact_email="mgoodberg@freewheel.tv">
      <FWVideoDocument>
			<xsl:attribute name="video_id"><xsl:value-of select="entryId"/></xsl:attribute>
			<xsl:variable name="series" select="normalize-space(string(media:custom/series))" />
			<xsl:variable name="season" select="normalize-space(string(media:custom/season))" />
			<xsl:variable name="seasonAndSeries">
				<xsl:call-template name="concatSeasonAndSeries">
					<xsl:with-param name="season" select="$season" />
					<xsl:with-param name="series" select="$series" />
				</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="genre" select="normalize-space(string(media:custom/genre))" />
			<xsl:variable name="referenceid" select="normalize-space(string(media:custom/referenceid))" />
			<xsl:variable name="thumbnail-url" select="normalize-space(string(thumbnailUrl/@url))" />
			<xsl:variable name="episode" select="normalize-space(string(media:custom/episode))" />
			<xsl:variable name="contentType" select="normalize-space(string(media:custom/contentType))" />
			<xsl:variable name="repeat">
				<xsl:call-template name="getRepeat">
					<xsl:with-param name="repeat" select="normalize-space(string(media:custom/repeat))" />
				</xsl:call-template>
			</xsl:variable>
        		<fwContentOwner>
            			<SelfContentOwner />
        		</fwContentOwner>
			<fwReplaceGroup>true</fwReplaceGroup>
			<fwTitles>
				<titleItem>
					<title>V|FDM</title>
					<titleType>Group</titleType>
				</titleItem>
				<titleItem>
					<title>V|ENT</title>
					<titleType>Group</titleType>
				</titleItem>
				<titleItem>
					<title>V|FX</title>
					<titleType>Group</titleType>
				</titleItem>
				<titleItem>
					<title>
						<xsl:call-template name="concatString">
							<xsl:with-param name="solidString" select="'V|FX'" />
							<xsl:with-param name="softString" select="$series" />
						</xsl:call-template>
					</title>
					<titleType>Series</titleType>
				</titleItem>
				<titleItem>
					<title>
						<xsl:call-template name="concatString">
							<xsl:with-param name="solidString" select="'V|Auto'" />
							<xsl:with-param name="softString" select="$contentType" />
						</xsl:call-template>
					</title>
					<titleType>Group</titleType>
				</titleItem>
				<xsl:choose>
					<xsl:when test="$contentType='long-form'">
				<titleItem>
					<title>
						<xsl:call-template name="concatString">
							<xsl:with-param name="solidString" select="'V|FX|Full Episodes'" />
							<xsl:with-param name="softString" select="$series" />
						</xsl:call-template>
					</title>
					<titleType>Group</titleType>
				</titleItem>
				<titleItem>
					<title>
						<xsl:value-of select="concat('V|FX|Full Episodes','|',$seasonAndSeries)" />
					</title>
					<titleType>Group</titleType>
				</titleItem>
						<xsl:if test="string-length($repeat)>0">
							<xsl:variable name="dataWithRepeat">
								<xsl:call-template name="concatString">
									<xsl:with-param name="solidString" select="'V|FX|Full Episodes'" />
									<xsl:with-param name="softString" select="$repeat" />
								</xsl:call-template>
							</xsl:variable>
				<titleItem>
					<title>
							<xsl:call-template name="concatString">
								<xsl:with-param name="solidString" select="$dataWithRepeat" />
								<xsl:with-param name="softString" select="$series" />
							</xsl:call-template>
					</title>
					<titleType>Group</titleType>
				</titleItem>
						</xsl:if>
					</xsl:when>
					<xsl:when test="$contentType='short-form'">
				<titleItem>
					<title>
						<xsl:value-of select="concat('V|FX|Repurposed Clips','|',$seasonAndSeries)" />
					</title>
					<titleType>Group</titleType>
				</titleItem>
					</xsl:when>
				</xsl:choose>
				<titleItem>
					<title>
						<xsl:call-template name="getTitle1">
							<xsl:with-param name="titleNode" select="title" />
							<xsl:with-param name="seasonNode" select="$season" />
						</xsl:call-template>	
						</title>
					<titleType>Episode Title1</titleType>
				</titleItem>
				<xsl:variable name="title2" select="$referenceid" />
				<xsl:if test="string-length($title2)>0">
				<titleItem>
					<title><xsl:value-of select="$title2" /></title>
					<titleType>Episode Title2</titleType>
				</titleItem>
				</xsl:if>
        		</fwTitles>
			<xsl:if test="description">
			<fwDescriptions>				
			    	<descriptionItem>
					<description><xsl:value-of select="description" /></description>
					<descriptionType>Episode</descriptionType>
			    	</descriptionItem>
        		</fwDescriptions>
			</xsl:if>
			<xsl:if test="string-length($genre)>0 or string-length($series)>0 or string-length($referenceid)>0 or string-length($thumbnail-url)>0 or string-length($episode)>0">
			<fwMetaData>
				<xsl:if test="string-length($genre)>0">
			   	<datumItem>
					<value><xsl:value-of select="$genre" /></value>
					<label>Genre</label>
			    	</datumItem>
				</xsl:if>
				<xsl:if test="string-length($series)>0">
			   	<datumItem>
					<value><xsl:value-of select="$series" /></value>
					<label>Show</label>
			    	</datumItem>
				</xsl:if>
				<xsl:if test="string-length($referenceid)>0">
			   	<datumItem>
					<value><xsl:value-of select="$referenceid" /></value>
					<label>ReferenceID</label>
			    	</datumItem>
				</xsl:if>
				<xsl:if test="string-length($thumbnail-url)>0">
			   	<datumItem>
					<value><xsl:value-of select="$thumbnail-url" /></value>
					<label>thumbnail_url</label>
			    	</datumItem>
				</xsl:if>
				<xsl:if test="string-length($episode)>0">
			   	<datumItem>
					<value><xsl:value-of select="$episode" /></value>
					<label>Episode</label>
			    	</datumItem>
				</xsl:if>
        		</fwMetaData>
			</xsl:if>
			<fwGenres>
				<genreItem><xsl:call-template name="concatString">
							<xsl:with-param name="solidString" select="'V|Auto'" />
							<xsl:with-param name="softString" select="$genre" />
					</xsl:call-template></genreItem>
        		</fwGenres>
        		<fwDuration>
			<xsl:call-template name="transformTimeToSenconds">
				<xsl:with-param name="time" select="bc:duration" />
			</xsl:call-template>
			</fwDuration>
    		</FWVideoDocument>
    </FWCoreContainer>
  </xsl:template>

  <xsl:template name="concatString">
	<xsl:param name="solidString" />
	<xsl:param name="softString" />
	<xsl:choose>
		<xsl:when test="string-length($softString)>0" >
			<xsl:value-of select="concat($solidString,'|',$softString)" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$solidString" />
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="concatSeasonAndSeries">
	<xsl:param name="season" />
	<xsl:param name="series" />
	<xsl:variable name="formatedSeason"> 
		<xsl:call-template name="formatSeason">
			<xsl:with-param name="season" select="$season" />
		</xsl:call-template>
	</xsl:variable>
	<xsl:variable name="result">
		<xsl:call-template name="concatString">
			<xsl:with-param name="solidString" select="$formatedSeason" />
			<xsl:with-param name="softString" select="$series" />
		</xsl:call-template>
	</xsl:variable>
	<xsl:value-of select="$result" />
</xsl:template>

<xsl:template name="getTitle1">
	<xsl:param name="titleNode" />
	<xsl:param name="seasonNode" />
	<xsl:variable name="title" select="normalize-space(string($titleNode))" />
	<xsl:variable name="formatedSeason"> 
		<xsl:call-template name="formatSeason">
			<xsl:with-param name="season" select="normalize-space(string($seasonNode))" />
		</xsl:call-template>
	</xsl:variable>
	<xsl:value-of select="concat('V','|',$formatedSeason,'|',$title)" />	
</xsl:template>

<xsl:template name="getRepeat">
	<xsl:param name="repeat" />
	<xsl:choose>
		<xsl:when test="'Yes'=$repeat">
			<xsl:value-of select="'R'" />
		</xsl:when>
		<xsl:when test="'No'=$repeat">
			<xsl:value-of select="'N'" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="''" />
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="formatSeason">
	<xsl:param name="season" />
	<xsl:choose>
		<xsl:when test="string-length(normalize-space($season))=1">
			<xsl:value-of select="concat('S','0',$season)" />
		</xsl:when>
		<xsl:when test="string-length(normalize-space($season))=2">
			<xsl:value-of select="concat('S',$season)" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="'S--'" />	
		</xsl:otherwise>	
	</xsl:choose>
</xsl:template>

<xsl:template name="transformTimeToSenconds">
	<xsl:param name="time" />
	<xsl:choose>
		<xsl:when test="string-length($time)=8">
			<xsl:variable name="hh">
      				<xsl:value-of select="substring($time,1,2)" />
    			</xsl:variable>
    			<xsl:variable name="mm">
      				<xsl:value-of select="substring($time,4,2)" />
    			</xsl:variable>
			<xsl:variable name="ss">
				<xsl:value-of select="substring($time,7,2)" />
			</xsl:variable>
			<xsl:value-of select="$hh*60*60+$mm*60+$ss"/>
		</xsl:when>
		<xsl:when test="string-length($time)=5">
    			<xsl:variable name="mm">
      				<xsl:value-of select="substring($time,1,2)" />
    			</xsl:variable>
			<xsl:variable name="ss">
				<xsl:value-of select="substring($time,4,2)" />
			</xsl:variable>
			<xsl:value-of select="$mm*60+$ss"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$time" />
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
</xsl:stylesheet>
