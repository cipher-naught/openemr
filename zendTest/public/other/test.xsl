<?xml version='1.0' ?>
<xsl:stylesheet xmlns:xsl='http://www.w3.org/1999/XSL/Transform' version='1.0'>
<!--<xsl:import href="createTables.sql.xsl"/> -->
<xsl:output method='text'/>

<xsl:template match="/">
<xsl:apply-templates/>
</xsl:template>
<xsl:template name="escapesinglequotes">
 <xsl:param name="arg1"/>
 <xsl:variable name="apostrophe">'</xsl:variable>
 <xsl:choose>
  <!-- this string has at least on single quote -->
  <xsl:when test="contains($arg1, $apostrophe)">
  <xsl:if test="string-length(normalize-space(substring-before($arg1, $apostrophe))) > 0"><xsl:value-of select="substring-before($arg1, $apostrophe)" disable-output-escaping="yes"/>''</xsl:if>
   <xsl:call-template name="escapesinglequotes">
    <xsl:with-param name="arg1"><xsl:value-of select="substring-after($arg1, $apostrophe)" disable-output-escaping="yes"/></xsl:with-param>
   </xsl:call-template>
  </xsl:when>
  <!-- no quotes found in string, just print it -->
  <xsl:when test="string-length(normalize-space($arg1)) > 0"><xsl:value-of select="$arg1"/></xsl:when>
 </xsl:choose>
</xsl:template>

<xsl:template name="escapedoublequotes">
 <xsl:param name="arg1"/>
 <xsl:variable name="doublequote">"</xsl:variable>
 <xsl:choose>
  <!-- this string has at least on single quote -->
  <xsl:when test="contains($arg1, $doublequote)">
  <xsl:if test="string-length(normalize-space(substring-before($arg1, $doublequote))) > 0"><xsl:value-of select="substring-before($arg1, $doublequote)" disable-output-escaping="yes"/>""</xsl:if>
   <xsl:call-template name="escapedoublequotes">
    <xsl:with-param name="arg1"><xsl:value-of select="substring-after($arg1, $doublequote)" disable-output-escaping="yes"/></xsl:with-param>
   </xsl:call-template>
  </xsl:when>
  <!-- no quotes found in string, just print it -->
  <xsl:when test="string-length(normalize-space($arg1)) > 0"><xsl:value-of select="$arg1"/></xsl:when>
 </xsl:choose>
</xsl:template>

<!-- =================================================================== -->
<!-- =================================================================== -->
<!-- =================================================================== -->

<xsl:template match="document">
<xsl:apply-templates select='data/row'/>
</xsl:template>
<!-- =================================================================== -->
<!-- =================================================================== -->
<!-- =================================================================== -->
<!-- Need to work with Intervention and eligibility,location, condition_browse-->
<!-- Will need to clean this up by the columns that are being sent in. -->
<xsl:template match="row">
insert ignore into patient_data(title, language, financial, fname, lname, mname, DOB, street, postal_code, city, state, country_code, drivers_license) values
(

);

<!--<xsl:apply-templates select='MedlineCitation/Article/AuthorList' /> -->
</xsl:template>

<!-- =================================================================== -->
<!-- =================================================================== -->
<!-- =================================================================== -->

<xsl:template match="AuthorList">
<xsl:apply-templates select='Author' />
</xsl:template>

<!-- =================================================================== -->
<!-- =================================================================== -->
<!-- =================================================================== -->

<xsl:template match="Author">
insert ignore into author(forename,lastname) values
(
&quot;<xsl:value-of select='ForeName' />&quot;,
&quot;<xsl:value-of select='LastName' />&quot;
);

set @authorid:= select uid from author where
forename=&quot;<xsl:value-of select='ForeName' />&quot; and
lastname=&quot;<xsl:value-of select='LastName' />&quot;
;

insert into paper2author(pmid,athorid) values (@pmid,@authorid);

</xsl:template>

</xsl:stylesheet>