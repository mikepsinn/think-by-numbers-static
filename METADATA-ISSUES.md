# Metadata Validation Issues

**Generated:** 2025-12-31T04:54:05.884Z

**Total Files with Issues:** 2

---

## 1. ./content/drug-war/drug-war-statistics.md

- [ ] Infographic file not found: /assets/infographics/drug-war/drug-war-statistics.png

## 2. ./content/government-spending/from-the-bankers-who-brought-you-president-obama-comes-new-president.md

- [ ] Infographic file not found: /assets/infographics/government-spending/from-the-bankers-who-brought-you-president-obama-comes-new-president.png

---

## How to Fix

1. Add missing metadata to the frontmatter of each file
2. For missing descriptions: Add a `description:` field to frontmatter (explicit description required, auto-excerpt not allowed)
3. For missing featured images: Add `metadata.media.featuredImage:` path
4. For missing tags: Add a `tags:` array
5. For missing categories: Add `metadata.categories:` array
6. Run `npm run build` to re-validate

