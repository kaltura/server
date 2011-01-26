<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
				xmlns:msdp="http://www.real.com/msdp"
				xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />
	<xsl:variable name="distributionProfileId" />
	<xsl:variable name="metadataProfileId" />	
	<xsl:variable name="deleteOp"/>	

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
	
	<xsl:template name="flavor-item">
		<xsl:param name="flavorAssetId" />
		
		<xsl:for-each select="/item/content">
			<xsl:if test="@flavorAssetId = $flavorAssetId">
				<item>
					<title>
						<xsl:if test="count(/item/customData[@metadataProfileId = $metadataProfileId]/metadata/ShortTitle) > 0">
							<xsl:value-of select="/item/customData[@metadataProfileId = $metadataProfileId]/metadata/ShortTitle" />
						</xsl:if>
					</title>
					<link>None</link>
					<description>
						<xsl:if test="count(/item/customData[@metadataProfileId = $metadataProfileId]/metadata/ShortDescription) > 0">
							<xsl:value-of select="/item/customData[@metadataProfileId = $metadataProfileId]/metadata/ShortDescription" />
						</xsl:if>
					</description>
					<msdp:encode>Y</msdp:encode>
					<msdp:move>Y</msdp:move>
					<enclosure url="{@url}" length="00:00:00" type="video" />
					<guid></guid>
				</item>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="item">
		<rss xmlns="http://www.real.com/msdp" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<xsl:attribute name="xsi:schemaLocation">http://www.real.com/msdp VCastRSS.xsd</xsl:attribute>
			<channel>
				<title>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/ShortTitle) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/ShortTitle" />
					</xsl:if>
				</title>
				<link>None</link>
				<msdp:externalid><xsl:value-of select="entryId" /></msdp:externalid>
				<shortdescription>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/ShortDescription) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/ShortDescription" />
					</xsl:if>
				</shortdescription>
				<description><xsl:value-of select="description" /></description>
				<msdp:keywords>
					<xsl:if test="count(tags/tag) > 0">
						<xsl:call-template name="implode">
							<xsl:with-param name="items" select="tags/tag" />
						</xsl:call-template>
					</xsl:if>
				</msdp:keywords>
				<pubDate>
					<xsl:value-of select="php:function('date', 'Y-m-d', sum(createdAt))" />
				</pubDate>
				<category>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/VerizonCategory) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/VerizonCategory" />
					</xsl:if>
				</category>
				<msdp:topStory>00:00:00</msdp:topStory>
				<msdp:genre></msdp:genre>
				<generator />
				<rating>None</rating>
				<msdp:copyright>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/copyright) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/copyright" />
					</xsl:if>
				</msdp:copyright>
				<msdp:entitlement>BASIC</msdp:entitlement>
				<msdp:year><xsl:value-of select="php:function('date', 'Y', sum(createdAt))" /></msdp:year>
				<msdp:liveDate>
					<xsl:choose>
						<xsl:when test="$deleteOp = ''">
							<xsl:choose>
								<xsl:when test="sum(distribution[@distributionProfileId=$distributionProfileId]/sunrise) > 0">
									<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s.000', sum(distribution[@distributionProfileId=$distributionProfileId]/sunrise))" />
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s.000')" />
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
								<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s.000',1295449112-2*86400)" />
						</xsl:otherwise>
					</xsl:choose>
				</msdp:liveDate>
				<xsl:choose>
					<xsl:when test="$deleteOp = ''">
						<xsl:if test="sum(distribution[@distributionProfileId=$distributionProfileId]/sunset) > 0">
							<msdp:endDate>
								<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@distributionProfileId=$distributionProfileId]/sunset))" />
							</msdp:endDate>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>						
						<msdp:endDate>
								<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s.000',1295449112-86400)" />
						</msdp:endDate>
					</xsl:otherwise>
				</xsl:choose>
				<msdp:purchaseEndDate />
				<msdp:priority>1</msdp:priority>
				<msdp:allowStreaming>Y</msdp:allowStreaming>
				<msdp:streamingPriceCode>284</msdp:streamingPriceCode>
				<msdp:allowDownload>Y</msdp:allowDownload>
				<msdp:downloadPriceCode>283</msdp:downloadPriceCode>
				<msdp:allowFastForwarding>Y</msdp:allowFastForwarding>
				<msdp:provider>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/VerizonProvider) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/VerizonProvider" />
					</xsl:if>					
 				</msdp:provider>
				<msdp:providerid>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/VerizonProviderId) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/VerizonProviderId" />
					</xsl:if>	
				</msdp:providerid>
				<msdp:alertCode></msdp:alertCode>
				<msdp:alertTimeToLive></msdp:alertTimeToLive>
				<msdp:alertShowImage>N</msdp:alertShowImage>
				<xsl:for-each select="distribution[@distributionProfileId=$distributionProfileId]/flavorAssetIds/flavorAssetId">
					<xsl:call-template name="flavor-item">
						<xsl:with-param name="flavorAssetId" select="." />
					</xsl:call-template>
				</xsl:for-each>
			</channel>
		</rss>
	</xsl:template>
</xsl:stylesheet>
