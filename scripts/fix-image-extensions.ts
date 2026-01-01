/**
 * Fix Image Extensions
 *
 * Identifies images with incorrect file extensions (e.g., .png files that are actually JPEG),
 * renames them to the correct extension, and updates all references throughout the project.
 */

import fs from 'fs/promises';
import path from 'path';
import { glob } from 'glob';

interface ImageInfo {
  path: string;
  actualFormat: 'jpeg' | 'png' | 'gif' | 'webp' | 'unknown';
  extension: string;
  needsRename: boolean;
  newPath?: string;
}

interface RenameOperation {
  oldPath: string;
  newPath: string;
  references: string[];
}

/**
 * Detect actual image format from file magic numbers
 */
async function detectImageFormat(filePath: string): Promise<'jpeg' | 'png' | 'gif' | 'webp' | 'unknown'> {
  try {
    const buffer = await fs.readFile(filePath);

    // Check magic numbers (first few bytes of file)
    // PNG: 89 50 4E 47
    if (buffer[0] === 0x89 && buffer[1] === 0x50 && buffer[2] === 0x4E && buffer[3] === 0x47) {
      return 'png';
    }

    // JPEG: FF D8 FF
    if (buffer[0] === 0xFF && buffer[1] === 0xD8 && buffer[2] === 0xFF) {
      return 'jpeg';
    }

    // GIF: 47 49 46 38
    if (buffer[0] === 0x47 && buffer[1] === 0x49 && buffer[2] === 0x46 && buffer[3] === 0x38) {
      return 'gif';
    }

    // WebP: RIFF....WEBP
    if (buffer[0] === 0x52 && buffer[1] === 0x49 && buffer[2] === 0x46 && buffer[3] === 0x46 &&
        buffer[8] === 0x57 && buffer[9] === 0x45 && buffer[10] === 0x42 && buffer[11] === 0x50) {
      return 'webp';
    }

    return 'unknown';
  } catch (error) {
    console.error(`Error reading ${filePath}:`, error);
    return 'unknown';
  }
}

/**
 * Get correct extension for a format
 */
function getExtensionForFormat(format: string): string {
  switch (format) {
    case 'jpeg': return 'jpg';
    case 'png': return 'png';
    case 'gif': return 'gif';
    case 'webp': return 'webp';
    default: return '';
  }
}

/**
 * Scan all images and identify those with incorrect extensions
 */
async function scanImages(): Promise<ImageInfo[]> {
  console.log('\n📁 Scanning images...');

  // Find all image files in content/assets
  const imagePatterns = [
    'content/assets/**/*.png',
    'content/assets/**/*.jpg',
    'content/assets/**/*.jpeg',
    'content/assets/**/*.gif',
    'content/assets/**/*.webp',
  ];

  const allImages: string[] = [];
  for (const pattern of imagePatterns) {
    const files = await glob(pattern, {
      ignore: ['**/node_modules/**', '**/_site/**', '**/dist/**'],
      absolute: true,
    });
    allImages.push(...files);
  }

  console.log(`Found ${allImages.length} images to check`);

  const imageInfos: ImageInfo[] = [];

  for (const imagePath of allImages) {
    const actualFormat = await detectImageFormat(imagePath);
    const currentExt = path.extname(imagePath).toLowerCase().substring(1); // Remove the dot
    const correctExt = getExtensionForFormat(actualFormat);

    // Normalize jpeg/jpg
    const normalizedCurrentExt = currentExt === 'jpeg' ? 'jpg' : currentExt;
    const normalizedCorrectExt = correctExt === 'jpeg' ? 'jpg' : correctExt;

    const needsRename = normalizedCorrectExt && normalizedCurrentExt !== normalizedCorrectExt;

    const info: ImageInfo = {
      path: imagePath,
      actualFormat,
      extension: currentExt,
      needsRename,
    };

    if (needsRename) {
      const dir = path.dirname(imagePath);
      const baseName = path.basename(imagePath, path.extname(imagePath));
      info.newPath = path.join(dir, `${baseName}.${correctExt}`);
    }

    imageInfos.push(info);
  }

  return imageInfos;
}

/**
 * Find all files that might contain references to images
 */
async function findReferenceFiles(): Promise<string[]> {
  console.log('\n📄 Finding files that might reference images...');

  const patterns = [
    'content/**/*.md',
    '11ty/**/*.njk',
    '11ty/**/*.js',
    'scripts/**/*.ts',
    'scripts/**/*.js',
    '**/*.json',
  ];

  const allFiles: string[] = [];
  for (const pattern of patterns) {
    const files = await glob(pattern, {
      ignore: ['**/node_modules/**', '**/_site/**', '**/dist/**', '**/package-lock.json'],
      absolute: true,
    });
    allFiles.push(...files);
  }

  console.log(`Found ${allFiles.length} files to check for references`);
  return allFiles;
}

/**
 * Find all references to an image path in a file
 */
async function findReferencesInFile(filePath: string, oldImagePath: string): Promise<boolean> {
  try {
    const content = await fs.readFile(filePath, 'utf-8');

    // Convert to forward slashes and get various possible reference formats
    const oldPathNormalized = oldImagePath.replace(/\\/g, '/');
    const oldPathFromContent = oldPathNormalized.replace(/^.*content\//, '/');
    const oldPathFromAssets = oldPathNormalized.replace(/^.*assets\//, '/assets/');
    const oldBasename = path.basename(oldImagePath);

    // Check if any reference format appears in the file
    return content.includes(oldPathNormalized) ||
           content.includes(oldPathFromContent) ||
           content.includes(oldPathFromAssets) ||
           content.includes(oldBasename);
  } catch (error) {
    return false;
  }
}

/**
 * Update references in a file
 */
async function updateReferencesInFile(
  filePath: string,
  oldImagePath: string,
  newImagePath: string
): Promise<boolean> {
  try {
    let content = await fs.readFile(filePath, 'utf-8');
    let modified = false;

    // Normalize paths
    const oldPathNormalized = oldImagePath.replace(/\\/g, '/');
    const newPathNormalized = newImagePath.replace(/\\/g, '/');

    // Get various reference formats
    const oldPathFromContent = oldPathNormalized.replace(/^.*content\//, '/');
    const newPathFromContent = newPathNormalized.replace(/^.*content\//, '/');

    const oldPathFromAssets = oldPathNormalized.replace(/^.*assets\//, '/assets/');
    const newPathFromAssets = newPathNormalized.replace(/^.*assets\//, '/assets/');

    const oldBasename = path.basename(oldImagePath);
    const newBasename = path.basename(newImagePath);

    // Replace all variations
    const replacements = [
      [oldPathNormalized, newPathNormalized],
      [oldPathFromContent, newPathFromContent],
      [oldPathFromAssets, newPathFromAssets],
      [oldBasename, newBasename],
    ];

    for (const [oldRef, newRef] of replacements) {
      if (content.includes(oldRef)) {
        content = content.split(oldRef).join(newRef);
        modified = true;
      }
    }

    if (modified) {
      await fs.writeFile(filePath, content, 'utf-8');
      return true;
    }

    return false;
  } catch (error) {
    console.error(`Error updating ${filePath}:`, error);
    return false;
  }
}

/**
 * Main function
 */
async function main() {
  console.log('🔧 Fix Image Extensions');
  console.log('='.repeat(60));

  // Scan images
  const imageInfos = await scanImages();
  const imagesToRename = imageInfos.filter(img => img.needsRename);

  if (imagesToRename.length === 0) {
    console.log('\n✅ All images have correct extensions!');
    return;
  }

  console.log(`\n⚠️  Found ${imagesToRename.length} images with incorrect extensions:`);
  for (const img of imagesToRename) {
    const relPath = path.relative(process.cwd(), img.path);
    const relNewPath = img.newPath ? path.relative(process.cwd(), img.newPath) : '';
    console.log(`  ${relPath}`);
    console.log(`    Actual format: ${img.actualFormat.toUpperCase()}`);
    console.log(`    Will rename to: ${relNewPath}`);
  }

  // Find files that might have references
  const referenceFiles = await findReferenceFiles();

  // Build rename operations
  console.log('\n🔍 Finding references...');
  const operations: RenameOperation[] = [];

  for (const img of imagesToRename) {
    if (!img.newPath) continue;

    const op: RenameOperation = {
      oldPath: img.path,
      newPath: img.newPath,
      references: [],
    };

    // Find all files that reference this image
    for (const refFile of referenceFiles) {
      const hasRef = await findReferencesInFile(refFile, img.path);
      if (hasRef) {
        op.references.push(refFile);
      }
    }

    operations.push(op);
  }

  // Show what will be updated
  console.log('\n📝 Summary of changes:');
  for (const op of operations) {
    const relOld = path.relative(process.cwd(), op.oldPath);
    const relNew = path.relative(process.cwd(), op.newPath);
    console.log(`\n  ${relOld} → ${relNew}`);
    if (op.references.length > 0) {
      console.log(`    References found in ${op.references.length} files:`);
      for (const ref of op.references) {
        const relRef = path.relative(process.cwd(), ref);
        console.log(`      - ${relRef}`);
      }
    } else {
      console.log(`    No references found`);
    }
  }

  // Confirm before proceeding
  console.log('\n⚠️  This will rename files and update references.');
  console.log('Press Ctrl+C to cancel, or any key to continue...');

  // Wait for user input (in non-interactive mode, just proceed)
  if (process.stdin.isTTY) {
    await new Promise((resolve) => {
      process.stdin.once('data', resolve);
      process.stdin.setRawMode(true);
      process.stdin.resume();
    });
    process.stdin.setRawMode(false);
    process.stdin.pause();
  }

  // Perform operations
  console.log('\n🚀 Applying changes...');

  for (const op of operations) {
    const relOld = path.relative(process.cwd(), op.oldPath);
    const relNew = path.relative(process.cwd(), op.newPath);

    try {
      // Update references first
      let updatedCount = 0;
      for (const refFile of op.references) {
        const updated = await updateReferencesInFile(refFile, op.oldPath, op.newPath);
        if (updated) {
          updatedCount++;
        }
      }

      if (updatedCount > 0) {
        console.log(`  ✓ Updated ${updatedCount} references for ${relOld}`);
      }

      // Then rename the file
      await fs.rename(op.oldPath, op.newPath);
      console.log(`  ✓ Renamed: ${relOld} → ${relNew}`);
    } catch (error) {
      console.error(`  ✗ Failed to process ${relOld}:`, error);
    }
  }

  console.log('\n✅ Done!');
  console.log('='.repeat(60));
}

// Run
main().catch(error => {
  console.error('Fatal error:', error);
  process.exit(1);
});
