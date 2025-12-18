const { DateTime } = require("luxon");

module.exports = function(eleventyConfig) {
  // Copy assets to output
  eleventyConfig.addPassthroughCopy("11ty/assets");
  eleventyConfig.addPassthroughCopy("11ty/**/assets");

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

  // Set input and output directories
  return {
    dir: {
      input: "11ty",
      output: "_site",
      includes: "_includes",
      data: "_data"
    },
    markdownTemplateEngine: "njk",
    htmlTemplateEngine: "njk",
    templateFormats: ["md", "njk", "html"]
  };
};
