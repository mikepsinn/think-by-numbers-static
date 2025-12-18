module.exports = function(eleventyConfig) {
  // Copy assets to output
  eleventyConfig.addPassthroughCopy("11ty/assets");
  eleventyConfig.addPassthroughCopy("11ty/**/assets");

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
