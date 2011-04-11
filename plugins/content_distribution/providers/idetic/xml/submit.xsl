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
				<IndirectUploadURL>
					<xsl:value-of select="@url" />
				</IndirectUploadURL>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="item">
    <ProgramDescription>
        <ProgramInformationTable> 
            <ProgramInformation> 
                <BasicDescription>
					<Title>
						<xsl:choose>
							<xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/ShortTitle) > 0">
								<xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/ShortTitle" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="title" />
							</xsl:otherwise>
						</xsl:choose>					
					</Title>				
					<ShortTitle>
            <xsl:choose>
              <xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/ShortTitle) > 0">
							  <xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/ShortTitle" />
						  </xsl:when>
              <xsl:otherwise>
                <xsl:value-of select="title" />
              </xsl:otherwise>
            </xsl:choose>
					</ShortTitle>
					<Synopsis>
            <xsl:choose>
              <xsl:when test="count(customData[@metadataProfileId = $metadataProfileId]/metadata/MediumDescription) > 0">
							  <xsl:value-of select="customData[@metadataProfileId = $metadataProfileId]/metadata/MediumDescription" />
						  </xsl:when>
              <xsl:otherwise>
                <xsl:value-of select="description" />
              </xsl:otherwise>
            </xsl:choose>
					</Synopsis>
					<Keyword>
            <xsl:choose>
              <xsl:when test="count(customData/metadata/keywords/keyword) > 0">
                <xsl:call-template name="implode">
                  <xsl:with-param name="items" select="customData/metadata/keywords/keyword" />
                </xsl:call-template>
              </xsl:when>
              <xsl:otherwise>
                <xsl:call-template name="implode">
                  <xsl:with-param name="items" select="tags/tag" />
                </xsl:call-template>
              </xsl:otherwise>
            </xsl:choose>
					</Keyword>
				</BasicDescription>
			</ProgramInformation> 
		</ProgramInformationTable> 
		<ProgramLocationTable>
			<ProgramLocation> 
				<OnDemandProgram>
					<StartOfAvailability>
						<xsl:choose>
							<xsl:when test="$deleteOp = ''">
								<xsl:choose>
									<xsl:when test="sum(distribution[@distributionProfileId=$distributionProfileId]/sunrise) > 0">
										<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@distributionProfileId=$distributionProfileId]/sunrise))" />
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z')" />
									</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:otherwise>
									<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z',1295449112-2*86400)" />
							</xsl:otherwise>
						</xsl:choose>
					</StartOfAvailability>
					<xsl:choose>
						<xsl:when test="$deleteOp = ''">
							<xsl:if test="sum(distribution[@distributionProfileId=$distributionProfileId]/sunset) > 0">
								<EndOfAvailability>
									<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@distributionProfileId=$distributionProfileId]/sunset))" />
								</EndOfAvailability>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>						
							<EndOfAvailability>
									<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z',1295449112-86400)" />
							</EndOfAvailability>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:for-each select="distribution[@distributionProfileId=$distributionProfileId]/flavorAssetIds/flavorAssetId">
						<xsl:call-template name="flavor-item">
							<xsl:with-param name="flavorAssetId" select="." />
						</xsl:call-template>
					</xsl:for-each>
				</OnDemandProgram>					
			</ProgramLocation>
		</ProgramLocationTable> 
	</ProgramDescription>
</xsl:template>


</xsl:stylesheet>
