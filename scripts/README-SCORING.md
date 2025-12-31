# AI Post Scoring System

This system uses Gemini Flash to automatically score blog posts on quality and educational value.

## Setup

1. Ensure you have `GOOGLE_GENERATIVE_AI_API_KEY` in your `.env` file
2. Install dependencies: `npm install`

## Running the Scorer

```bash
npm run score-posts
```

This will:
- Scan all markdown files in the `content/` directory
- Send each post to Gemini Flash for scoring
- Add AI scores to the frontmatter of each post

## Scoring Criteria

Each post receives two scores from 1-10:

### Quality Score (1-10)
- Writing quality and clarity
- Factual accuracy
- Data presentation
- Overall professionalism

### Value Score (1-10)
- Educational importance
- Impact on readers' understanding
- Relevance of information
- Practical applicability

## Output Format

Scores are added to frontmatter as:

```yaml
---
title: "Post Title"
description: "Post description"
aiScores:
  quality: 8
  value: 9
  reasoning: "Well-researched with shocking statistics that challenge common beliefs"
  scoredAt: "2025-12-31T05:00:00.000Z"
  model: "gemini-3-flash-preview"
---
```

## Cost Estimate

- Average cost: ~$0.0001 per post
- 75 posts ≈ $0.0075 total
- Uses Gemini Flash (most cost-effective model)

## Notes

- The script processes posts sequentially with a 1-second delay to avoid rate limiting
- Scores are saved directly to the markdown files
- Re-running will update existing scores
- Token usage and cost estimates are logged for each request
