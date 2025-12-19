const { DateTime } = require("luxon");

module.exports = function(eleventyConfig) {
  // Copy assets to output
  eleventyConfig.addPassthroughCopy("content/assets");
  eleventyConfig.addPassthroughCopy("content/**/assets");
  // Preserve WordPress uploads for SEO
  eleventyConfig.addPassthroughCopy("content/wp-content");

  // Date filters
  eleventyConfig.addFilter("readableDate", (dateObj) => {
    return DateTime.fromJSDate(dateObj, { zone: "utc" }).toFormat("MMMM d, yyyy");
  });

  eleventyConfig.addFilter("dateToISO", (dateObj) => {
    return DateTime.fromJSDate(dateObj, { zone: "utc" }).toISO();
  });

  eleventyConfig.addFilter("dateToRfc3339", (dateObj) => {
    return DateTime.fromJSDate(dateObj, { zone: "utc" }).toISO();
  });

  eleventyConfig.addFilter("dateToRfc822", (dateObj) => {
    return DateTime.fromJSDate(dateObj, { zone: "utc" }).toRFC2822();
  });

  // Get newest collection item date
  eleventyConfig.addFilter("getNewestCollectionItemDate", (collection) => {
    if (!collection || !collection.length) {
      return new Date();
    }
    return new Date(Math.max(...collection.map(item => item.date)));
  });

  // Limit filter
  eleventyConfig.addFilter("limit", (array, limit) => {
    return array.slice(0, limit);
  });

  // Decode HTML entities
  eleventyConfig.addFilter("decodeHtml", (str) => {
    if (!str) return str;
    const entities = {
      '&#8217;': "'", '&#8216;': "'", '&#8220;': '"', '&#8221;': '"',
      '&#8230;': '...', '&#x2122;': '™', '&#038;': '&', '&amp;': '&',
      '&#8211;': '–', '&#8212;': '—', '&quot;': '"', '&#039;': "'",
      '&lt;': '<', '&gt;': '>'
    };
    // Replace both numeric (&#8217;) and named (&amp;) entities
    return str.replace(/&#?[\w\d]+;/g, match => entities[match] || match);
  });

  // Escape HTML
  eleventyConfig.addFilter("escape", (str) => {
    return str.replace(/&/g, "&amp;")
              .replace(/</g, "&lt;")
              .replace(/>/g, "&gt;")
              .replace(/"/g, "&quot;")
              .replace(/'/g, "&#039;");
  });

  // Convert relative URLs to absolute
  eleventyConfig.addFilter("htmlToAbsoluteUrls", (htmlContent, baseUrl) => {
    if (!htmlContent) return htmlContent;
    return htmlContent
      .replace(/href="\//g, `href="${baseUrl}/`)
      .replace(/src="\//g, `src="${baseUrl}/`);
  });

  // Find MP3 URL in content
  eleventyConfig.addFilter("findMp3Url", (content) => {
    if (!content) return null;
    const match = content.match(/\]\((\/assets\/podcasts\/[^)]+\.mp3)/);
    return match ? match[1] : null;
  });

  // Extract excerpt from content
  eleventyConfig.addFilter("excerpt", (content) => {
    if (!content) return "";
    // Decode HTML entities first
    const entities = {
      '&lt;': '<', '&gt;': '>', '&amp;': '&', '&quot;': '"', '&#39;': "'",
      '&nbsp;': ' ', '&#8217;': "'", '&#8216;': "'", '&#8220;': '"',
      '&#8221;': '"', '&#8211;': '–', '&#8212;': '—'
    };
    let decoded = content.replace(/&[#\w]+;/g, match => entities[match] || '');

    // Strip HTML tags and get plain text
    let excerpt = decoded
      .replace(/<[^>]+>/g, '') // Remove HTML tags
      .replace(/\s+/g, ' ') // Normalize whitespace
      .trim();

    // Truncate to ~150 characters at word boundary
    if (excerpt.length > 150) {
      excerpt = excerpt.substring(0, 150);
      excerpt = excerpt.substring(0, excerpt.lastIndexOf(' ')) + '...';
    }
    return excerpt;
  });

  // URL encode filter for sharing URLs
  eleventyConfig.addFilter("urlencode", (str) => {
    if (!str) return "";
    return encodeURIComponent(str);
  });

  // Set input and output directories
  return {
    dir: {
      input: "content",
      output: "_site",
      includes: "../11ty/_includes",
      data: "../11ty/_data"
    },
    markdownTemplateEngine: "njk",
    htmlTemplateEngine: "njk",
    templateFormats: ["md", "njk", "html"]
  };
};
