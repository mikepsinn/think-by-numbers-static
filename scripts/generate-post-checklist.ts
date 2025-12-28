#!/usr/bin/env node
import { glob } from 'glob';
import * as fs from 'fs';
import * as path from 'path';
import matter from 'gray-matter';

interface PostInfo {
  path: string;
  title?: string;
  category: string;
}

async function generatePostChecklist() {
  const contentDir = path.join(process.cwd(), 'content');
  const outputFile = path.join(process.cwd(), 'POST-CHECKLIST.md');

  console.log('Scanning content directory:', contentDir);

  // Find all markdown files
  const files = await glob('**/*.md', { cwd: contentDir });

  console.log(`Found ${files.length} markdown files`);

  // Organize posts by category
  const postsByCategory: { [key: string]: PostInfo[] } = {};

  for (const file of files) {
    const fullPath = path.join(contentDir, file);
    const content = fs.readFileSync(fullPath, 'utf-8');

    // Parse frontmatter to get title
    let title: string | undefined;
    try {
      const { data } = matter(content);
      title = data.title;
    } catch (error) {
      // If frontmatter parsing fails, just use filename
    }

    // Determine category from directory structure
    const parts = file.split(path.sep);
    const category = parts.length > 1 ? parts[0] : 'root';

    if (!postsByCategory[category]) {
      postsByCategory[category] = [];
    }

    postsByCategory[category].push({
      path: file,
      title,
      category,
    });
  }

  // Sort categories alphabetically
  const sortedCategories = Object.keys(postsByCategory).sort();

  // Generate markdown checklist
  let markdown = '# Think by Numbers - Post Checklist\n\n';
  markdown += `Generated: ${new Date().toISOString()}\n\n`;
  markdown += `**Total Posts: ${files.length}**\n\n`;
  markdown += '---\n\n';

  // Table of Contents
  markdown += '## Table of Contents\n\n';
  sortedCategories.forEach(category => {
    const count = postsByCategory[category].length;
    const categoryName = category === 'root'
      ? 'Standalone Pages'
      : category.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    markdown += `- [${categoryName}](#${category.toLowerCase().replace(/\s+/g, '-')}) (${count} posts)\n`;
  });

  markdown += '\n---\n\n';

  // Generate checklist for each category
  sortedCategories.forEach(category => {
    const posts = postsByCategory[category];
    const categoryName = category === 'root'
      ? 'Standalone Pages'
      : category.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');

    markdown += `## ${categoryName}\n\n`;
    markdown += `**Total: ${posts.length} posts**\n\n`;

    // Sort posts by filename
    posts.sort((a, b) => a.path.localeCompare(b.path));

    posts.forEach(post => {
      const fileName = path.basename(post.path, '.md');
      const displayTitle = post.title || fileName;
      const relativePath = post.path.replace(/\\/g, '/');

      markdown += `- [ ] [${displayTitle}](content/${relativePath})\n`;
      if (post.title && post.title !== fileName) {
        markdown += `  - File: \`${fileName}.md\`\n`;
      }
    });

    markdown += '\n';
  });

  // Add summary section
  markdown += '---\n\n';
  markdown += '## Progress Summary\n\n';
  markdown += '- [ ] All posts reviewed\n';
  markdown += '- [ ] All metadata verified\n';
  markdown += '- [ ] All images optimized\n';
  markdown += '- [ ] All links validated\n';
  markdown += '- [ ] SEO optimization complete\n';
  markdown += '- [ ] Social media metadata added\n';

  // Write to file
  fs.writeFileSync(outputFile, markdown, 'utf-8');

  console.log(`\n✅ Checklist generated: ${outputFile}`);
  console.log(`\nSummary:`);
  sortedCategories.forEach(category => {
    const count = postsByCategory[category].length;
    const categoryName = category === 'root'
      ? 'Standalone Pages'
      : category.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    console.log(`  - ${categoryName}: ${count} posts`);
  });
}

generatePostChecklist().catch(console.error);
