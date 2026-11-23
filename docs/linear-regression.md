---
title: Linear regression
intro: Fit a straight line to paired data and predict new values.
group: Guides
order: 70
---

## Fitting a line

Simple linear regression finds the straight line `y = intercept + slope * x` that lies closest to your points. The library uses ordinary least squares, which minimizes the sum of the squared vertical distances between the points and the line. `LinearRegression::fit()` takes the x values and the y values as two samples of the same size. The values are paired by position.

```php
use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\Sample;

$hours = new Sample([1, 2, 3, 4, 5]);
$score = new Sample([2, 4, 5, 4, 5]);

$regression = LinearRegression::fit($hours, $score);

$regression->slope;     // 0.6
$regression->intercept; // 2.2
```

The slope is how much y changes when x grows by one. The intercept is the value of y at `x = 0`. Both are public readonly properties, and the class has no public constructor.

## Goodness of fit

`rSquared` is the coefficient of determination. It is the share of the variation in y that the line explains, from 0 (none) to 1 (all). The library computes it as the squared Pearson correlation.

```php
use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\Sample;

$regression = LinearRegression::fit(new Sample([1, 2, 3, 4, 5]), new Sample([2, 4, 5, 4, 5]));

round($regression->rSquared, 2); // 0.6
```

When y is constant, the fitted line is horizontal and passes through every point. In that case `rSquared` is defined as 1.0, even though the correlation is undefined.

```php
use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\Sample;

$flat = LinearRegression::fit(new Sample([1, 2, 3]), new Sample([4, 4, 4]));

$flat->slope;     // 0.0
$flat->intercept; // 4.0
$flat->rSquared;  // 1.0
```

## Predicting values

`predict()` returns `intercept + slope * x` for any x. Predicting inside the range of your data is usually safe. Predicting far outside it, called extrapolation, is much less reliable.

```php
use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\Sample;

$regression = LinearRegression::fit(new Sample([1, 2, 3, 4, 5]), new Sample([3, 5, 7, 9, 11]));

$regression->predict(10.0); // 21.0
$regression->slope;         // 2.0
$regression->intercept;     // 1.0
```

## When the input is not valid

The two samples must have the same size, otherwise [`SampleSizeMismatchException`](errors.md) is thrown. The x values must not all be equal, because a vertical set of points has no slope. In that case [`ZeroVarianceException`](errors.md) is thrown.

```php
use PHPMath\Statistics\Exception\ZeroVarianceException;
use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\Sample;

try {
    LinearRegression::fit(new Sample([2, 2, 2]), new Sample([1, 2, 3]));
} catch (ZeroVarianceException $exception) {
    // A regression is undefined when x has zero variance.
}
```
