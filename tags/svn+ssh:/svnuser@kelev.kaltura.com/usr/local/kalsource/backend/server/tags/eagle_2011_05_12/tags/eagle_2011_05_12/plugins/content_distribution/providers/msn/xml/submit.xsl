<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />
	<xsl:variable name="metadataProfileId" />
	<xsl:variable name="movFlavorAssetId" />
	<xsl:variable name="flvFlavorAssetId" />
	<xsl:variable name="wmvFlavorAssetId" />
	<xsl:variable name="thumbAssetId" />

	<xsl:template match="item">

		<video xmlns="urn:schemas-microsoft-com:msnvideo:catalog">
			<uuid>
				<xsl:if test="count(distribution[@provider='MSN']/remoteId) > 0">
					<xsl:value-of select="distribution[@provider='MSN']/remoteId" />
				</xsl:if>
			</uuid>
			<providerId>
				<xsl:value-of select="referenceID" />
			</providerId>
			<csId>Fox Sports</csId>
			<source>
				<xsl:text>Fox_</xsl:text>
				<xsl:if test="count(category) > 0">
					<xsl:value-of select="category[position() = 1]/@name" />
				</xsl:if>
			</source>
			<pageGroup></pageGroup>
			<title>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle) > 0">
					<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongTitle"/>
				</xsl:if>
			</title>
			<description>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/LongDescription) > 0">
					<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/LongDescription"/>
				</xsl:if>
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
				<searchableEndDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='MSN']/sunset))" />
				</searchableEndDate>
				<archiveEndDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='MSN']/sunset))" />
				</archiveEndDate>
			</xsl:if>
			<tags>
				<tag market="us" namespace="MSNVideo_Cat">
					<xsl:if test="count(category) > 0">
						<xsl:value-of select="category[position() = 1]/@name" />
					</xsl:if>
				</tag>
				<tag market="us" namespace="MSNVideo_Top_Cat">
					<xsl:text>Fox Sports_</xsl:text>
					<xsl:if test="count(category) > 0">
						<xsl:value-of select="category[position() = 1]/@name" />
					</xsl:if>
				</tag>
				<xsl:for-each select="customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey">
					<xsl:if test="string(statskeyType) = 'Player'">
						<tag market="us" namespace="SportAthlete">
							<xsl:value-of select="statskeyName"/>
						</tag>						
					</xsl:if>
					<xsl:if test="statskeyType = 'Team'">
						<xsl:choose>
							<xsl:when test="parentId = '24' or parentId = '99'">
								<tag market="us" namespace="SportCollege">
									<xsl:value-of select="statskeyName"/>
								</tag>					
							</xsl:when>
							<xsl:otherwise>					
								<tag market="us" namespace="SportTeam">
									<xsl:value-of select="statskeyName"/>
								</tag>		
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					<xsl:if test="statskeyType = 'League'">
						<tag market="us" namespace="SportLeague">
							<xsl:value-of select="statskeyName"/>
						</tag>						
					</xsl:if>
					<xsl:if test="statskeyType = 'Sport'">
						<tag market="us" namespace="SportCategory">
							<xsl:value-of select="statskeyName"/>
						</tag>						
					</xsl:if>
				</xsl:for-each>
				<tag market="us" namespace="mobile">
					<xsl:choose>
						<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '4494']) > 0">
							<xsl:text>mlb</xsl:text>
						</xsl:when>
						<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '4495']) > 0">
							<xsl:text>nba</xsl:text>
						</xsl:when>
						<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '4496']) > 0">
							<xsl:text>collegefb</xsl:text>
						</xsl:when>
						<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '4497']) > 0">
							<xsl:text>nhl</xsl:text>
						</xsl:when>
						<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '4498']) > 0">
							<xsl:text>nfl</xsl:text>
						</xsl:when>
						<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '5588']) > 0">
							<xsl:text>pga</xsl:text>
						</xsl:when>
						<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '6338']) > 0">
							<xsl:text>tennis</xsl:text>
						</xsl:when>
						<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '770200']) > 0">
							<xsl:text>ufc</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>foxsports</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</tag>						
			</tags>
			<videoFiles>
				<xsl:if test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/StatskeysFull/statskeys/statskey/statskeyId[string() = '6098']) = 0">
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
