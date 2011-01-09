<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />
	<xsl:variable name="metadataProfileId" />
	<xsl:variable name="thumbAssetId" />
	<xsl:variable name="flavorAssetId" />
	<xsl:variable name="keywords" />
	<xsl:variable name="author" />
	<xsl:variable name="album" />
	
	
	<xsl:variable name="smallcase" select="'abcdefghijklmnopqrstuvwxyz'" />
	<xsl:variable name="uppercase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />
	<xsl:template name="upper-case">
		<xsl:param name="str"/>
		<xsl:value-of select="translate($str, $smallcase, $uppercase)" />
	</xsl:template>

	<xsl:template match="item">
	
		<addContent>
			<media>
				<xsl:if test="count(distribution[@provider='Comcast']/remoteId) > 0">
					<id>
						<xsl:value-of select="distribution[@provider='Comcast']/remoteId" />
					</id>
				</xsl:if>
				<xsl:if test="count(thumbnail[@thumbAssetId = $thumbAssetId])">
					<thumbnailURL>
						<xsl:value-of select="thumbnail[@thumbAssetId = $thumbAssetId]/@url"/>
					</thumbnailURL>
				</xsl:if>
				<airdate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(createdAt))" />
				</airdate>
				<categories>
					<xsl:for-each select="customData[@metadataProfileId = $metadataProfileId]/metadata/ComcastCategory">
						<string>
							<xsl:value-of select="."/>
						</string>
					</xsl:for-each>
				</categories>
				<copyright>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/copyright) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/copyright"/>
					</xsl:if>
				</copyright>
				<description>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/ShortDescription) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/ShortDescription"/>
					</xsl:if>
				</description>
				<xsl:if test="sum(distribution[@provider='Comcast']/sunrise) > 0">
					<availableDate>
						<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='Comcast']/sunrise))" />
					</availableDate>
				</xsl:if>
				<xsl:if test="sum(distribution[@provider='Comcast']/sunset) > 0">
					<expirationDate>
						<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='Comcast']/sunset))" />
					</expirationDate>
				</xsl:if>
				<keywords><xsl:value-of select="$keywords" /></keywords>
				<rating>G</rating>
				<title><xsl:value-of select="title" /></title>
				<!--  
				<author><xsl:value-of select="$author" /></author>
				<album><xsl:value-of select="$album" /></album>
				-->
				<customData>
					<CustomDataElement>
						<title>Headline</title>
						<value>
							<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle) > 0">
								<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle"/>
							</xsl:if>
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Link Href</title>
						<value></value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Link Text</title>
						<value></value>
					</CustomDataElement>
					<!--  
					<CustomDataElement>
						<title>Video Dimensions</title>
						<value>
							<xsl:for-each select="content[@flavorAssetId = $flavorAssetId]">
								<xsl:value-of select="@width" />
								<xsl:text>x</xsl:text>
								<xsl:value-of select="@height" />
							</xsl:for-each>
						</value>
					</CustomDataElement>
					<CustomDataElement>
						<title>Notes to Comcast</title>
						<value></value>
					</CustomDataElement>
					-->
				</customData>
			</media>
			<mediaFiles>
				<xsl:for-each select="content[@flavorAssetId = $flavorAssetId]">
					<mediaFile>
						<assetTypes><string>Video</string></assetTypes>
						<bitrate><xsl:value-of select="@videoBitrate" /></bitrate>
						<contentType>Video</contentType>
						<format>
							<xsl:call-template name="upper-case">
								<xsl:with-param name="str" select="@format"/>
							</xsl:call-template>
						</format>
						<height><xsl:value-of select="@height" /></height>
						<length><xsl:value-of select="/item/media/duration" /></length>
						<mediaFileType>Internal</mediaFileType>
						<originalLocation><xsl:value-of select="@url" /></originalLocation>
						<!--  
						<allowRelease>true</allowRelease>
						-->
						<width><xsl:value-of select="@width" /></width>
					</mediaFile>
				</xsl:for-each>
			</mediaFiles>
		</addContent>

	</xsl:template>
</xsl:stylesheet>
