<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl" version="1.0">

	<xsl:output omit-xml-declaration="no" method="html" />

	<xsl:template name="indent">
		<xsl:param name="times" select="1" />
		<xsl:if test="$times > 1">
			<span class="indent">
			</span>
			<xsl:call-template name="indent">
				<xsl:with-param name="times" select="($times - 1)" />
			</xsl:call-template>
		</xsl:if>
	</xsl:template>

	<xsl:template match="*" mode="escape">
		<xsl:param name="indent" select="1" />
		<xsl:param name="localName" select="local-name()" />
		
		<xsl:if test="name() != 'xs:annotation'">
			<xsl:if test="position() > 2">
				<br />
			</xsl:if>

			<!-- Begin opening tag -->
			<xsl:call-template name="indent">
				<xsl:with-param name="times" select="$indent" />
			</xsl:call-template>
			<b>&lt;</b>
			<span class="xml-element"><xsl:value-of select="name()" /></span>

			<!-- Namespaces -->
			<!-- <xsl:for-each select="namespace::*"> <xsl:text> xmlns</xsl:text> 
				<xsl:if test="name() != ''"> <xsl:text>:</xsl:text> <xsl:value-of select="name()"/> 
				</xsl:if> <xsl:text>=&quot;</xsl:text> <xsl:call-template name="escape-xml"> 
				<xsl:with-param name="text" select="."/> </xsl:call-template> <xsl:text>&quot;</xsl:text> 
				</xsl:for-each> -->

			<!-- Attributes -->
			<xsl:for-each select="@*">
				<xsl:if test="name() != 'xmlns:xml'">
					<xsl:text> </xsl:text>
					<span class="xml-attribute"><xsl:value-of select="name()" /></span>
					<b>=&quot;</b>
					<span class="xml-attribute-value">
						<xsl:choose>
							<xsl:when test="$localName = 'element' and name() ='ref'">
								<xsl:element name="a">
									<xsl:attribute name="href">#element-<xsl:call-template name="escape-xml"><xsl:with-param name="text" select="." /></xsl:call-template></xsl:attribute>
									<xsl:call-template name="escape-xml"><xsl:with-param name="text" select="." /></xsl:call-template>
								</xsl:element>
							</xsl:when>
							<xsl:otherwise>
								<xsl:call-template name="escape-xml"><xsl:with-param name="text" select="." /></xsl:call-template>
							</xsl:otherwise>
						</xsl:choose>
					</span>
					<b>&quot;</b>
				</xsl:if>
			</xsl:for-each>

			<!-- End opening tag -->
			<b>&gt;</b>

			<xsl:if test="count(*[name() != 'xs:annotation']) > 0">
				<br />
			</xsl:if>
			<!-- Content (child elements, text nodes, and PIs) -->
			<xsl:apply-templates select="node()" mode="escape">
				<xsl:with-param name="indent" select="$indent + 1" />
			</xsl:apply-templates>
			<xsl:if test="count(*[name() != 'xs:annotation']) > 0">
				<xsl:call-template name="indent">
					<xsl:with-param name="times" select="$indent" />
				</xsl:call-template>
			</xsl:if>

			<!-- Closing tag -->
			
			<b>&lt;/</b>
			<span class="xml-element"><xsl:value-of select="name()" /></span>
			<b>&gt;</b>
			<br />
		</xsl:if>
	</xsl:template>

	<xsl:template match="text()" mode="escape">
		<span class="xml-attribute-value">
			<xsl:call-template name="escape-xml">
				<xsl:with-param name="text" select="." />
			</xsl:call-template>
		</span>
	</xsl:template>

	<xsl:template match="processing-instruction()" mode="escape">
		<b>&lt;?</b>
		<xsl:value-of select="name()" />
		<xsl:text> </xsl:text>
		<xsl:call-template name="escape-xml">
			<xsl:with-param name="text" select="." />
		</xsl:call-template>
		<b>?&gt;</b>
	</xsl:template>

	<xsl:template name="escape-xml">
		<xsl:param name="text" />
		<xsl:if test="$text != ''">
			<xsl:variable name="head" select="substring($text, 1, 1)" />
			<xsl:variable name="tail" select="substring($text, 2)" />
			<xsl:choose>
				<xsl:when test="$head = '&amp;'">
					&amp;amp;
				</xsl:when>
				<xsl:when test="$head = '&lt;'">
					&amp;lt;
				</xsl:when>
				<xsl:when test="$head = '&gt;'">
					&amp;gt;
				</xsl:when>
				<xsl:when test="$head = '&quot;'">
					&amp;quot;
				</xsl:when>
				<xsl:when test="$head = &quot;&apos;&quot;">
					&amp;apos;
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$head" />
				</xsl:otherwise>
			</xsl:choose>
			<xsl:call-template name="escape-xml">
				<xsl:with-param name="text" select="$tail" />
			</xsl:call-template>
		</xsl:if>
	</xsl:template>

	<xsl:template name="element">
		<xsl:param name="type" select="@type"/>
		<xsl:choose>
			<xsl:when test="string-length(@name)">
			
				<hr/>
				<a name="element-{@name}"/><a name="type-{$type}"/>
				<span class="element-title"><xsl:value-of select="@name"/> element</span>
				<xsl:text> (</xsl:text>
				<xsl:for-each select="/*[local-name() = 'schema']/*[(local-name() = 'complexType' or local-name() = 'simpleType') and @name = $type]">
					<xsl:if test="@abstract = 'true'">abstract </xsl:if>
				</xsl:for-each>
				<xsl:value-of select="$type"/>
				<xsl:text>)</xsl:text>
				<br/>
				
				<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
					<span class="element-description"><xsl:copy-of select="."/></span><br/>
				</xsl:for-each>
				<xsl:call-template name="element-type"><xsl:with-param name="type" select="@type"/></xsl:call-template>
				
				<xsl:if test="count(*[local-name() = 'annotation']/*[local-name() = 'appinfo']/*[local-name() = 'example']/*) > 0">
					<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'appinfo']/*[local-name() = 'example']">
						<span class="element-example-title">
							<xsl:choose>
								<xsl:when test="string-length(@title) > 0"><xsl:value-of select="@title"/></xsl:when>
								<xsl:otherwise>XML Example</xsl:otherwise>
							</xsl:choose>
						</span>
						<br/>
						<div class="element-example">
							<xsl:apply-templates mode="escape">
								<xsl:with-param name="indent" select="1" />
							</xsl:apply-templates>
						</div>
					</xsl:for-each>
				</xsl:if>
				
				<xsl:if test="count(/*[local-name() = 'schema']/*[local-name() = 'complexType' or local-name() = 'simpleType']/*[local-name() = 'complexContent' or local-name() = 'simpleContent']/*[local-name() = 'extension' and @base = $type]) > 0">
					<span class="element-extended-title">Extended elements</span><br/>
					<ol>
						<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'complexType' or local-name() = 'simpleType']/*[local-name() = 'complexContent' or local-name() = 'simpleContent']/*[local-name() = 'extension' and @base = $type]">
							<xsl:call-template name="extended-element">
								<xsl:with-param name="type" select="../../@name"/>
							</xsl:call-template>
						</xsl:for-each>
					</ol>
				</xsl:if>
				
			</xsl:when>
			<xsl:when test="string-length(@ref)">
				<xsl:call-template name="element-ref"><xsl:with-param name="name" select="@ref"/></xsl:call-template>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="extended-element">
		<xsl:param name="type"/>
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'element' and @type = $type]">
			<li>
				<a href="#element-{@name}"><xsl:value-of select="@name"/></a>
			</li>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="element-ref">
		<xsl:param name="name"/>
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'element' and @name = $name]">
			<xsl:call-template name="element"/>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="element-type">
		<xsl:param name="type"/>
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'complexType' and @name = $type]">
			<xsl:call-template name="type"/>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="child-attribute">
		<xsl:choose>
			<xsl:when test="string-length(@name)">
				<tr>
					<td><xsl:value-of select="@name"/></td>
					<td>
						<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
							<span class="child-attribute-description"><xsl:copy-of select="."/></span><br/>
						</xsl:for-each>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="@use = 'required'">Yes</xsl:when>
							<xsl:otherwise>No</xsl:otherwise>
						</xsl:choose>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="starts-with(@type, 'Kaltura')">
								<a href="/api_v3/testmeDoc/index.php?object={@type}"><xsl:value-of select="@type"/></a>
							</xsl:when>
							<xsl:when test="contains(@type, ':')">
								<xsl:value-of select="substring-after(@type, ':')"/>
							</xsl:when>
							<xsl:when test="string-length(@type) > 0">
								<xsl:value-of select="substring-after(@type, ':')"/>
							</xsl:when>
							<xsl:when test="count(*[local-name() = 'simpleType']/*[local-name() = 'restriction' and string-length(@base) > 0]) > 0">
								<xsl:for-each select="*[local-name() = 'simpleType']/*[local-name() = 'restriction' and string-length(@base) > 0]">
									<xsl:value-of select="substring-after(@base, ':')"/>
								</xsl:for-each>
							</xsl:when>
						</xsl:choose>
					</td>
					<td>
						<xsl:if test="count(*[local-name() = 'simpleType']/*[local-name() = 'restriction']/*) > 0">
							<xsl:for-each select="*[local-name() = 'simpleType']/*[local-name() = 'restriction']">
								<xsl:call-template name="restrictions"/>
							</xsl:for-each>
						</xsl:if>
					</td>
				</tr>
			</xsl:when>
			<xsl:when test="string-length(@ref)">
				<tr>
					<td><xsl:value-of select="@ref"/></td>
					<td>
						<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
							<span class="child-attribute-description"><xsl:copy-of select="."/></span><br/>
						</xsl:for-each>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="@use = 'required'">Yes</xsl:when>
							<xsl:otherwise>No</xsl:otherwise>
						</xsl:choose>
					</td>
					<td><xsl:call-template name="attribute-ref-type"><xsl:with-param name="name" select="@ref"/></xsl:call-template></td>
					<td></td>
				</tr>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="attribute-ref-type">
		<xsl:param name="name"/>
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'attribute' and @name = $name]">
			<xsl:value-of select="substring-after(@type, ':')"/>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="restrictions">
		<xsl:for-each select="*">
			<xsl:choose>
				<xsl:when test="local-name() = 'minInclusive'">
					 Minimum value: <xsl:value-of select="@value"/><br/>
				</xsl:when>
				<xsl:when test="local-name() = 'maxInclusive'">
					 Maximum value: <xsl:value-of select="@value"/><br/>
				</xsl:when>
				<xsl:when test="local-name() = 'pattern'">
					 Regular expression: '<xsl:value-of select="@value"/>'<br/>
				</xsl:when>
				<xsl:when test="local-name() = 'length'">
					 Length: <xsl:value-of select="@value"/> characters<br/>
				</xsl:when>
				<xsl:when test="local-name() = 'minLength'">
					 Minimum length: <xsl:value-of select="@value"/> characters<br/>
				</xsl:when>
				<xsl:when test="local-name() = 'maxLength'">
					 Maximum length: <xsl:value-of select="@value"/> characters<br/>
				</xsl:when>
				<xsl:when test="local-name() = 'fractionDigits'"></xsl:when>
				<xsl:when test="local-name() = 'totalDigits'"></xsl:when>
				<xsl:when test="local-name() = 'whiteSpace'"></xsl:when>
			</xsl:choose>
		</xsl:for-each>
		
		<xsl:if test="count(*[local-name() = 'enumeration']) > 1">
			Acceptable values:
			<ul>
				<xsl:for-each select="*[local-name() = 'enumeration']">
					<li><xsl:value-of select="@value"/></li>
				</xsl:for-each>
			</ul>
		</xsl:if>
	</xsl:template>

	<xsl:template name="child-extension">
		<xsl:param name="choiceCounter" />
		<xsl:param name="choiceIndex" />
		<xsl:param name="choiceSize" />
		<xsl:param name="trClass" />
		<xsl:param name="extension" select="@ref"/>
		
		<xsl:if test="count(/*[local-name() = 'schema']/*[local-name() = 'element' and @substitutionGroup = $extension]) > 0">
			<xsl:element name="tr">
				<xsl:attribute name="class">
					<xsl:if test="contains($trClass, 'choice')">choice</xsl:if>
				</xsl:attribute>
				
				<xsl:choose>
					<xsl:when test="$choiceCounter > 0">
						<td class="first extensions-title">Extensions:</td>
						<xsl:if test="position() = 1 or $choiceSize = 1">
							<xsl:element name="td">
								<xsl:attribute name="rowspan">
									<xsl:value-of select="$choiceSize + count(/*[local-name() = 'schema']/*[local-name() = 'element' and @substitutionGroup = $extension])"/>
								</xsl:attribute>
								<xsl:if test="contains($trClass, 'choice-end')">
									<xsl:attribute name="class">last-rowspan</xsl:attribute>
								</xsl:if>
								<xsl:text>Option </xsl:text>
								<xsl:value-of select="$choiceIndex"/>
							</xsl:element>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<td colspan="2" class="first extensions-title">Extensions:</td>
					</xsl:otherwise>
				</xsl:choose>
				<td colspan="5" class="last extensions-title"></td>
			</xsl:element>
		</xsl:if>
		
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'element' and @substitutionGroup = $extension]">
			<tr class="extension {$trClass}">
				<xsl:choose>
					<xsl:when test="$choiceCounter > 0">
						<td class="first"><a href="#element-{@name}"><xsl:value-of select="@name"/></a></td>
					</xsl:when>
					<xsl:otherwise>
						<td class="first" colspan="2"><a href="#element-{@name}"><xsl:value-of select="@name"/></a></td>
					</xsl:otherwise>
				</xsl:choose>
				<td>
					<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
						<span class="child-extension-description"><xsl:copy-of select="."/></span><br/>
					</xsl:for-each>
				</td>
				<td>No</td>
				<td>Unbounded</td>
				<td></td>
				<td class="last"></td>
			</tr>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="child-extended-elements">
		<xsl:param name="type" select="@base"/>
		
		<xsl:for-each select="/*[local-name() = 'schema']/*[(local-name() = 'complexType' or local-name() = 'simpleType') and @name = $type]/*">
			<xsl:call-template name="child-element">
				<xsl:with-param name="choiceCounter" select="0"/>
				<xsl:with-param name="choiceIndex" select="0"/>
				<xsl:with-param name="choiceSize" select="0"/>
				<xsl:with-param name="trClass" select="''"/>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="child-element">
		<xsl:param name="choiceCounter" />
		<xsl:param name="choiceIndex" />
		<xsl:param name="choiceSize" />
		<xsl:param name="trClass" />
		
		<xsl:choose>
		
			<xsl:when test="local-name() = 'attribute'"></xsl:when>
		
			<xsl:when test="local-name() = 'any'">
			
				<tr class="{$trClass}">			
					<xsl:choose>
						<xsl:when test="$choiceCounter > 0">
							<td class="first">[Any element]</td>
							<xsl:if test="position() = 1 or $choiceSize = 1">
								<td rowspan="{$choiceSize}">Option <xsl:value-of select="$choiceIndex"/></td>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<td class="first" colspan="2">[Any element]</td>
						</xsl:otherwise>
					</xsl:choose>
					<td>
						<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
							<span class="child-element-description"><xsl:copy-of select="."/></span><br/>
						</xsl:for-each>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="number(@minOccurs) = 0">No</xsl:when>
							<xsl:otherwise>Yes</xsl:otherwise>
						</xsl:choose>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="number(@maxOccurs) > 0"><xsl:value-of select="number(@maxOccurs)"/></xsl:when>
							<xsl:otherwise>Unbounded</xsl:otherwise>
						</xsl:choose>
					</td>
					<td>any type</td>
					<td class="last"></td>
				</tr>
			
			
			</xsl:when>
		
			<xsl:when test="local-name() = 'sequence'">
				<xsl:for-each select="*">
					<xsl:call-template name="child-element">
						<xsl:with-param name="choiceCounter" select="$choiceCounter"/>
						<xsl:with-param name="choiceIndex" select="$choiceIndex"/>
						<xsl:with-param name="choiceSize" select="count(../*)"/>
						<xsl:with-param name="trClass">
							<xsl:if test="contains($trClass, 'choice')">choice</xsl:if>
							<xsl:if test="contains($trClass, 'choice-start') and position() = 1"> choice-start</xsl:if>
							<xsl:if test="contains($trClass, 'choice-end') and position() = count(../*)"> choice-end</xsl:if>
						</xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</xsl:when>
			
			<xsl:when test="local-name() = 'choice'">
			
				<tr class="choice-head {$trClass}">
					<xsl:choose>
						<xsl:when test="$choiceCounter > 0">
							<td class="first">Choice</td>
							<xsl:if test="position() = 1 or $choiceSize = 1">
								<td rowspan="{$choiceSize}">Option <xsl:value-of select="$choiceIndex"/></td>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<td class="first" colspan="2">Choice</td>
						</xsl:otherwise>
					</xsl:choose>
					<td></td>
					<td>
						<xsl:choose>
							<xsl:when test="number(@minOccurs) = 0">No</xsl:when>
							<xsl:otherwise>Yes</xsl:otherwise>
						</xsl:choose>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="number(@maxOccurs) > 0"><xsl:value-of select="number(@maxOccurs)"/></xsl:when>
							<xsl:otherwise>Unbounded</xsl:otherwise>
						</xsl:choose>
					</td>
					<td></td>
					<td class="last">
						One of the following choices
					</td>
				</tr>
			
				<xsl:for-each select="*">
					<xsl:call-template name="child-element">
						<xsl:with-param name="choiceCounter" select="$choiceCounter + 1"/>
						<xsl:with-param name="choiceIndex" select="position()"/>
						<xsl:with-param name="choiceSize" select="1"/>
						<xsl:with-param name="trClass">
							<xsl:text>choice</xsl:text>
							<xsl:if test="position() = 1"> choice-start</xsl:if>
							<xsl:if test="position() = count(../*)"> choice-end</xsl:if>
						</xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
			</xsl:when>
			
			<xsl:when test="local-name() = 'complexContent' or local-name() = 'simpleContent'">
				<xsl:for-each select="*[local-name() = 'extension']">
					<tr class="extends-title">
						<td colspan="7">Extended from <a href="#type-{@base}"><xsl:value-of select="@base"/></a></td>
					</tr>
					
					<xsl:call-template name="child-extended-elements"/>

					<tr class="extends-title">
						<td colspan="7"> </td>
					</tr>
				</xsl:for-each>

				<xsl:for-each select="*[local-name() = 'extension']/*">
					<xsl:call-template name="child-element">
						<xsl:with-param name="choiceCounter" select="$choiceCounter"/>
						<xsl:with-param name="choiceIndex" select="$choiceIndex"/>
						<xsl:with-param name="choiceSize" select="1"/>
						<xsl:with-param name="trClass">
							<xsl:if test="contains($trClass, 'choice')">choice</xsl:if>
							<xsl:if test="contains($trClass, 'choice-start') and position() = 1"> choice-start</xsl:if>
							<xsl:if test="contains($trClass, 'choice-end') and position() = count(../*)"> choice-end</xsl:if>
						</xsl:with-param>
					</xsl:call-template>
				</xsl:for-each>
				
			</xsl:when>
					
			<xsl:when test="local-name() = 'element' and contains(@ref, '-extension')">
				<xsl:call-template name="child-extension">
					<xsl:with-param name="choiceCounter" select="$choiceCounter"/>
					<xsl:with-param name="choiceIndex" select="$choiceIndex"/>
					<xsl:with-param name="choiceSize" select="$choiceSize"/>
					<xsl:with-param name="trClass" select="$trClass" />
				</xsl:call-template>
			</xsl:when>
			
			<xsl:otherwise>

				<tr class="{$trClass}">			
					<xsl:choose>
						<xsl:when test="string-length(@name)">
							<xsl:choose>
								<xsl:when test="$choiceCounter > 0">
									<td class="first"><xsl:value-of select="@name"/></td>
									<xsl:if test="position() = 1 or $choiceSize = 1">
										<td rowspan="{$choiceSize}">Option <xsl:value-of select="$choiceIndex"/></td>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<td class="first" colspan="2"><xsl:value-of select="@name"/></td>
								</xsl:otherwise>
							</xsl:choose>
							<td>
								<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
									<span class="child-element-description"><xsl:copy-of select="."/></span><br/>
								</xsl:for-each>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="number(@minOccurs) = 0">No</xsl:when>
									<xsl:otherwise>Yes</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="number(@maxOccurs) > 0"><xsl:value-of select="number(@maxOccurs)"/></xsl:when>
									<xsl:otherwise>Unbounded</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="starts-with(@type, 'Kaltura')">
										<a href="/api_v3/testmeDoc/index.php?object={@type}"><xsl:value-of select="@type"/></a>
									</xsl:when>
									<xsl:when test="contains(@type, ':')">
										<xsl:value-of select="substring-after(@type, ':')"/>
									</xsl:when>
									<xsl:when test="string-length(@type) > 0">
										<xsl:value-of select="substring-after(@type, ':')"/>
									</xsl:when>
									<xsl:when test="count(*[local-name() = 'simpleType']/*[local-name() = 'restriction' and string-length(@base) > 0]) > 0">
										<xsl:for-each select="*[local-name() = 'simpleType']/*[local-name() = 'restriction' and string-length(@base) > 0]">
											<xsl:value-of select="substring-after(@base, ':')"/>
										</xsl:for-each>
									</xsl:when>
								</xsl:choose>
							</td>
							<td class="last">
								<xsl:if test="count(*[local-name() = 'simpleType']/*[local-name() = 'restriction']/*) > 0">
									<xsl:for-each select="*[local-name() = 'simpleType']/*[local-name() = 'restriction']">
										<xsl:call-template name="restrictions"/>
									</xsl:for-each>
								</xsl:if>
							</td>
						</xsl:when>
						<xsl:when test="string-length(@ref)">
							<xsl:choose>
								<xsl:when test="$choiceCounter > 0">
									<td class="first"><a href="#element-{@ref}"><xsl:value-of select="@ref"/></a></td>
									<xsl:if test="position() = 1 or $choiceSize = 1">
										<td rowspan="{$choiceSize}">Option <xsl:value-of select="$choiceIndex"/></td>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<td class="first" colspan="2"><a href="#element-{@ref}"><xsl:value-of select="@ref"/></a></td>
								</xsl:otherwise>
							</xsl:choose>
							<td>
								<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
									<span class="child-element-description"><xsl:copy-of select="."/></span><br/>
								</xsl:for-each>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="number(@minOccurs) = 0">No</xsl:when>
									<xsl:otherwise>Yes</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="number(@maxOccurs) > 0"><xsl:value-of select="number(@maxOccurs)"/></xsl:when>
									<xsl:otherwise>Unbounded</xsl:otherwise>
								</xsl:choose>
							</td>
							<td><xsl:call-template name="element-ref-type"><xsl:with-param name="name" select="@ref"/></xsl:call-template></td>
							<td class="last"></td>
						</xsl:when>
					</xsl:choose>
				</tr>
			
			</xsl:otherwise>
		</xsl:choose>
	
	</xsl:template>

	<xsl:template name="element-ref-type">
		<xsl:param name="name"/>
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'element' and @name = $name]">
			<xsl:value-of select="substring-after(@type, ':')"/>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="type">
		<xsl:if test="count(*[local-name() = 'attribute']) > 0 or count(*[local-name() = 'simpleContent']/*[local-name() = 'extension']/*[local-name() = 'attribute']) > 0">
			<br/>
			<span class="child-attributes">Attributes</span><br/>
			<table class="child-attributes-table" cellspacing="0">
				<thead>
					<tr>
						<th>Attribute Name</th>
						<th>Description</th>
						<th>Required</th>
						<th>Type</th>
						<th>Restrictions</th>
					</tr>
				</thead>
				<tbody>
					<xsl:for-each select="*[local-name() = 'attribute']">
						<xsl:call-template name="child-attribute"/>
					</xsl:for-each>
					<xsl:for-each select="*[local-name() = 'simpleContent']/*[local-name() = 'extension']/*[local-name() = 'attribute']">
						<xsl:call-template name="child-attribute"/>
					</xsl:for-each>
				</tbody>
			</table>
		</xsl:if>
		
		<xsl:if test="count(*[not(local-name() = 'attribute') and not(local-name() = 'annotation')]) > 0">
			<br/>
			<span class="child-elements">Sub-Elements</span><br/>
			<table class="child-elements-table" cellspacing="0">
				<thead>
					<tr>
						<th colspan="2">Element Name</th>
						<th>Description</th>
						<th>Required</th>
						<th>Maximum Appearances</th>
						<th>Type</th>
						<th>Restrictions</th>
					</tr>
				</thead>
				<tbody>
					<xsl:for-each select="*">
						<xsl:call-template name="child-element">
							<xsl:with-param name="choiceCounter" select="0"/>
							<xsl:with-param name="choiceIndex" select="0"/>
							<xsl:with-param name="choiceSize" select="count(*)"/>
							<xsl:with-param name="trClass" select="''"/>
						</xsl:call-template>
					</xsl:for-each>
				</tbody>
			</table>
		</xsl:if>
		
		<br/>
	</xsl:template>

	<xsl:template match="/">
		<div class="code">
			<xsl:apply-templates mode="escape" />
		</div>

		<xsl:for-each select="*[local-name() = 'schema']/*[local-name() = 'element' and not(contains(@name, '-extension'))]">
			<xsl:call-template name="element" />
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>