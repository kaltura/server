<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />
	
	<xsl:variable name="distributionProfileId" />
	<xsl:variable name="aspectRatio" />
	<xsl:variable name="frameRate" />

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
				<mediaFile>
					<contentType>Video</contentType>
					<encodingProfileTitle>
						<xsl:if test="count(@flavorParamsName) > 0">
							<xsl:value-of select="@flavorParamsName" />
						</xsl:if>
					</encodingProfileTitle>
					<format><xsl:value-of select="@format" /></format>
					<originalLocation>
						<xsl:value-of select="/item/entryId"/>
						<xsl:text>_</xsl:text>
						<xsl:value-of select="$flavorAssetId"/>
						<xsl:text>_</xsl:text>
						<xsl:value-of select="@videoBitrate"/>
						<xsl:text>.</xsl:text>
						<xsl:value-of select="@extension"/>
					</originalLocation>
					<customData>
					</customData>
				</mediaFile>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="thumb-item">
		<xsl:param name="thumbAssetId" />
		
		<xsl:for-each select="/item/thumbnail">
			<xsl:if test="@thumbAssetId = $thumbAssetId">
				<mediaFile>
					<contentType>Image</contentType>
					<encodingProfileTitle>Mezzanine thumbnail</encodingProfileTitle>
					<format>JPEG</format>
					<originalLocation>
						<xsl:value-of select="/item/entryId"/>
						<xsl:text>_</xsl:text>
						<xsl:value-of select="$thumbAssetId"/>
						<xsl:text>.jpg</xsl:text>
					</originalLocation>
					<customData>
					</customData>
				</mediaFile>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="item">
		<addContent xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<xsl:attribute name="xsi:noNamespaceSchemaLocation">AddContent.xsd</xsl:attribute>
			<media>
			
				<!-- Required native fields -->
				<contentType>Video</contentType>
				<title><xsl:value-of select="title" /></title>
				<description><xsl:value-of select="description" /></description>
				<categories>
					<xsl:for-each select="customData/metadata/HuluCategories">
						<string><xsl:value-of select="." /></string>
					</xsl:for-each>
				</categories>
				<rating>
					<xsl:if test="count(customData/metadata/HuluRating) > 0">
						<xsl:value-of select="customData/metadata/HuluRating" />
					</xsl:if>
				</rating> 
				
				<!-- Optional native fields -->
				<xsl:if test="sum(distribution[@provider='Hulu']/sunrise) > 0">
					<availableDate>
						<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s', sum(distribution[@provider='Hulu']/sunrise))" />
					</availableDate>
				</xsl:if>
				<xsl:if test="sum(distribution[@provider='Hulu']/sunset) > 0">
					<expirationDate>
						<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s', sum(distribution[@provider='Hulu']/sunset))" />
					</expirationDate>
				</xsl:if>
				<copyright>
					<xsl:if test="count(customData/metadata/copyright) > 0">
						<xsl:value-of select="customData/metadata/copyright" />
					</xsl:if>
				</copyright>
				<keywords>
					<xsl:if test="count(tags/tag) > 0">
						<xsl:call-template name="implode">
							<xsl:with-param name="items" select="tags/tag" />
						</xsl:call-template>
					</xsl:if>
				</keywords>
				<language>English</language>
				<customData>
				
					<!-- Required custom data -->
					<CustomDataElement>
						<title>Media Type</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluMediaType" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Series Title</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluSeriesTitle" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Programming Type</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluProgrammingType" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Aspect Ratio</title>
						<value>
							<xsl:value-of select="$aspectRatio" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Frame Rate</title>
						<value>
							<xsl:value-of select="$frameRate" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>isMashupPermitted</title>
						<value>Yes</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Channel</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluChannel" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Episode Number</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluEpisodeNumber" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Season Number</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluSeasonNumber" />
						</value>
					</CustomDataElement>
					<!-- Optional custom data -->
					<CustomDataElement>
						<title>TMS Program ID</title>
						<value>
							<xsl:choose>
								<xsl:when test="string-length(customData/metadata/HuluTmsProgramID) > 0">
									<xsl:value-of select="customData/metadata/HuluTmsProgramID" />
								</xsl:when>
								<xsl:otherwise>N/A</xsl:otherwise>
							</xsl:choose>
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>TMS Series ID</title>
						<value>
							<xsl:choose>
								<xsl:when test="string-length(customData/metadata/HuluTmsSeriesID) > 0">
									<xsl:value-of select="customData/metadata/HuluTmsSeriesID" />
								</xsl:when>
								<xsl:otherwise>N/A</xsl:otherwise>
							</xsl:choose>
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>ExternalID</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluExternalID" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Full Description</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluFullDescription" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Series Description</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluSeriesDescription" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Season Synopsis</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluSeasonSynopsis" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Studio</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluStudio" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>CP Promotional Text</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluPromotionalText" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>CP Promotional Link</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluPromotionalLink" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Tunein Information</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluTuneinInformation" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Content Rating Reason</title>
						<value>
							<xsl:if test="count(customData/metadata/HuluContentRatingReason) > 0">
								<xsl:call-template name="implode">
									<xsl:with-param name="items" select="customData/metadata/HuluContentRatingReason" />
								</xsl:call-template>
							</xsl:if>
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Original Premiere Date</title>
						<value>
							<xsl:if test="sum(customData/metadata/HuluOriginalPremiereDate) > 0">
								<xsl:value-of select="php:function('date', 'Y-m-d', sum(customData/metadata/HuluOriginalPremiereDate))" />
							</xsl:if>
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Programming Priority</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluProgrammingPriority" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Daypart</title>
						<value>
							<xsl:value-of select="customData/metadata/HuluDaypart" />
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Segments</title>
						<value>
							<xsl:if test="count(customData/metadata/HuluSegments) > 0">
								<xsl:call-template name="implode">
									<xsl:with-param name="items" select="customData/metadata/HuluSegments" />
								</xsl:call-template>
							</xsl:if>
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>isProgressiveAllowed</title>
						<value>False</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>isDistributable </title>
						<value>True</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>isClippingAllowed </title>
						<value>True</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>allowAnyAvailabilityDate</title>
						<value>True</value>
					</CustomDataElement>
				</customData>
			</media>
			
			<mediaFiles>
				<xsl:for-each select="distribution[@distributionProfileId=$distributionProfileId]/flavorAssetIds/flavorAssetId">
					<xsl:call-template name="flavor-item">
						<xsl:with-param name="flavorAssetId" select="." />
					</xsl:call-template>
				</xsl:for-each>
				<xsl:for-each select="distribution[@distributionProfileId=$distributionProfileId]/thumbAssetIds/thumbAssetId">
					<xsl:call-template name="thumb-item">
						<xsl:with-param name="thumbAssetId" select="." />
					</xsl:call-template>
				</xsl:for-each>
				<!-- 
				
					TODO take the SAMI file from the distribution data
				
					<mediaFile>
						<contentType>Document</contentType>
						<format>Text</format>
						<assetTypes>
							<string>SAMI</string>
						</assetTypes>
						<originalLocation>FOX-24-06-004.smi</originalLocation>
					</mediaFile>
					
				 -->
			</mediaFiles>
			<options />
		</addContent>
	</xsl:template>
</xsl:stylesheet>
