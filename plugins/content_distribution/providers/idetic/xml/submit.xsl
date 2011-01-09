<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />

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
				<Program><xsl:value-of select="@url" /></Program>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="item">
		<ProgramDescription>
			<ProgramInformationTable>
				<ProgramInformation>
					<BasicDescription>
						<Title>
							<xsl:if test="count(customData/metadata/ShortTitle) > 0">
								<xsl:value-of select="customData/metadata/ShortTitle" />
							</xsl:if>
						</Title>
						<ShortTitle>
							<xsl:if test="count(customData/metadata/ShortTitle) > 0">
								<xsl:value-of select="customData/metadata/ShortTitle" />
							</xsl:if>
						</ShortTitle>
						<Synopsis>
							<xsl:if test="count(customData/metadata/MediumDescription) > 0">
								<xsl:value-of select="customData/metadata/MediumDescription" />
							</xsl:if>
						</Synopsis>
						<Keyword>
							<xsl:if test="count(customData/metadata/keywords/keyword) > 0">
								<xsl:call-template name="implode">
									<xsl:with-param name="items" select="customData/metadata/keywords/keyword" />
								</xsl:call-template>
							</xsl:if>
						</Keyword>
						<ReleaseInformation>
							<ReleaseDate>
								<DayAndYear>
									<xsl:if test="sum(distribution[@provider='Idetic']/sunrise) > 0">
										<availableDate>
											<xsl:value-of select="php:function('date', 'm-d-Y', sum(distribution[@provider='Idetic']/sunrise))" />
										</availableDate>
									</xsl:if>
								</DayAndYear>
							</ReleaseDate>
						</ReleaseInformation>
					</BasicDescription>
				</ProgramInformation>
			</ProgramInformationTable>
			<ProgramLocationTable>
				<ProgramLocation>
					<OnDemandProgram>
						<xsl:for-each select="distribution[@provider='Idetic']/flavorAssetIds/flavorAssetId">
							<xsl:call-template name="flavor-item">
								<xsl:with-param name="flavorAssetId" select="." />
							</xsl:call-template>
						</xsl:for-each>
						<EndOfAvailability>
							<xsl:if test="sum(distribution[@provider='Idetic']/sunset) > 0">
								<availableDate>
									<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='Idetic']/sunset))" />
								</availableDate>
							</xsl:if>
						</EndOfAvailability>
					</OnDemandProgram>
				</ProgramLocation>
			</ProgramLocationTable>
		</ProgramDescription>
		
	</xsl:template>
</xsl:stylesheet>
