const fs = require('fs');
const yaml = require('js-yaml');
const path = require('path');

// Read the current posts metadata
const postsFile = path.join(process.cwd(), 'posts-metadata.yml');
const data = yaml.load(fs.readFileSync(postsFile, 'utf8'));

// Define ranking criteria and assign ranks
const rankings = {
  // TIER 1: Highest interest - Directly affects most people (1-15)
  'economics/wealth-inequality-in-america.md': 1,
  'economics/4-trillion-printed-in-2020-went-entirely-to-the-top-1-percent.md': 2,
  'economics/how-the-federal-reserve-steals-from-the-poor-and-gives-to-the-rich.md': 3,
  'democracy/voter-support-for-a-bill-has-near-zero-influence-on-whether-it-will-become-law.md': 4,
  'health/youre-14-times-more-likely-to-die-of-cancer-than-from-an-opioid-overdose.md': 5,
  'drug-war/drug-war-statistics.md': 6,
  'health/americans-spend-more-on-weight-loss-products-than-it-would-take-to-end-world-hunger.md': 7,
  'government-spending/corporate-vs-social-welfare.md': 8,
  'economics/what-percent-of-economists-support-protectionist-tariffs-2.md': 9,
  'democracy/unrepresentative-democracy-government-by-millionaires-for-millionaires.md': 10,
  'economics/when-the-government-prints-new-money-who-gets-it.md': 11,
  'economics/how-to-protect-yourself-against-the-invisible-inflation-burglar.md': 12,
  'government-spending/false-sense-of-insecurity.md': 13,
  'vote-2.md': 14,
  'politics/vote.md': 15,

  // TIER 2: High interest - Important shocking stats (16-30)
  'government-spending/how-much-does-the-us-spend-on-the-military-compared-to-the-rest-of-the-world.md': 16,
  'terrorism/how-many-americans-are-killed-by-terrorists.md': 17,
  'economics/us-stock-market-grew-3-times-faster-under-obama-than-trump.md': 18,
  'economics/ideal-level-of-government-spending.md': 19,
  'military/war/the-economic-case-for-peace-a-comprehensive-financial-analysis.md': 20,
  'government-spending/military-spending.md': 21,
  'taxes/how-much-does-the-average-american-pay-in-taxes.md': 22,
  'government-spending/how-much-does-the-government-spend-per-person.md': 23,
  'economics/long-run-trends-in-human-well-being.md': 24,
  'health/how-many-net-lives-does-the-fda-save.md': 25,
  'health/only-0-000000002-of-potential-treatments-have-been-studied.md': 26,
  'economics/gdp-and-you.md': 27,
  'economics/how-much-money-is-there-in-the-world-2.md': 28,
  'drug-war/what-is-the-cause-of-the-opioid-crisis-2.md': 29,
  'democracy/the-majority-of-americans-didnt-support-the-american-revolution.md': 30,

  // TIER 3: Medium-high interest (31-45)
  'terrorism/suicide-terrorism-statistics.md': 31,
  'federal-reserve/financial-sector-costs-us-more-than-all.md': 32,
  'financial-sector/monetary-system-steals-poor-rich.md': 33,
  'military/fraudulent-defense-contractors-paid-1-trillion.md': 34,
  'government-spending/cbs-news-2-3-trillion-missing-from-pentagon.md': 35,
  'military/we-have-enough-nuclear-bombs-to-kill-everyone-on-the-planet-2-6-times.md': 36,
  'economics/a-world-run-by-economists.md': 37,
  'politics/it-takes-3-5-of-the-population-to-change-the-world.md': 38,
  'economics/historical-examples-show-government.md': 39,
  'healthcare/depression-rates-correlate-highly-with-changes-in-diet-and-autoimmune-disease.md': 40,
  'health/fecal-transplant-reduces-autism-symptoms-45.md': 41,
  'democracy/democracys-effect-on-economic-growth.md': 42,
  'taxes/a-more-progressive-tax-system-makes-people-happier.md': 43,
  'economics/bottled-water-costs-40x-more-why-dont-poor-areas-have-water-plants.md': 44,
  'government-spending/john-stossel-bailouts-and-bull.md': 45,

  // TIER 4: Medium interest (46-60)
  'government-spending/corporate-welfare/what-would-happen-if-we-let-aig-fail.md': 46,
  'economics/keynsian-creationism-and-intelligently-design-economies.md': 47,
  'economics/how-much-does-it-cost-to-develop-a-drug.md': 48,
  'health/how-many-people-have-alzheimers-disease.md': 49,
  'health/less-rem-sleep-linked-to-dementia.md': 50,
  'healthcare/free-advanced-interpretation-of-your-23andme-raw-data.md': 51,
  'military/military-industrial-complex-funnels-wealth-to-4-counties.md': 52,
  'statistics/world-clock-statistics.md': 53,
  'terrorism/muslims-terrorists-terrorists-muslims.md': 54,
  'politics/has-political-power-become-more-decentralized-over-time.md': 55,
  '3-easy-steps.md': 56,
  'government-spending/usps-uses-18-billion-in-taxpayer-subsidies-to-subsidize-junk-mail-killing-100-million-trees-and-irritating-millions.md': 57,
  'economics/how-to-identify-the-bottom-of-a-stock-market-correction.md': 58,
  'government-spending/corporate-welfare/voters-cu-corporate-welfare-programs-as-a-good-place-to-cut-government-spending-rasmussen-reports.md': 59,
  'terrorism/iran-terrorism-history.md': 60,

  // TIER 5: Lower interest - More specific/niche (61-75)
  'government-spending/how-much-do-we-spend-on-the-military.md': 61,
  'taxes/irs-automation-could-save-americans-191-billion.md': 62,
  'healthcare/more-upward-wealth-redistribution-from.md': 63,
  'military/war/posts.md': 64,
  'government-spending/from-the-bankers-who-brought-you-president-obama-comes-new-president.md': 65,
  'government-spending/meet-the-backers-bankers-flip-flop-to-romney.md': 66,
  'government-spending/obama-to-cut-deficit-in-half-after.md': 67,
  'government-spending/spending-cuts-budget-2012-republican-primary-candidates-compared.md': 68,
  'politics/most-voters-believe-their-own-party-will-win.md': 69,
  'statistics/how-much-time-do-people-spend-consuming-news.md': 70,
  'statistics/what-was-the-population-when-the-united-states-was-established.md': 71,
  'psychology/myth-free-will.md': 72,
  'science/how-much-of-our-dna-is-useless-junk.md': 73,
  'monkey-business/drink-by-numbers.md': 74,
  'monkey-business/the-kids-today-need-to-learn-their-mathematics-ya-see.md': 75,
};

// Special entries (non-posts)
const specialRankings = {
  'empiricist-movement.md': 45,
  'i-am-biased.md': 50,
  'manage-subscriptions.md': 75,
  'politics/a-utilitarian-party-platform.md': 48,
  'productivity/prioritize-your-to-do-list-by-converting-to-present-value.md': 55,
  'psychology/subconscious-processes-27500-times-more-data-than-the-conscious-mind.md': 52,
  'solutions/the-hedonistic-imperative.md': 60,
  'solutions/wisdom-of-crowds.md': 58,
  'science/theres-about-a-0-1-chance-that-covid-19-coincidentally-arose-in-nature-near-near-the-wuhan-institute-of-virology.md': 35,
  'tools/algonomy-a-framework-for-dealing-with-suffering.md': 62,
  'tools/the-encyclopedia-of-world-problems-and-human-potential.md': 63,
  'uncategorized/fda-fails-to-apply-basic-rational-cost-benefit-analysis-to-regulatory-decisions.md': 40,
  'uncategorized/how-to-overcome-bias-in-crowdsoucing-participants.md': 65,
  'utilitarianism/direct-suffering-caused-animal-foods.md': 48,
  'utilitarianism/is-it-morally-optimal-to-rescue-a-cat-from-the-pound.md': 70,
  'federal-reserve/zombie-companies-will-lead-to-americas-lost-decade.md': 45,
  'freedom-of-speech/world-press-and-pothi-freedom-index.md': 57,
};

// Merge rankings
const allRankings = { ...rankings, ...specialRankings };

// Add ranks to posts
data.posts.forEach(post => {
  const rank = allRankings[post.file];
  post.rank = rank || 50; // Default to middle if not explicitly ranked
});

// Sort by rank
data.posts.sort((a, b) => a.rank - b.rank);

// Write updated file
const yamlOutput = yaml.dump(data, { lineWidth: -1, noRefs: true });
fs.writeFileSync(postsFile, yamlOutput, 'utf8');

console.log(`✓ Ranked ${data.posts.length} posts`);
console.log(`✓ Updated ${postsFile}`);
