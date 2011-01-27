<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />
	<xsl:variable name="csId" />
	<xsl:variable name="source" />
	<xsl:variable name="metadataProfileId" />
	<xsl:variable name="movFlavorAssetId" />
	<xsl:variable name="flvFlavorAssetId" />
	<xsl:variable name="wmvFlavorAssetId" />
	<xsl:variable name="thumbAssetId" />

	<xsl:template match="item">

		<video xmlns="urn:schemas-microsoft-com:msnvideo:catalog">
			<xsl:if test="count(distribution[@provider='MSN']/remoteId) > 0">
				<uuid>
					<xsl:value-of select="distribution[@provider='MSN']/remoteId" />
				</uuid>
			</xsl:if>
			<providerId>
				<xsl:value-of select="entryId" />
			</providerId>
			<csId>
				<xsl:value-of select="$csId" />
			</csId>
			<source>
				<xsl:value-of select="$source" />
			</source>
			<pageGroup></pageGroup>
			<title>
				<xsl:value-of select="title" />
			</title>
			<description>
				<xsl:value-of select="description" />
			</description>
			<durationSecs>
				<xsl:value-of select="floor(sum(media/duration) div 1000)" />
			</durationSecs>
			<xsl:if test="sum(distribution[@provider='MSN']/sunrise) > 0">
				<startDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='MSN']/sunrise))" />
				</startDate>
			</xsl:if>
			<xsl:if test="sum(distribution[@provider='MSN']/sunset) > 0">
				<activeEndDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='MSN']/sunset))" />
				</activeEndDate>
			</xsl:if>
			<xsl:if test="sum(distribution[@provider='MSN']/sunset) > 0">
				<searchableEndDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='MSN']/sunset))" />
				</searchableEndDate>
			</xsl:if>
			<xsl:if test="sum(distribution[@provider='MSN']/sunset) > 0">
				<archiveEndDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='MSN']/sunset))" />
				</archiveEndDate>
			</xsl:if>
			<tags>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/MSNVideoCat) > 0">
					<tag market="us" namespace="MSNVideo_Cat"><xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/MSNVideoCat"/></tag>
				</xsl:if>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/MSNVideoTop) > 0">
					<tag market="us" namespace="MSNVideo_Top"><xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/MSNVideoTop"/></tag>
				</xsl:if>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/MSNVideoTopCat) > 0">
					<tag market="us" namespace="MSNVideo_Top_Cat"><xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/MSNVideoTopCat"/></tag>
				</xsl:if>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/MSNPublic) > 0">
					<tag market="us" namespace="Public"><xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/MSNPublic"/></tag>
				</xsl:if>
			</tags>
			<videoFiles>
				<xsl:if test="count(content[@flavorAssetId = $movFlavorAssetId])">
					<videoFile formatCode="1001">
						<uri>
							<xsl:value-of select="content[@flavorAssetId = $movFlavorAssetId]/@url"/>
							<xsl:text>/name/</xsl:text>
							<xsl:value-of select="content/@flavorAssetId"/>
							<xsl:text>.mov</xsl:text>
						</uri>
					</videoFile>
				</xsl:if>
				<xsl:if test="count(content[@flavorAssetId = $wmvFlavorAssetId])">
					<videoFile formatCode="1002">
						<uri>
							<xsl:value-of select="content[@flavorAssetId = $wmvFlavorAssetId]/@url"/>
							<xsl:text>/name/</xsl:text>
							<xsl:value-of select="content/@flavorAssetId"/>
							<xsl:text>.wmv</xsl:text>
						</uri>
					</videoFile>
				</xsl:if>
				<xsl:if test="count(content[@flavorAssetId = $flvFlavorAssetId])">
					<videoFile formatCode="1003">
						<uri>
							<xsl:value-of select="content[@flavorAssetId = $flvFlavorAssetId]/@url"/>
							<xsl:text>/name/</xsl:text>
							<xsl:value-of select="content/@flavorAssetId"/>
							<xsl:text>.flv</xsl:text>
						</uri>
					</videoFile>
				</xsl:if>
			</videoFiles>
			<files>
				<xsl:if test="count(thumbnail[@thumbAssetId = $thumbAssetId])">
					<file formatCode="2009">
						<uri>
							<xsl:value-of select="thumbnail[@thumbAssetId = $thumbAssetId]/@url"/>
							<xsl:text>/name/</xsl:text>
							<xsl:value-of select="thumbnail/@thumbAssetId"/>
							<xsl:text>.jpg</xsl:text>
						</uri>
					</file>
				</xsl:if>
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
