#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const yaml = require('js-yaml');

/**
 * Extract title and description from markdown frontmatter
 * Generates posts-metadata.yml file with all posts
 */

function extractMetadata() {
  const contentDir = path.join(__dirname, '..', 'content');
  const posts = [];

  function walkDir(dir) {
    const files = fs.readdirSync(dir);

    for (const file of files) {
      const filePath = path.join(dir, file);
      const stat = fs.statSync(filePath);

      if (stat.isDirectory()) {
        walkDir(filePath);
      } else if (file.endsWith('.md')) {
        try {
          const content = fs.readFileSync(filePath, 'utf-8');

          // Check if file starts with frontmatter
          if (content.startsWith('---')) {
            // Extract frontmatter
            const match = content.match(/^---\n([\s\S]*?)\n---\n/);
            if (match) {
              const frontmatterText = match[1];
              try {
                const frontmatter = yaml.load(frontmatterText);

                // Get relative path from content directory
                const relPath = path.relative(contentDir, filePath).replace(/\\/g, '/');

                // Extract title and description
                const title = frontmatter?.title;
                const description = frontmatter?.description;

                // Only include if both exist
                if (title && description) {
                  posts.push({
                    file: relPath,
                    title: String(title),
                    description: String(description),
                  });
                }
              } catch (yamlError) {
                console.error(`YAML Error in ${filePath}:`, yamlError.message);
              }
            }
          }
        } catch (error) {
          console.error(`Error reading ${filePath}:`, error.message);
        }
      }
    }
  }

  // Walk the content directory
  walkDir(contentDir);

  // Sort by file path
  posts.sort((a, b) => a.file.localeCompare(b.file));

  // Generate YAML output
  const output = {
    posts: posts,
  };

  const yamlOutput = yaml.dump(output, {
    lineWidth: -1,
    quotingType: '"',
  });

  // Write to file
  const outputPath = path.join(__dirname, '..', 'posts-metadata.yml');
  fs.writeFileSync(outputPath, yamlOutput, 'utf-8');

  console.log(`✓ Extracted metadata from ${posts.length} posts`);
  console.log(`✓ Written to: ${outputPath}`);
}

extractMetadata();
