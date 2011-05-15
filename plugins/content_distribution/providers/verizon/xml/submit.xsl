<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
				xmlns:ns2="http://www.real.com/msdp"
				xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />
	<xsl:variable name="distributionProfileId" />
	<xsl:variable name="metadataProfileId" />	
	<xsl:variable name="deleteOp"/>	
	<xsl:variable name="vrzFlavorAssetId" />
	<xsl:variable name="thumbAssetId" />
	<xsl:variable name="providerName" />
	<xsl:variable name="providerId" />
	

	<xsl:template name="implode">
		<xsl:param name="items" />
		<xsl:param name="separator" select="','" />
		<xsl:for-each select="$items">
			<xsl:if test="position() &gt; 1">
				<xsl:value-of select="$separator" />
			</xsl:if>
			
			<xsl:value-of select="." />
		</xsl:for-each>
	</xsl:template>
		
	<xsl:template match="item">
		<ns2:rss xmlns:ns2="http://www.real.com/msdp" xmlns="http://www.real.com/msdp" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<xsl:attribute name="xsi:schemaLocation">http://www.real.com/ns2 VCastRSS.xsd</xsl:attribute>
			<ns2:channel>
				<ns2:title>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle" />
					</xsl:if>
				</ns2:title>
				<ns2:link>None</ns2:link>
				<ns2:externalid><xsl:value-of select="entryId" /></ns2:externalid>
				<ns2:shortdescription>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle" />
					</xsl:if>
				</ns2:shortdescription>
				<ns2:description>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongDescription) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongDescription" />
					</xsl:if>				
				</ns2:description>
				<ns2:keywords>
					<xsl:if test="count(tags/tag) > 0">
						<xsl:call-template name="implode">
							<xsl:with-param name="items" select="customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyName" />
						</xsl:call-template>
					</xsl:if>
				</ns2:keywords>
				<ns2:pubDate>
					<xsl:value-of select="php:function('date', 'Y-m-d', sum(createdAt))" />
				</ns2:pubDate>
				<ns2:category>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/VerizonCategory) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/VerizonCategory" />
					</xsl:if>
				</ns2:category>
				<ns2:topStory>00:00:00</ns2:topStory>
				<ns2:genre></ns2:genre>
				<ns2:generator />
				<ns2:rating>None</ns2:rating>
				<ns2:copyright>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/copyright) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/copyright" />
					</xsl:if>
				</ns2:copyright>
				<ns2:entitlement>BASIC</ns2:entitlement>
				<ns2:year><xsl:value-of select="php:function('date', 'Y', sum(createdAt))" /></ns2:year>
				<ns2:liveDate>
					<xsl:choose>
						<xsl:when test="$deleteOp = ''">
							<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s.000', sum(createdAt))" />
						</xsl:when>
						<xsl:otherwise>
								<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s.000',1295449112-2*86400)" />
						</xsl:otherwise>
					</xsl:choose>
				</ns2:liveDate>
				<ns2:endDate>
					<xsl:choose>
						<xsl:when test="$deleteOp = ''">
							<xsl:if test="sum(distribution[@distributionProfileId=$distributionProfileId]/sunset) > 0">
								<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@distributionProfileId=$distributionProfileId]/sunset))" />
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>						
							<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s.000',1295449112-86400)" />
						</xsl:otherwise>
					</xsl:choose>
				</ns2:endDate>
				<ns2:purchaseEndDate />
				<ns2:priority>1</ns2:priority>
				<ns2:allowStreaming>Y</ns2:allowStreaming>
				<ns2:streamingPriceCode>284</ns2:streamingPriceCode>
				<ns2:allowDownload>N</ns2:allowDownload>
				<ns2:downloadPriceCode>283</ns2:downloadPriceCode>
				<ns2:allowFastForwarding>Y</ns2:allowFastForwarding>
				<ns2:provider>
					<xsl:value-of select="$providerName" />
 				</ns2:provider>
				<ns2:providerid>
					<xsl:value-of select="$providerId" />
				</ns2:providerid>
				<ns2:alertCode></ns2:alertCode>
				<ns2:alertTimeToLive></ns2:alertTimeToLive>
				<ns2:alertShowImage>N</ns2:alertShowImage>
				<ns2:image ignore="N">
					<ns2:type>thumbnail</ns2:type>
					<ns2:url>
					<xsl:if test="count(thumbnail[@thumbAssetId = $thumbAssetId])">
							<xsl:value-of select="thumbnail[@thumbAssetId = $thumbAssetId]/@url"/>
					</xsl:if>					
					</ns2:url>
					<ns2:title></ns2:title>
				</ns2:image>
				<ns2:item>
					<ns2:title>
					</ns2:title>
					<link>None</link>
					<ns2:description>
					</ns2:description>
					<ns2:encode>Y</ns2:encode>
					<ns2:move>Y</ns2:move>
					<xsl:if test="count(content[@flavorAssetId = $vrzFlavorAssetId])">
						<enclosure url="{content[@flavorAssetId = $vrzFlavorAssetId]/@url}/name/{entryId}.mp4" length="00:00:00" type="video" />
					</xsl:if>
					<ns2:guid></ns2:guid>
				</ns2:item>
			</ns2:channel>
		</ns2:rss>
	</xsl:template>
</xsl:stylesheet>
