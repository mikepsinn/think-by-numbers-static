import * as fs from 'fs/promises';
import * as path from 'path';
import matter from 'gray-matter';
import { generateGeminiFlashContent, extractJsonFromResponse } from './lib/llm.js';

interface PostScore {
  qualityScore: number;      // 1-10: Overall quality (writing, clarity, accuracy)
  valueScore: number;         // 1-10: Educational value and importance
  reasoning: string;          // Brief explanation of the scores
}

async function getAllMarkdownFiles(dir: string): Promise<string[]> {
  const files: string[] = [];
  const entries = await fs.readdir(dir, { withFileTypes: true });

  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...await getAllMarkdownFiles(fullPath));
    } else if (entry.isFile() && entry.name.endsWith('.md')) {
      files.push(fullPath);
    }
  }

  return files;
}

async function scorePost(filePath: string): Promise<PostScore> {
  const content = await fs.readFile(filePath, 'utf-8');
  const { data, content: bodyContent } = matter(content);

  const title = data.title || 'Untitled';
  const description = data.description || '';

  // Limit content to first 2000 chars to keep token count reasonable
  const truncatedContent = bodyContent.substring(0, 2000);

  const prompt = `Mission: Maximize median health/wealth/happiness by exposing harmful policies.

Score 1-10:
- Clarity: Clear mechanism? Strong data? Actionable?
- Impact: Novel/counter-intuitive insights? Hidden facts few know? Exposes wealth extraction/misallocation?

Timeliness: Elections/politicians >5yr old max 4/10. Dated stats max 5/10. Timeless mechanisms full potential.

Title: ${title}
Description: ${description}
Content: ${truncatedContent}

JSON only:
{
  "qualityScore": <1-10>,
  "valueScore": <1-10>,
  "reasoning": "<why this matters NOW>"
}`;

  try {
    const response = await generateGeminiFlashContent(prompt);
    const result = extractJsonFromResponse(response, `scoring ${filePath}`) as PostScore;

    // Validate scores are in range
    if (result.qualityScore < 1 || result.qualityScore > 10 ||
        result.valueScore < 1 || result.valueScore > 10) {
      throw new Error(`Invalid scores received: quality=${result.qualityScore}, value=${result.valueScore}`);
    }

    return result;
  } catch (error) {
    console.error(`Error scoring ${filePath}:`, error);
    // Return default scores on error
    return {
      qualityScore: 5,
      valueScore: 5,
      reasoning: 'Error occurred during scoring, using default values'
    };
  }
}

async function updatePostFrontmatter(filePath: string, scores: PostScore): Promise<void> {
  const content = await fs.readFile(filePath, 'utf-8');
  const { data, content: bodyContent } = matter(content);

  // Count images (markdown ![...](...) and HTML <img>)
  const markdownImages = (bodyContent.match(/!\[.*?\]\(.*?\)/g) || []).length;
  const htmlImages = (bodyContent.match(/<img[^>]*>/g) || []).length;
  const imageCount = markdownImages + htmlImages;

  // Update frontmatter with scores and metadata
  data.aiScores = {
    quality: scores.qualityScore,
    value: scores.valueScore,
    reasoning: scores.reasoning,
    scoredAt: new Date().toISOString(),
    model: 'gemini-3-flash-preview',
    length: bodyContent.length,
    imageCount: imageCount
  };

  // Rebuild the file with updated frontmatter
  const updatedContent = matter.stringify(bodyContent, data);
  await fs.writeFile(filePath, updatedContent, 'utf-8');
}

async function main() {
  const contentDir = path.join(process.cwd(), 'content');

  console.log('🔍 Finding all markdown posts...');
  const files = await getAllMarkdownFiles(contentDir);

  // Filter to only actual posts (exclude some meta files)
  const posts = files.filter(f => {
    const basename = path.basename(f);
    return !['README.md', 'LICENSE.md', 'CONTRIBUTING.md'].includes(basename);
  });

  console.log(`📊 Found ${posts.length} posts to score\n`);

  let processedCount = 0;

  for (const postPath of posts) {
    const relativePath = path.relative(contentDir, postPath);
    console.log(`\n[${processedCount + 1}/${posts.length}] Scoring: ${relativePath}`);

    try {
      const scores = await scorePost(postPath);
      await updatePostFrontmatter(postPath, scores);

      console.log(`  ✅ Quality: ${scores.qualityScore}/10 | Value: ${scores.valueScore}/10`);
      console.log(`  📝 ${scores.reasoning}`);

      processedCount++;

      // Small delay to avoid rate limiting
      await new Promise(resolve => setTimeout(resolve, 1000));

    } catch (error) {
      console.error(`  ❌ Error processing ${relativePath}:`, error);
    }
  }

  console.log(`\n✅ Completed scoring ${processedCount}/${posts.length} posts`);
}

main().catch(console.error);
