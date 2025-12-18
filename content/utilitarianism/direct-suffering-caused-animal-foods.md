---
title: How Much Direct Suffering Is Caused by Various Animal Foods?
authors: []
date: 2013-09-13T05:56:05.000Z
metadata:
  featuredImage: >-
    https://thinkbynumbers.org/wp-content/uploads/2011/10/Suffering-Caused-by-Various-Animal-Foods.jpg
  media:
    featuredImage: /wp-content/uploads/2011/10/Suffering-Caused-by-Various-Animal-Foods.jpg
  categories:
    - Utilitarianism
  uuid: 11ty/import::wordpress::http://thinkbynumbers.org/blog/?p=1067
  type: wordpress
  url: >-
    https://thinkbynumbers.org/utilitarianism/direct-suffering-caused-animal-foods/
tags:
  - utilitarianism
---
This is a rigorous analysis from the brilliant utilitarian mind of [Brian Tomasik](http://briantomasik.com/) originally published at [http://www.utilitarian-essays.com](http://reducing-suffering.org/) in 2005. I made this graph in 2007 to try to illustrate his point visually.

[![Graph of Suffering Caused by Various Animal Foods](/wp-content/uploads/2011/10/Suffering-Caused-by-Various-Animal-Foods.jpg)](http://thinkbynumbers.org/wp-content/uploads/2011/10/Suffering-Caused-by-Various-Animal-Foods.jpg)

> _Abstract._ Consuming equal weights of different animal products may produce vastly different expected amounts of direct suffering. Farmed seafood may cause the most, followed by poultry products. Pork, beef, and especially milk produce considerably less suffering in comparison. As an extreme case, creating demand for a kg of farmed catfish meat is expected to cause 20,000 times as much suffering as creating demand for a kg of milk. (Note: I haven’t yet analyzed lobster, shrimp, [prawn](http://www.dailymail.co.uk/sciencetech/article-492323/Prawns-feel-pain-say-scientists.html), or other small seafood, but my guess is that, given the small size of these organisms, eating them causes large amounts of expected suffering per kilogram — at least as long as we give them a nontrivial probability of sentience, as [I think we should](https://en.wikipedia.org/wiki/Pain_in_crustaceans).)

**Introduction**

It is clear that animals living on factory farms endure great amounts of suffering, enough that it is wrong to bring them into existence by creating economic demand for meat. If readers are unconvinced on this point, I recommend the vast collection of literature available online, including [FactoryFarming.com](http://www.farmsanctuary.org/learn/factory-farming/), [Why Vegan](http://veganoutreach.org/modernfarms/), and [these videos](http://reducing-suffering.org/on-the-seriousness-of-suffering/#factory-farming).

In considering the suffering of farm animals, we presumably ought to care equally about equal amounts of suffering, regardless of which animals experience that suffering. It does not follow, however, that we ought to avoid all animal-based food products with equal amounts of effort. Eating certain types of meat may cause more suffering than eating the same amount of another type of meat under otherwise identical circumstances.

Below, I’ll investigate how much direct suffering, on average, is caused by creating demand for a kilogram of different types of animal products. By “direct” suffering, I refer only to the suffering of the animal whose flesh, milk, or eggs is part of the animal product. I’m ignoring, therefore, the suffering of male chicks in being ground up as part of the egg-production process, the suffering of calves separated from their mothers in milk production, the contribution of milk production to the veal/beef industry, the large amounts of wild fish that are caught and fed to farmed fish, the (sometimes high) fraction of farm animals that die before reaching slaughter, the environmental destruction that meat production entails, and potential health consequences of eating certain types of meat. Readers should consider these factors, too–perhaps incorporating them into the computations below.

**Results**

Here is a table of data on various animal foods. Column 4 computes a straightforward value for the expected number of days of life on a factory farm that purchasing a kg of an animal food causes animals of that type to endure. Column 5 represents my best-guess estimates for how bad life is per day for each of those animals. For instance, since I think the suffering of hens in battery cages is perhaps 2.5 times as intense, on average, as the suffering of beef cows, I put a “1” in the beef-cow entry and “2.5” in the egg column. Column 6 is a subjective estimate of the average pain of slaughter for each animal, expressed in terms of an equivalent number of days of regular life for that animal. For instance, I used “5” as an estimate for broiler chickens, which means I assume chickens would, on average, be indifferent between enduring the stress of slaughter and living for five more days in their usual conditions. Finally, I computed “Suffering per kg” in column 7 as follows:

column 7 = ((column 4)\*(column 5) + (column 5)\*(column 6))/(column 3),

where the first summand on the right represents “suffering per kg due to life” and the second summand represents “suffering per kg due to slaughter.”

<table border="1" width="914"><tbody><tr><td>Column 1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td></tr><tr><td width="84"><em>Animal or Food Product</em></td><td width="99"><em>Average lifespan (days)</em></td><td width="177"><em>Average amount of food produced per lifetime (kg)</em></td><td width="162"><em>Expected days of life caused per kg of meat demanded</em></td><td width="142"><em>Suffering per day of life (beef cows = 1)</em></td><td width="103"><em>Number of days of life equivalent to pain of death</em></td><td width="101"><em>Expected suffering caused per kg demanded</em></td></tr><tr><td>Farmed catfish</td><td>730</td><td>0.48</td><td>1521</td><td>0.9<sup><a href="http://reducing-suffering.org/how-much-direct-suffering-is-caused-by-various-animal-foods/#note_fish">*</a></sup></td><td>10</td><td>1388</td></tr><tr><td>Farmed salmon</td><td>730</td><td>2.73</td><td>267</td><td>0.9<sup><a href="http://reducing-suffering.org/how-much-direct-suffering-is-caused-by-various-animal-foods/#note_fish">*</a></sup></td><td>10</td><td>244<sup><a href="http://reducing-suffering.org/how-much-direct-suffering-is-caused-by-various-animal-foods/#note_salmon">**</a></sup></td></tr><tr><td>Eggs</td><td>365</td><td>15.3</td><td>27.7</td><td>2.5</td><td>3.6</td><td>70</td></tr><tr><td>Chicken</td><td>42</td><td>1.83</td><td>23.0</td><td>1.8</td><td>5</td><td>46</td></tr><tr><td>Turkey</td><td>126</td><td>10.3</td><td>12.2</td><td>1.8</td><td>5</td><td>23</td></tr><tr><td>Pork</td><td>183</td><td>91.1</td><td>2.01</td><td>1.8</td><td>5</td><td>3.7</td></tr><tr><td>Beef</td><td>402</td><td>339</td><td>1.19</td><td>1</td><td>9</td><td>1.2</td></tr><tr><td>Milk</td><td>2009</td><td>50,420</td><td>0.040</td><td>1.8</td><td>5</td><td>0.07</td></tr></tbody></table>

\* I think the suffering of farmed fish may actually be greater than this, perhaps about the same as the average suffering of meat chickens. However, while the probability is close to 1 that land farm animals can suffer, the scientific jury is still out on whether fish can as well. I use a 50% probability of fish suffering to be conservative, though my actual probability is somewhat higher, perhaps 80%.

\*\* This analysis counts only the suffering of the farmed fish. Importantly, it ignores the many times more small fish that are killed to provide feed to these larger fish. When this is taken into account, it’s possible that large farmed fish like salmon outweigh smaller (especially non-farmed) fish in per-kilogram impact.

**Notes**

Readers should feel free to substitute their own estimates into columns 5 and 6; for instance, if they buy meat from a non-factory farm, the values in column 5 may be considerably smaller. A link at the bottom of the page allows users to download the above table as an Excel worksheet.

It’s important to remember that the above figures are _per kg demanded_, not _per kg eaten_ necessarily. If you get your meat from [dumpster diving](https://en.wikipedia.org/wiki/Dumpster_diving), you’re not causing suffering to other animals. There may be less extreme circumstances, too, in which eating animal products doesn’t contribute, or is less likely to contribute, to animal suffering. (See [Does Vegetarianism Make a Difference?](http://reducing-suffering.org/does-vegetarianism-make-a-difference/))

Similarly, _per kg_ doesn’t necessarily mean _per serving_. If your choice is between ordering a two-egg omelet (roughly [0.1 kg](http://reducing-suffering.org/how-much-direct-suffering-is-caused-by-various-animal-foods/#egg_weight) of egg) versus a half pound of chicken, then the latter would cause more direct expected suffering (10.4 units = 0.23 kg \* 46 units/kg) than the former (7 units = 0.1 kg \* 70 units/kg).

Download this table: [suffering-per-kg.xls](http://www.utilitarian-essays.com/suffering-per-kg.xls)

See the data sources: [Where the Numbers Came From](http://reducing-suffering.org/how-much-direct-suffering-is-caused-by-various-animal-foods/)

[http://www.utilitarian-essays.com/suffering-per-kg.html](http://reducing-suffering.org/how-much-direct-suffering-is-caused-by-various-animal-foods/)