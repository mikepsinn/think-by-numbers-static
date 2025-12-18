# Think by Numbers - Static Site

A better world through data.

Static site built with [Eleventy](https://www.11ty.dev/) from WordPress export.

## Features

- ✅ 93 blog posts with full content
- ✅ Featured images for all posts
- ✅ Self-hosted podcast with 13 episodes (130MB MP3s)
- ✅ RSS feed for blog
- ✅ Podcast RSS feed
- ✅ Client-side search (Pagefind)
- ✅ LaTeX math rendering (KaTeX)
- ✅ Responsive layouts

## Quick Start

```bash
# Install dependencies
npm install

# Start development server
npm start
# Server will be at http://localhost:8080

# Build for production
npm run build
```

## Scripts

- `npm start` - Clean, build, and start dev server with live reload
- `npm run serve` - Start dev server (shows URL in console)
- `npm run build` - Build site + generate search index
- `npm run search` - Generate Pagefind search index
- `npm run clean` - Remove _site directory

## Project Structure

```
.
├── 11ty/                      # Source files
│   ├── _data/                 # Global data (site.json)
│   ├── _includes/             # Layouts & templates
│   │   └── base.njk          # Base layout
│   ├── assets/               # Global assets
│   │   └── podcasts/         # Self-hosted podcast MP3s
│   ├── [categories]/         # Blog posts by category
│   │   ├── *.md             # Markdown posts
│   │   └── assets/          # Post-specific assets
│   ├── feed.njk             # RSS feed template
│   ├── podcast.njk          # Podcast RSS feed
│   ├── index.njk            # Homepage
│   └── search.njk           # Search page
├── _site/                    # Built site (gitignored)
├── .eleventy.js              # Eleventy config
└── package.json              # Dependencies & scripts
```

## Feeds

- **Blog RSS**: `/feed.xml`
- **Podcast RSS**: `/podcast.xml`

## Search

Search is powered by [Pagefind](https://pagefind.app/), a static search library that indexes your site at build time.

## LaTeX Math Rendering

Mathematical expressions are rendered using [KaTeX](https://katex.org/). Use these delimiters:
- Inline math: `$equation$` or `\(equation\)`
- Display math: `$$equation$$` or `\[equation\]`

Example:
```markdown
The formula $$E = mc^2$$ shows mass-energy equivalence.
```

## Featured Images

Featured images are automatically displayed at the top of each post. The importer downloads them from WordPress and stores them in `assets/` subdirectories.

## Deployment

The site is static HTML and can be deployed to:
- GitHub Pages
- Netlify
- Vercel
- Cloudflare Pages
- Any static host

Just run `npm run build` and deploy the `_site` directory.

## WordPress Import

This site was imported from WordPress using [@11ty/import](https://github.com/11ty/eleventy-import) with Windows compatibility fixes and podcast MP3 download support.
