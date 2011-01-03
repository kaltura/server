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
	
		<addMediaWithFiles>
			<media>
				<xsl:if test="count(thumbnail[@thumbAssetId = $thumbAssetId])">
					<thumbnailURL>
						<xsl:value-of select="thumbnail[@thumbAssetId = $thumbAssetId]/@url"/>
					</thumbnailURL>
				</xsl:if>
				<airdate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s-08:00', sum(createdAt))" />
				</airdate>
				<title><xsl:value-of select="title" /></title>
				<description>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/ShortDescription) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/ShortDescription"/>
					</xsl:if>
				</description>
				<keywords><xsl:value-of select="$keywords" /></keywords>
				<categories>
					<xsl:for-each select="customData[@metadataProfileId = $metadataProfileId]/metadata/ComcastCategory">
						<string>
							<xsl:value-of select="."/>
						</string>
					</xsl:for-each>
				</categories>
				<author><xsl:value-of select="$author" /></author>
				<album><xsl:value-of select="$album" /></album>
				<copyright>
					<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/copyright) > 0">
						<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/copyright"/>
					</xsl:if>
				</copyright>
				<customData>
					<item>
						<title>Headline</title>
						<value>
							<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle) > 0">
								<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle"/>
							</xsl:if>
						</value>
					</item>
					<item>
						<title>Link Href</title>
						<value></value>
					</item>
					<item>
						<title>Link Text</title>
						<value></value>
					</item>
					<item>
						<title>Video Dimensions</title>
						<value>
							<xsl:for-each select="content[@flavorAssetId = $flavorAssetId]">
								<xsl:value-of select="@width" />
								<xsl:text>x</xsl:text>
								<xsl:value-of select="@height" />
							</xsl:for-each>
						</value>
					</item>
					<item>
						<title>Notes to Comcast</title>
						<value></value>
					</item>
				</customData>
			</media>
			<mediaFiles>
				<xsl:for-each select="content[@flavorAssetId = $flavorAssetId]">
					<mediaFile>
						<bitrate><xsl:value-of select="@videoBitrate" /></bitrate>
						<contentType>Video</contentType>
						<format>
							<xsl:call-template name="upper-case">
								<xsl:with-param name="str" select="@format"/>
							</xsl:call-template>
						</format>
						<length>
							<xsl:value-of select="floor(sum(media/duration) div 60)" />
							<xsl:text>:</xsl:text>
							<xsl:value-of select="(sum(media/duration) mod 60)" />
							<xsl:text>:00</xsl:text>
						</length>
						<mediaFileType>Internal</mediaFileType>
						<originalLocation><xsl:value-of select="@url" /></originalLocation>
						<allowRelease>true</allowRelease>
					</mediaFile>
				</xsl:for-each>
			</mediaFiles>
		</addMediaWithFiles>

	</xsl:template>
</xsl:stylesheet>
