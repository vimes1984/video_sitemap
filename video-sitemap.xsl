<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
    xmlns:html="http://www.w3.org/TR/REC-html40"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>XML Video Sitemap</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css">body {
          font-family:Georgia, serif;
          font-size:12px;
          background-color:#eee;
          width:1030px;
          margin:20px auto;
          }
          
          a {
          border:none;
          }
          
          a:link {
          color:#fff;
          text-decoration:none;
          }
          
          .labnol {
          position:relative;
          float:left;
          border:2px solid #000;
          margin:5px;
          }
          
          p {
          position:absolute;
          top:130px;
          width:200px;
          color:#ddd;
          background:#222;
          font-style:italic;
          line-height:18px;
          opacity:0.9;
          filter:alpha(opacity=90);
          margin:0;
          padding:0 5px;
          }
		</style>
      </head>
      <body>
        <xsl:for-each select="sitemap:urlset/sitemap:url">
          <xsl:variable name="u">
            <xsl:value-of select="sitemap:loc"/>
          </xsl:variable>
          <xsl:variable name="t">
            <xsl:value-of select="video:video/video:thumbnail_loc"/>
          </xsl:variable>
          <a href="{$u}" target="_blank">
            <div class="labnol">
              <img src="{$t}" width="240" height="180" />
              <p>
                <xsl:value-of select="video:video/video:title"/>
              </p>
            </div>
          </a>
        </xsl:for-each>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
