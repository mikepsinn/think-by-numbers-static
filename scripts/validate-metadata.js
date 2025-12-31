/**
 * Eleventy plugin to validate metadata on posts and pages
 * Throws build errors if critical metadata is missing
 */

const fs = require('fs');
const path = require('path');

// Store all validation issues globally
const allValidationIssues = [];

module.exports = function(eleventyConfig) {
  eleventyConfig.on('eleventy.before', ({ runMode }) => {
    console.log('[Metadata Validator] Starting validation...');
    // Clear issues from previous run
    allValidationIssues.length = 0;
  });

  // Use collection to validate all posts/pages
  eleventyConfig.addCollection("_validateMetadata", function(collectionApi) {
    const allItems = collectionApi.getAll();

    allItems.forEach(item => {
      // Only validate markdown files from content directory
      if (!item.inputPath ||
          !item.inputPath.includes('/content/') ||
          !item.inputPath.endsWith('.md')) {
        return;
      }

      const data = item.data;
      const inputPath = item.inputPath;
      const errors = [];
      const warnings = [];

      // Skip if no title (not a real post/page)
      if (!data.title || data.title.trim() === '') {
        return;
      }

      // Required: Explicit description (no auto-excerpt)
      if (!data.description || data.description.trim() === '') {
        errors.push(`Missing description (auto-excerpt not allowed - must provide explicit description in frontmatter)`);
      }

      // Required for posts: Featured image
      if (data.type !== 'page' && data.metadata?.type === 'wordpress') {
        if (!data.metadata?.media?.featuredImage && !data.featuredImage) {
          warnings.push(`Missing featured image (og:image will use default)`);
        }
      }

      // Required: Date for posts
      if (data.type !== 'page' && !data.date) {
        errors.push(`Missing date`);
      }

      // Recommended: Tags
      if (!data.tags || data.tags.length === 0) {
        warnings.push(`No tags specified`);
      }

      // Recommended: Categories for WordPress posts
      if (data.metadata?.type === 'wordpress' && (!data.metadata?.categories || data.metadata.categories.length === 0)) {
        warnings.push(`No categories specified`);
      }

      // Validate image files exist on disk
      if (data.metadata?.media?.featuredImage) {
        const imagePath = path.join(process.cwd(), 'content', data.metadata.media.featuredImage.replace(/^\//, ''));
        if (!fs.existsSync(imagePath)) {
          warnings.push(`Featured image file not found: ${data.metadata.media.featuredImage}`);
        }
      }

      if (data.metadata?.media?.ogImage) {
        const imagePath = path.join(process.cwd(), 'content', data.metadata.media.ogImage.replace(/^\//, ''));
        if (!fs.existsSync(imagePath)) {
          warnings.push(`OG image file not found: ${data.metadata.media.ogImage}`);
        }
      }

      if (data.metadata?.media?.infographic) {
        const imagePath = path.join(process.cwd(), 'content', data.metadata.media.infographic.replace(/^\//, ''));
        if (!fs.existsSync(imagePath)) {
          warnings.push(`Infographic file not found: ${data.metadata.media.infographic}`);
        }
      }

      // Combine errors and warnings - all fail the build
      const allIssues = [...errors, ...warnings];

      if (allIssues.length > 0) {
        // Store issues for markdown output
        allValidationIssues.push({
          path: inputPath,
          issues: allIssues
        });
      }
    });

    // If there are any issues, write to file and throw error
    if (allValidationIssues.length > 0) {
      const todoContent = generateTodoMarkdown(allValidationIssues);
      const todoPath = path.join(process.cwd(), 'METADATA-ISSUES.md');
      fs.writeFileSync(todoPath, todoContent, 'utf8');

      console.error(`\n[METADATA VALIDATION FAILED]`);
      console.error(`Found ${allValidationIssues.length} file(s) with issues.`);
      console.error(`See METADATA-ISSUES.md for details.\n`);

      throw new Error(`Metadata validation failed. ${allValidationIssues.length} files have issues. See METADATA-ISSUES.md`);
    }

    return [];
  });

  console.log('[Metadata Validator] Plugin loaded. All validation issues will be written to METADATA-ISSUES.md');
};

function generateTodoMarkdown(issues) {
  const timestamp = new Date().toISOString();
  let markdown = `# Metadata Validation Issues\n\n`;
  markdown += `**Generated:** ${timestamp}\n\n`;
  markdown += `**Total Files with Issues:** ${issues.length}\n\n`;
  markdown += `---\n\n`;

  issues.forEach(({ path, issues: fileIssues }, index) => {
    markdown += `## ${index + 1}. ${path}\n\n`;
    fileIssues.forEach(issue => {
      markdown += `- [ ] ${issue}\n`;
    });
    markdown += `\n`;
  });

  markdown += `---\n\n`;
  markdown += `## How to Fix\n\n`;
  markdown += `1. Add missing metadata to the frontmatter of each file\n`;
  markdown += `2. For missing descriptions: Add a \`description:\` field to frontmatter (explicit description required, auto-excerpt not allowed)\n`;
  markdown += `3. For missing featured images: Add \`metadata.media.featuredImage:\` path\n`;
  markdown += `4. For missing tags: Add a \`tags:\` array\n`;
  markdown += `5. For missing categories: Add \`metadata.categories:\` array\n`;
  markdown += `6. Run \`npm run build\` to re-validate\n\n`;

  return markdown;
}
