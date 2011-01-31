<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="xml" />

	<xsl:template match="/">
	<MySpaceFeed xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">

	<Title>Fox Sports Videos</Title>
	<Description>Fox Sports Videos</Description>
	<Contact></Contact>
	<LastUpdate>
			<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z')" />	
	</LastUpdate>
	<MediaItems>
	
		<xsl:for-each select="item">
		<MediaItem>
			<Name>
				<xsl:value-of select="title" />
			</Name>
			<Title>
				<xsl:value-of select="title" />
			</Title>
			<Description>
				<xsl:value-of select="description" />
			</Description>
			<xsl:if test="sum(distribution[@provider='MYSPACE']/sunrise) > 0">
				<ReleaseDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='MYSPACE']/sunrise))" />
				</ReleaseDate>
			</xsl:if>			
			<xsl:if test="sum(distribution[@provider='MYSPACE']/sunset) > 0">
				<TerminationDate>
					<xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(distribution[@provider='MYSPACE']/sunset))" />
				</TerminationDate>
			</xsl:if>
		</MediaItem>
	</xsl:for-each>
	
	</MediaItems>	
	</MySpaceFeed>
	</xsl:template>

</xsl:stylesheet>
