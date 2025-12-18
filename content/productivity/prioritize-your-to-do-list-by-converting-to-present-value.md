---
title: "Prioritize Your To-Do List By Converting To Present\_Value"
authors: []
date: 2019-04-13T00:02:11.000Z
metadata:
  categories:
    - Productivity
  uuid: 11ty/import::wordpress::https://thinkbynumbers.org/?p=9546
  type: wordpress
  url: >-
    https://thinkbynumbers.org/productivity/prioritize-your-to-do-list-by-converting-to-present-value/
tags:
  - productivity
---
## Prioritize Your To-Do List By Converting Costs and Benefits To Present Value

There is an infinite number of ways you can spend your most valuable resource, time. How can you decide what the most cost-effective way to spend your time is? By converting the returns of each activity to its present value and picking the activity with the highest present value.

I live in a house that’s bigger than I need and farther away from my job than I’d like. So I’d like to sell it and get a smaller apartment closer to work.

### Costs and Benefits of Moving

#### 1\. Time Savings

It’s also going to take a lot of time. In order to do apples to apples comparisons, we need to convert this time to a monetary value. According to the Bureau of Labor Statistics, the average American worker makes [$27/hour](https://www.bls.gov/news.release/empsit.t19.htm).

Moving closer to my job will save me 2 hours per week (an equivalent of $54 in weekly earnings). I assume I’ll be working there for 4 more years. This gives us a total savings of $11,232 (52 weeks \* $54/week \* 4 years).

However, this is complicated by the fact that due to inflation and interest-earning potential, a dollar today is worth more than a dollar tomorrow. So to achieve an apples to apples comparison, we’ll need to convert these savings to the equivalent Present Value.

So let’s calculate the equivalent present value of this expense. You can do this in Google Sheets with the following formula.

`Present Value = future_value/(1 + rate)^number_of_periods`

or in Google Sheets, you can use the formula:

`PV(rate, number_of_periods, future_value)`

-   `rate` – This is the rate that you could get by investing this money. [10%](https://www.nerdwallet.com/blog/investing/average-stock-market-return/) is the average annual rate of return for the stock market, so let’s use that.
-   `number_of_periods` – We’re using the annual return and I’ll be saving this drive for 4 years. So the number of periods is 4.
-   `future_value` – We’ll save $11k (52 weeks \* $54/week \* 4 years).

The result is a Present Value of $8k for the time savings from not having to drive so much.

#### 2\. Rent Savings

Say my monthly mortgage payment is $1400/month. I can get a smaller apartment for $900/month. So I’ll save $500 per month on rent. After 4 years, that will save me $24k. Plugging those numbers into our PV formula gives us $15k.

#### 2\. Selling & Moving Expenses

Moving comes with a great cost. Assuming a sale price of $200,000, the typical costs of selling a house and moving are $33,330.

<iframe loading="lazy" class="airtable-embed" style="background: transparent; border: 1px solid #ccc;" src="https://airtable.com/embed/shrKWs8DhDmaQi6zn?backgroundColor=yellow&amp;viewControls=on" width="100%" height="533" frameborder="0"></iframe>

Also, assume you have $116k remaining on the principal of your mortgage. That brings our total proceeds down to $50k.  
So far, our total PV we get from moving is $58k ($8 plus $50k).

#### 3\. Opportunity Cost of Future Sale

We also need to consider the opportunity cost to sell our house in 20 years once the mortgage is paid off. The U.S. House Price Index shows that prices have risen at 3.4% per year on average since 1991, so we’ll use that to illustrate our calculations.

To calculate the expected future value based on your growth rate, add one to the rate, and raise this to a power equal to the number of years you’re looking at.

`future_value = (1 + annual_growth_rate) ^ years * current_value`

or

`(1 + 0.0034) ^ 20 years * $200k = $390k Future Value`

So in 20 years, our home should be worth $390k. Let’s convert that back to the Present Value so we can compare apples to apples.

`Present Value = $390k/(1 + 0.1)^(20 years) = $58k`

### Net Present Value

The Net Present Value is what helps us make our final decision. It is the present value of the benefits minus the present value of the costs.

In our case, the Net Present Value is:  
`$7k + $18k -$58k - $33k + $200k - $116k = $18k`

Since $18k is positive, it’s time to move! 😀

<iframe loading="lazy" class="airtable-embed" style="background: transparent; border: 1px solid #ccc;" src="https://airtable.com/embed/shrmprxcBBETN5l1v?backgroundColor=yellow&amp;viewControls=on" width="100%" height="533" frameborder="0"></iframe>

### Future Value

Now let’s calculate how much better off we’ll be in 20 years if we sell. We can invest this $18k in an index fund getting us 10% return a year. In 20 years this $18k will be worth $121k.

## Opportunity Cost

However, let’s say I have a startup business that I could be working on in the time I spend moving. Let’s calculate how much time it will take me to move.

It will probably take about 40 hours to pack everything and another 40 hours to unpack everything and move it. We often underestimate, so lets double that to 160 hours of time taken. The median hourly rate is $27/hour. That’s a current time cost of $4320 bringing our total Present Value down to $14k.