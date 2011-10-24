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
			
				<div class="box">
					<a name="element-{@name}"/><a name="type-{$type}"/>
					<h3>
						<span class="element-title"><xsl:value-of select="@name"/> element</span>
						<xsl:text> (</xsl:text>
						<xsl:for-each select="/*[local-name() = 'schema']/*[(local-name() = 'complexType' or local-name() = 'simpleType') and @name = $type]">
							<xsl:if test="@abstract = 'true'">abstract </xsl:if>
						</xsl:for-each>
						<xsl:value-of select="$type"/>
						<xsl:text>)</xsl:text>
					</h3>
					<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
						<p><xsl:copy-of select="."/></p>
					</xsl:for-each>
					<xsl:call-template name="element-type"><xsl:with-param name="type" select="@type"/></xsl:call-template>
				
					<xsl:if test="count(*[local-name() = 'annotation']/*[local-name() = 'appinfo']/*[local-name() = 'example']/*) > 0">
						<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'appinfo']/*[local-name() = 'example']">
							<h4 class="element-example-title">
								<xsl:choose>
									<xsl:when test="string-length(@title) > 0"><xsl:value-of select="@title"/></xsl:when>
									<xsl:otherwise>XML Example</xsl:otherwise>
								</xsl:choose>
							</h4>
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
				</div>
				
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
				<li>
					<xsl:value-of select="@name"/>
					<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
						<p class="child-attribute-description"><xsl:copy-of select="."/></p>
					</xsl:for-each>
					<ul>
						<xsl:if test="@use = 'required'">
							<li>Required</li>
						</xsl:if>
						<xsl:choose>
							<xsl:when test="starts-with(@type, 'Kaltura')">
								<li class="kaltura-object">Type: <a href="#" onclick="KDoc.openObject('{@type}')"><xsl:value-of select="@type"/></a></li>
							</xsl:when>
							<xsl:when test="contains(@type, ':')">
								<li class="xsd-extension">Type: <xsl:value-of select="substring-after(@type, ':')"/></li>
							</xsl:when>
							<xsl:when test="string-length(@type) > 0">
								<li class="xsd">Type: <xsl:value-of select="substring-after(@type, ':')"/></li>
							</xsl:when>
							<xsl:when test="count(*[local-name() = 'simpleType']/*[local-name() = 'restriction' and string-length(@base) > 0]) > 0">
								<xsl:for-each select="*[local-name() = 'simpleType']/*[local-name() = 'restriction' and string-length(@base) > 0]">
									<li class="xsd-restriction">Type: <xsl:value-of select="substring-after(@base, ':')"/></li>
								</xsl:for-each>
							</xsl:when>
						</xsl:choose>
						<xsl:if test="count(*[local-name() = 'simpleType']/*[local-name() = 'restriction']/*) > 0">
							<xsl:for-each select="*[local-name() = 'simpleType']/*[local-name() = 'restriction']">
								<xsl:call-template name="restrictions"/>
							</xsl:for-each>
						</xsl:if>
					</ul>
				</li>
			</xsl:when>
			<xsl:when test="string-length(@ref)">
				<li>
					<xsl:value-of select="@ref"/>
					<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
						<p class="child-attribute-description"><xsl:copy-of select="."/></p>
					</xsl:for-each>
					<ul>
						<xsl:if test="@use = 'required'">
							<li>Required</li>
						</xsl:if>
						<xsl:call-template name="attribute-ref-type"><xsl:with-param name="name" select="@ref"/></xsl:call-template>
					</ul>
				</li>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="attribute-ref-type">
		<xsl:param name="name"/>
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'attribute' and @name = $name]">
			<li class="attribute-ref-type">Type: <xsl:value-of select="substring-after(@type, ':')"/></li>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="restrictions">
		<xsl:for-each select="*">
			<xsl:choose>
				<xsl:when test="local-name() = 'minInclusive'">
					 <li>Minimum value: <xsl:value-of select="@value"/></li>
				</xsl:when>
				<xsl:when test="local-name() = 'maxInclusive'">
					 <li>Maximum value: <xsl:value-of select="@value"/></li>
				</xsl:when>
				<xsl:when test="local-name() = 'pattern'">
					 <li>Regular expression: '<xsl:value-of select="@value"/>'</li>
				</xsl:when>
				<xsl:when test="local-name() = 'length'">
					 <li>Length: <xsl:value-of select="@value"/> characters</li>
				</xsl:when>
				<xsl:when test="local-name() = 'minLength'">
					 <li>Minimum length: <xsl:value-of select="@value"/> characters</li>
				</xsl:when>
				<xsl:when test="local-name() = 'maxLength'">
					 <li>Maximum length: <xsl:value-of select="@value"/> characters</li>
				</xsl:when>
				<xsl:when test="local-name() = 'fractionDigits'"></xsl:when>
				<xsl:when test="local-name() = 'totalDigits'"></xsl:when>
				<xsl:when test="local-name() = 'whiteSpace'"></xsl:when>
			</xsl:choose>
		</xsl:for-each>
		
		<xsl:if test="count(*[local-name() = 'enumeration']) > 1">
			<li>Acceptable values:
				<ul>
					<xsl:for-each select="*[local-name() = 'enumeration']">
						<li><xsl:value-of select="@value"/></li>
					</xsl:for-each>
				</ul>
			</li>
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
						<h4 class="first extensions-title">Extensions:</h4>
						<xsl:if test="position() = 1 or $choiceSize = 1">
							<xsl:element name="span">
								<xsl:if test="contains($trClass, 'choice-end')">
									<xsl:attribute name="class">last-rowspan</xsl:attribute>
								</xsl:if>
								<xsl:text>Option </xsl:text>
								<xsl:value-of select="$choiceIndex"/>
							</xsl:element>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<h4 class="first extensions-title">Extensions:</h4>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:element>
		</xsl:if>
		
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'element' and @substitutionGroup = $extension]">
			<li class="extension {$trClass}">
				<xsl:choose>
					<xsl:when test="$choiceCounter > 0">
						<span><a href="#element-{@name}"><xsl:value-of select="@name"/></a></span>
					</xsl:when>
					<xsl:otherwise>
						<span><a href="#element-{@name}"><xsl:value-of select="@name"/></a></span>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
					<p class="child-extension-description"><xsl:copy-of select="."/></p>
				</xsl:for-each>
			</li>
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
			
				<li class="{$trClass}">
					<xsl:choose>
						<xsl:when test="$choiceCounter > 0">
							<span>[Any element]</span>
							<xsl:if test="position() = 1 or $choiceSize = 1">
								<span>Option <xsl:value-of select="$choiceIndex"/></span>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<span>[Any element]</span>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
						<p class="child-element-description"><xsl:copy-of select="."/></p>
					</xsl:for-each>
					<ul>
						<xsl:if test="number(@minOccurs) > 0">
							<li>Required</li>
						</xsl:if>
						<xsl:if test="number(@maxOccurs) > 0">
							<li>Maximum Appearances: <xsl:value-of select="number(@maxOccurs)"/></li>
						</xsl:if>
					</ul>
				</li>
			
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
			
				<li class="choice-head {$trClass}">
					<span>Choice</span>
					<ul>
						<xsl:if test="number(@minOccurs) > 0">
							<li>Required</li>
						</xsl:if>
						<xsl:if test="number(@maxOccurs) > 0">
							<li>Maximum Appearances: <xsl:value-of select="number(@maxOccurs)"/></li>
						</xsl:if>
						<li>
							<img class="choice-{generate-id(.)}" src="images/expended.gif" onclick="KDoc.toggleItem('choice-{generate-id(.)}')"/>
							<img class="choice-{generate-id(.)}" src="images/collapsed.gif" onclick="KDoc.toggleItem('choice-{generate-id(.)}')" style="display: none;"/>
							<span>One of the following choices:</span>
							<ol class="choice-{generate-id(.)}">
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
							</ol>
						</li>
					</ul>
				</li>
			
			</xsl:when>
			
			<xsl:when test="local-name() = 'complexContent' or local-name() = 'simpleContent'">
				<xsl:for-each select="*[local-name() = 'extension']">
					<li class="extends-title">
						Extended from <a href="#type-{@base}"><xsl:value-of select="@base"/></a>
					</li>
					
					<xsl:call-template name="child-extended-elements"/>
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

				<li class="{$trClass}">			
					<xsl:choose>
						<xsl:when test="string-length(@name)">
							<xsl:choose>
								<xsl:when test="$choiceCounter > 0">
									<span><xsl:value-of select="@name"/></span>
									<xsl:if test="position() = 1 or $choiceSize = 1">
										<span> (Option <xsl:value-of select="$choiceIndex"/>)</span>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<span><xsl:value-of select="@name"/></span>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
								<p class="child-element-description"><xsl:copy-of select="."/></p>
							</xsl:for-each>
							<ul>
								<xsl:if test="number(@minOccurs) > 0">
									<li>Required</li>
								</xsl:if>
								<xsl:if test="number(@maxOccurs) > 0">
									<li>Maximum Appearances: <xsl:value-of select="number(@maxOccurs)"/></li>
								</xsl:if>
								<xsl:choose>
									<xsl:when test="starts-with(@type, 'Kaltura')">
										<li class="kaltura-object">Type: <a href="#" onclick="KDoc.openObject('{@type}')"><xsl:value-of select="@type"/></a></li>
									</xsl:when>
									<xsl:when test="contains(@type, ':')">
										<li class="xsd-extension">Type: <xsl:value-of select="substring-after(@type, ':')"/></li>
									</xsl:when>
									<xsl:when test="string-length(@type) > 0">
										<li class="xsd">Type: <xsl:value-of select="substring-after(@type, ':')"/></li>
									</xsl:when>
									<xsl:when test="count(*[local-name() = 'simpleType']/*[local-name() = 'restriction' and string-length(@base) > 0]) > 0">
										<xsl:for-each select="*[local-name() = 'simpleType']/*[local-name() = 'restriction' and string-length(@base) > 0]">
											<li class="xsd-restriction">Type: <xsl:value-of select="substring-after(@base, ':')"/></li>
										</xsl:for-each>
									</xsl:when>
								</xsl:choose>
								<xsl:if test="count(*[local-name() = 'simpleType']/*[local-name() = 'restriction']/*) > 0">
									<xsl:for-each select="*[local-name() = 'simpleType']/*[local-name() = 'restriction']">
										<xsl:call-template name="restrictions"/>
									</xsl:for-each>
								</xsl:if>
							</ul>
						</xsl:when>
						<xsl:when test="string-length(@ref)">
							<xsl:choose>
								<xsl:when test="$choiceCounter > 0">
									<span><a href="#element-{@ref}"><xsl:value-of select="@ref"/></a></span>
									<xsl:if test="position() = 1 or $choiceSize = 1">
										<span>Option <xsl:value-of select="$choiceIndex"/></span>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<span><a href="#element-{@ref}"><xsl:value-of select="@ref"/></a></span>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:for-each select="*[local-name() = 'annotation']/*[local-name() = 'documentation']">
								<p class="child-element-description"><xsl:copy-of select="."/></p>
							</xsl:for-each>
							<ul>
								<xsl:if test="number(@minOccurs) > 0">
									<li>Required</li>
								</xsl:if>
								<xsl:if test="number(@maxOccurs) > 0">
									<li>Maximum Appearances: <xsl:value-of select="number(@maxOccurs)"/></li>
								</xsl:if>
							</ul>
						</xsl:when>
					</xsl:choose>
				</li>
			
			</xsl:otherwise>
		</xsl:choose>
	
	</xsl:template>

	<xsl:template name="element-ref-type">
		<xsl:param name="name"/>
		<xsl:for-each select="/*[local-name() = 'schema']/*[local-name() = 'element' and @name = $name]">
			<li class="element-ref-type">Type: <xsl:value-of select="substring-after(@type, ':')"/></li>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="type">
		<xsl:if test="count(*[local-name() = 'attribute']) > 0 or count(*[local-name() = 'simpleContent']/*[local-name() = 'extension']/*[local-name() = 'attribute']) > 0">
			<h4 class="child-attributes">Attributes</h4>
			<ul class="child-attributes-table">
				<xsl:for-each select="*[local-name() = 'attribute']">
					<xsl:call-template name="child-attribute"/>
				</xsl:for-each>
				<xsl:for-each select="*[local-name() = 'simpleContent']/*[local-name() = 'extension']/*[local-name() = 'attribute']">
					<xsl:call-template name="child-attribute"/>
				</xsl:for-each>
			</ul>
		</xsl:if>
		
		<xsl:if test="count(*[not(local-name() = 'attribute') and not(local-name() = 'annotation')]) > 0">
			<h4 class="child-elements">Sub-Elements</h4>
			<ul>
				<xsl:for-each select="*">
					<xsl:call-template name="child-element">
						<xsl:with-param name="choiceCounter" select="0"/>
						<xsl:with-param name="choiceIndex" select="0"/>
						<xsl:with-param name="choiceSize" select="count(*)"/>
						<xsl:with-param name="trClass" select="''"/>
					</xsl:call-template>
				</xsl:for-each>
			</ul>
		</xsl:if>
		
		<br/>
	</xsl:template>

	<xsl:template match="/">
		<div class="box">
			<h3>XSD</h3>
			<div class="code">
				<xsl:apply-templates mode="escape" />
			</div>
		</div>

		<xsl:for-each select="*[local-name() = 'schema']/*[local-name() = 'element' and not(contains(@name, '-extension'))]">
			<xsl:call-template name="element" />
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>