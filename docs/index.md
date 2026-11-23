---
title: Introduction
intro: Descriptive statistics for PHP. Every sample, distribution and regression is an immutable value object.
group: Getting started
order: 10
---

## Requirements

The library needs a current PHP version. It has no other requirements and no runtime dependencies.

- PHP 8.4 or newer

## Installation

Install the library with Composer.

```bash
composer require phpmath/statistics
```

## A first example

Put your numbers in a `Sample` and ask for the statistic you need. Each method returns a plain value or a new object. Comments in the examples show the result as `var_export()` prints it, so a float such as 5 is written as `5.0`.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

$sample->mean();                    // 5.0
$sample->median();                  // 4.5
$sample->populationVariance();      // 4.0
$sample->sampleStandardDeviation(); // 2.138089935299395
```

Calling `mean()` did not change `$sample`. This is true for every operation in the library.

## Where to go next

Start with the core concepts, or jump straight to the topic you need.

<div class="cards"><a href="concepts.md"><strong>Core concepts</strong><span>Immutability, creating values and reading values.</span></a><a href="descriptive-statistics.md"><strong>Descriptive statistics</strong><span>Mean, median, mode, variance and skewness.</span></a><a href="percentiles-and-quartiles.md"><strong>Percentiles and quartiles</strong><span>Where a value sits in the sorted data.</span></a><a href="covariance-and-correlation.md"><strong>Covariance and correlation</strong><span>How two samples move together.</span></a><a href="normal-distribution.md"><strong>Normal distribution</strong><span>Density, cumulative probability and quantiles.</span></a><a href="linear-regression.md"><strong>Linear regression</strong><span>Fit a line and predict new values.</span></a><a href="api.md"><strong>API reference</strong><span>Every method with its parameters and errors.</span></a></div>
