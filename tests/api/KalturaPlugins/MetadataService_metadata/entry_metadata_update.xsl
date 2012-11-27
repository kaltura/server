<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<!-- copy everything, apply templates to modify -->
	<xsl:template match="node()|@*">
		<xsl:copy>
			<xsl:apply-templates select="node()|@*"/>
		</xsl:copy>
	</xsl:template>

	<!-- modify title -->
	<xsl:template match="Title">
		<xsl:copy>new playlist title</xsl:copy>
	</xsl:template>

	<!-- modify hidden -->
	<xsl:template match="Hidden">
		<xsl:copy>0</xsl:copy>
	</xsl:template>

</xsl:stylesheet>


