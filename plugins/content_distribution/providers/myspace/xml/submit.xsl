<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" 
				xmlns:str="http://exslt.org/strings" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />
	<xsl:variable name="distributionProfileId" />
	<xsl:variable name="metadataProfileId" />	

	<xsl:template match="@*|node()">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()"/> 
		</xsl:copy>
	</xsl:template>

	<xsl:template match="MySpaceFeed">
		<xsl:param name="title" />
		<xsl:for-each select="MediaItems/MediaItem">
			<xsl:if test="Name != $title">
				<xsl:copy>
					<xsl:apply-templates select="@*|node()"/> 
				</xsl:copy>
			</xsl:if>			
		</xsl:for-each>	
	</xsl:template>

	<xsl:template match="item">
	<MySpaceFeed xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
	<Title>
	<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/FeedTitle) > 0">
		<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/FeedTitle" />
	</xsl:if>
	</Title>
	<Description>
	<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/FeedDescription) > 0">
		<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/FeedDescription" />
	</xsl:if>	
	</Description>
	<Contact>
		<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/FeedContact" />
	</Contact>
	<LastUpdate>
			<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z')" />	
	</LastUpdate>
	<MediaItems>	
		<MediaItem>
			<Name>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/Slug) > 0">
					<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/Slug" />
				</xsl:if>				
			</Name>
			<Title>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle) > 0">
					<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle" />
				</xsl:if>	
			</Title>
			<Description>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongDescription) > 0">
					<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongDescription" />
				</xsl:if>	
			</Description>
			<xsl:if test="sum(distribution[@distributionProfileId=$distributionProfileId]/sunrise) > 0">
				<ReleaseDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@distributionProfileId=$distributionProfileId]/sunrise))" />
				</ReleaseDate>
			</xsl:if>			
			<xsl:if test="sum(distribution[@distributionProfileId=$distributionProfileId]/sunset) > 0">
				<TerminationDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@distributionProfileId=$distributionProfileId]/sunset))" />
				</TerminationDate>
			</xsl:if>
			<LastUpdate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z')" />	
			</LastUpdate>			
			<Location>
				<xsl:choose>
					<xsl:when test="string-length(distribution[@distributionProfileId=$distributionProfileId]/flavorAssetIds/flavorAssetId) > 0">
						<xsl:variable name="flavourId" select="distribution[@distributionProfileId=$distributionProfileId]/flavorAssetIds/flavorAssetId"/>
						<xsl:value-of select="content[@flavorAssetId=$flavourId]/@url"/>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>					
				</xsl:choose>
			</Location>
			<Tags>
				<xsl:for-each select="str:tokenize(customData[@metadataProfileId = $metadataProfileId]/metadata/Keywords, ',')">
				  <Tag>
					<xsl:value-of select="."/>
				  </Tag>
				</xsl:for-each>			
			</Tags>
		</MediaItem>
      <xsl:apply-templates select="document('C:/kaltura/opt/kaltura/app/plugins/content_distribution/providers/myspace/xml/feed.xml')">
		<xsl:with-param name="title" select="customData[@metadataProfileId = $metadataProfileId]/metadata/Slug"/>
	  </xsl:apply-templates>
	</MediaItems>	
	</MySpaceFeed>
	</xsl:template>

</xsl:stylesheet>
