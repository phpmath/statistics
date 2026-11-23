---
title: Normal distribution
intro: Work with the bell curve using its density, cumulative probability and quantiles.
group: Guides
order: 60
---

## Creating a distribution

A normal distribution is the bell curve. It is described by its mean, the centre of the curve, and its standard deviation, which sets how wide the curve is. The standard deviation must be finite and greater than zero, and the mean must be finite.

```php
use PHPMath\Statistics\NormalDistribution;

$iq = new NormalDistribution(mean: 100.0, standardDeviation: 15.0);

$iq->mean;              // 100.0
$iq->standardDeviation; // 15.0
```

An invalid standard deviation throws [`InvalidStandardDeviationException`](errors.md). A mean that is `NAN` or infinite throws [`NonFiniteValueException`](errors.md).

## Probability density

The probability density, `pdf()`, is the height of the curve at a value. It is highest at the mean. It is not a probability itself: it shows which values are more likely than others, and the area under the curve is what gives probabilities.

```php
use PHPMath\Statistics\NormalDistribution;

$iq = new NormalDistribution(100.0, 15.0);

$iq->pdf(100.0); // 0.02659615202676218
$iq->pdf(130.0); // 0.0035993977675458705
```

## Cumulative probability

The cumulative distribution, `cdf()`, is the probability that a random value is less than or equal to `x`. It always lies between 0 and 1, and it is exactly 0.5 at the mean. The tails stay accurate, so very small probabilities are not rounded to zero too early.

```php
use PHPMath\Statistics\NormalDistribution;

$iq = new NormalDistribution(100.0, 15.0);

$iq->cdf(100.0);                     // 0.5
$iq->cdf(115.0);                     // 0.841344746068543
$iq->cdf(130.0) - $iq->cdf(70.0);    // 0.9544997361036414
```

The last line is the probability of landing within two standard deviations of the mean, about 95 percent.

Both `pdf()` and `cdf()` accept infinite values and return the limits. A `NAN` value throws [`NonFiniteValueException`](errors.md).

```php
use PHPMath\Statistics\NormalDistribution;

$iq = new NormalDistribution(100.0, 15.0);

$iq->cdf(-INF); // 0.0
$iq->cdf(INF);  // 1.0
$iq->pdf(INF);  // 0.0
$iq->pdf(-INF); // 0.0
```

## Quantiles

The quantile, `quantile()`, is the opposite of `cdf()`. Give it a probability and it returns the value `x` for which the probability of being at or below `x` equals that probability. The probability must be strictly between 0 and 1, otherwise [`InvalidProbabilityException`](errors.md) is thrown.

```php
use PHPMath\Statistics\NormalDistribution;

$iq = new NormalDistribution(100.0, 15.0);

$iq->quantile(0.5);   // 100.0
$iq->quantile(0.975); // 129.39945976810083
```

The quantile is found by bisection on the cumulative distribution, so it is accurate but slower than `cdf()`. For the standard normal distribution (mean 0 and standard deviation 1), the quantile of 0.975 is the well known 1.96.

```php
use PHPMath\Statistics\NormalDistribution;

$standard = new NormalDistribution(0.0, 1.0);

round($standard->quantile(0.975), 2); // 1.96
```

## Fitting a distribution to data

The library has no fitting method, but a `Sample` gives you the numbers. Use the sample standard deviation when your data is a small part of a larger group.

```php
use PHPMath\Statistics\NormalDistribution;
use PHPMath\Statistics\Sample;

$heights = new Sample([172, 168, 181, 175, 170, 178, 169, 174]);
$normal = new NormalDistribution($heights->mean(), $heights->sampleStandardDeviation());

round($normal->cdf(180.0), 3); // 0.928
```
