const fs = require('fs');
const path = require('path');
const https = require('https');
const http = require('http');

function findHtmlFiles(dir, fileList = []) {
  const files = fs.readdirSync(dir);
  files.forEach(file => {
    const filePath = path.join(dir, file);
    const stat = fs.statSync(filePath);
    if (stat.isDirectory() && !filePath.includes('_pagefind')) {
      findHtmlFiles(filePath, fileList);
    } else if (file.endsWith('.html')) {
      fileList.push(filePath);
    }
  });
  return fileList;
}

async function checkExternalUrl(url) {
  return new Promise((resolve) => {
    const client = url.startsWith('https:') ? https : http;
    const request = client.request(url, { method: 'HEAD', timeout: 5000 }, (res) => {
      resolve({ url, status: res.statusCode, broken: res.statusCode >= 400 });
    });
    request.on('error', () => {
      resolve({ url, status: 'ERROR', broken: true });
    });
    request.on('timeout', () => {
      request.destroy();
      resolve({ url, status: 'TIMEOUT', broken: true });
    });
    request.end();
  });
}

async function validateSite() {
  console.log('🔍 Validating site...\n');

  const issues = {
    htmlEntities: [],
    brokenImages: [],
    externalImages: [],
    unrenderedMarkdown: [],
    brokenLinks: [],
    brokenExternalLinks: []
  };

  const externalLinksToCheck = new Set();

  // Find all HTML files
  const htmlFiles = findHtmlFiles('_site');

  console.log(`📄 Checking ${htmlFiles.length} HTML files...\n`);

  for (const file of htmlFiles) {
    const content = fs.readFileSync(file, 'utf-8');

    // Check for unrendered HTML entities in excerpts
    const excerptMatches = content.match(/<div class="entry-summary">[\s\S]*?<\/div>/g);
    if (excerptMatches) {
      excerptMatches.forEach((excerpt, idx) => {
        if (excerpt.includes('&lt;') || excerpt.includes('&gt;')) {
          issues.htmlEntities.push({
            file: file.replace('_site/', ''),
            excerpt: excerpt.substring(0, 200) + '...'
          });
        }
      });
    }

    // Check for unrendered markdown links in content
    const markdownLinkPattern = /\[([^\]]+)\]\(([^)]+)\)/g;
    const bodyMatch = content.match(/<body[\s\S]*<\/body>/);
    if (bodyMatch) {
      const bodyContent = bodyMatch[0];
      let match;
      while ((match = markdownLinkPattern.exec(bodyContent)) !== null) {
        // Get context around the match
        const start = Math.max(0, match.index - 50);
        const end = Math.min(bodyContent.length, match.index + match[0].length + 50);
        const context = bodyContent.substring(start, end);
        issues.unrenderedMarkdown.push({
          file: file.replace('_site/', ''),
          link: match[0],
          context: context.replace(/\n/g, ' ').replace(/\s+/g, ' ')
        });
      }
    }

    // Check for broken image references
    const imgMatches = content.match(/<img[^>]+src="([^"]+)"[^>]*>/g);
    if (imgMatches) {
      imgMatches.forEach(imgTag => {
        const srcMatch = imgTag.match(/src="([^"]+)"/);
        if (srcMatch) {
          const src = srcMatch[1];
          // Check if local image exists
          if (src.startsWith('/') && !src.startsWith('//')) {
            // Try both the original path and URL-decoded path
            const imgPath = path.join('_site', src);
            const decodedPath = path.join('_site', decodeURIComponent(src));

            if (!fs.existsSync(imgPath) && !fs.existsSync(decodedPath)) {
              issues.brokenImages.push({
                file: file.replace('_site/', ''),
                src: src
              });
            }
          } else if (src.startsWith('http://') || src.startsWith('https://')) {
            // Track external images for reporting
            issues.externalImages.push({
              file: file.replace('_site/', ''),
              src: src
            });
          }
        }
      });
    }

    // Check for broken internal and external links
    const linkMatches = content.match(/<a[^>]+href="([^"]+)"[^>]*>/g);
    if (linkMatches) {
      linkMatches.forEach(linkTag => {
        const hrefMatch = linkTag.match(/href="([^"]+)"/);
        if (hrefMatch) {
          const href = hrefMatch[1];

          // Check external links
          if (href.startsWith('http://') || href.startsWith('https://')) {
            externalLinksToCheck.add(href);
          }
          // Check if internal link (starts with /) but not anchor-only or external
          else if (href.startsWith('/') && !href.startsWith('//') && !href.includes('#')) {
            // Remove query params and trailing slash for checking
            const cleanHref = href.split('?')[0].replace(/\/$/, '');

            // Skip non-HTML resources (RSS feeds, media files, etc.)
            if (cleanHref.match(/\.(xml|json|txt|pdf|mp3|mp4|zip|jpg|jpeg|png|gif|svg|webp|css|js)$/i)) {
              return;
            }

            const linkPath = path.join('_site', cleanHref, 'index.html');
            const linkPathAlt = path.join('_site', cleanHref + '.html');

            if (!fs.existsSync(linkPath) && !fs.existsSync(linkPathAlt)) {
              issues.brokenLinks.push({
                file: file.replace('_site/', ''),
                href: href
              });
            }
          }
        }
      });
    }
  }

  // Check external links
  if (externalLinksToCheck.size > 0) {
    console.log(`🌐 Checking ${externalLinksToCheck.size} external links...\n`);
    const externalResults = await Promise.all(
      Array.from(externalLinksToCheck).map(url => checkExternalUrl(url))
    );
    externalResults.forEach(result => {
      if (result.broken) {
        issues.brokenExternalLinks.push(result);
      }
    });
  }

  // Report issues
  console.log('📊 VALIDATION RESULTS:\n');

  if (issues.htmlEntities.length > 0) {
    console.log(`❌ Found ${issues.htmlEntities.length} excerpts with unrendered HTML entities:`);
    issues.htmlEntities.slice(0, 5).forEach(issue => {
      console.log(`   - ${issue.file}`);
      console.log(`     ${issue.excerpt.substring(0, 100)}...`);
    });
    if (issues.htmlEntities.length > 5) {
      console.log(`   ... and ${issues.htmlEntities.length - 5} more`);
    }
    console.log('');
  } else {
    console.log('✅ No HTML entity issues found in excerpts\n');
  }

  if (issues.brokenImages.length > 0) {
    console.log(`❌ Found ${issues.brokenImages.length} broken image references:`);
    issues.brokenImages.slice(0, 10).forEach(issue => {
      console.log(`   - ${issue.file}: ${issue.src}`);
    });
    if (issues.brokenImages.length > 10) {
      console.log(`   ... and ${issues.brokenImages.length - 10} more`);
    }
    console.log('');
  } else {
    console.log('✅ No broken images found\n');
  }

  if (issues.unrenderedMarkdown.length > 0) {
    console.log(`❌ Found ${issues.unrenderedMarkdown.length} unrendered markdown links:`);
    issues.unrenderedMarkdown.slice(0, 10).forEach(issue => {
      console.log(`   - ${issue.file}`);
      console.log(`     Link: ${issue.link}`);
      console.log(`     Context: ${issue.context.substring(0, 100)}...`);
    });
    if (issues.unrenderedMarkdown.length > 10) {
      console.log(`   ... and ${issues.unrenderedMarkdown.length - 10} more`);
    }
    console.log('');
  } else {
    console.log('✅ No unrendered markdown links found\n');
  }

  if (issues.externalImages.length > 0) {
    console.log(`ℹ️  Found ${issues.externalImages.length} external image references (may or may not be broken):`);
    // Group by unique URL to avoid duplication
    const uniqueExternal = [...new Set(issues.externalImages.map(i => i.src))];
    uniqueExternal.slice(0, 10).forEach(src => {
      console.log(`   - ${src}`);
    });
    if (uniqueExternal.length > 10) {
      console.log(`   ... and ${uniqueExternal.length - 10} more`);
    }
    console.log('');
  }

  if (issues.brokenLinks.length > 0) {
    console.log(`❌ Found ${issues.brokenLinks.length} broken internal links:`);
    issues.brokenLinks.slice(0, 10).forEach(issue => {
      console.log(`   - ${issue.file}: ${issue.href}`);
    });
    if (issues.brokenLinks.length > 10) {
      console.log(`   ... and ${issues.brokenLinks.length - 10} more`);
    }
    console.log('');
  } else {
    console.log('✅ No broken internal links found\n');
  }

  if (issues.brokenExternalLinks.length > 0) {
    console.log(`❌ Found ${issues.brokenExternalLinks.length} broken external links:`);
    issues.brokenExternalLinks.slice(0, 10).forEach(issue => {
      console.log(`   - ${issue.url} (Status: ${issue.status})`);
    });
    if (issues.brokenExternalLinks.length > 10) {
      console.log(`   ... and ${issues.brokenExternalLinks.length - 10} more`);
    }
    console.log('');
  } else {
    console.log('✅ No broken external links found\n');
  }

  // Summary
  const totalIssues = issues.htmlEntities.length + issues.brokenImages.length + issues.unrenderedMarkdown.length + issues.brokenLinks.length + issues.brokenExternalLinks.length;
  if (totalIssues === 0) {
    console.log('🎉 No issues found! Site looks good.\n');
  } else {
    console.log(`⚠️  Total issues found: ${totalIssues}\n`);
  }

  return issues;
}

validateSite().catch(console.error);
