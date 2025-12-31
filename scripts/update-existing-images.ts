/**
 * Update existing generated images with watermark and metadata
 */

import dotenv from 'dotenv';
import path from 'path';
import fs from 'fs/promises';
import matter from 'gray-matter';
import { addWatermark, ImageMetadata } from './lib/genai-image.js';
import { glob } from 'glob';

// Load environment variables
dotenv.config();

/**
 * Get all existing generated images
 */
async function getAllGeneratedImages(): Promise<string[]> {
  const images = await glob('content/assets/**/*.{png,jpg,jpeg}', {
    absolute: true,
  });
  return images;
}

/**
 * Find the markdown file corresponding to an image
 */
async function findCorrespondingMarkdown(imagePath: string): Promise<string | null> {
  // Extract the relative path structure
  // e.g., content/assets/og-images/economics/tariffs.png -> economics/tariffs
  const relativePath = path.relative('content/assets', imagePath);
  const parts = relativePath.split(path.sep);

  // Remove first directory (og-images or infographics)
  if (parts.length < 2) return null;
  parts.shift(); // Remove og-images/infographics

  // Remove file extension
  const basename = path.basename(parts[parts.length - 1], path.extname(parts[parts.length - 1]));
  parts[parts.length - 1] = basename;

  // Construct markdown path
  const mdPath = path.join('content', ...parts) + '.md';

  try {
    await fs.access(mdPath);
    return mdPath;
  } catch {
    return null;
  }
}

/**
 * Update a single image with watermark and metadata
 */
async function updateImage(imagePath: string): Promise<void> {
  console.log(`\n[*] Processing: ${imagePath}`);

  // Find corresponding markdown file
  const mdPath = await findCorrespondingMarkdown(imagePath);

  if (!mdPath) {
    console.log(`  [SKIP] No corresponding markdown file found`);
    return;
  }

  console.log(`  Found markdown: ${mdPath}`);

  // Read markdown frontmatter
  const mdContent = await fs.readFile(mdPath, 'utf-8');
  const { data: frontmatter } = matter(mdContent);

  // Prepare metadata
  const metadata: ImageMetadata = {
    title: frontmatter.title || 'ThinkByNumbers Article',
    description: frontmatter.description || frontmatter.title || '',
    author: 'ThinkByNumbers',
    copyright: `© ${new Date().getFullYear()} ThinkByNumbers.org`,
    keywords: frontmatter.tags || [],
  };

  console.log(`  Title: ${metadata.title}`);

  // Read existing image
  const imageBuffer = await fs.readFile(imagePath);

  // Apply watermark and metadata
  const updatedBuffer = await addWatermark(imageBuffer, metadata);

  // Save updated image
  await fs.writeFile(imagePath, updatedBuffer);

  console.log(`  [OK] Updated with watermark and metadata`);
}

/**
 * Main function
 */
async function main() {
  console.log('🖼️  Updating Existing Generated Images');
  console.log('='.repeat(60));

  // Get all generated images
  console.log('\n[*] Finding generated images...');
  const images = await getAllGeneratedImages();
  console.log(`[OK] Found ${images.length} images\n`);

  if (images.length === 0) {
    console.log('No images to update.');
    return;
  }

  // Process each image
  let updated = 0;

  for (const imagePath of images) {
    await updateImage(imagePath);
    updated++;
  }

  // Summary
  console.log('\n' + '='.repeat(60));
  console.log('Summary:');
  console.log(`  Total images: ${images.length}`);
  console.log(`  Updated: ${updated}`);
  console.log('='.repeat(60) + '\n');
}

// Run the script
main().catch(error => {
  console.error('Fatal error:', error);
  process.exit(1);
});
