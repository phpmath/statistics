# statistics

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![CI][ico-ci]][link-ci]
[![Total Downloads][ico-downloads]][link-downloads]

A PHP library for immutable descriptive statistics on float data.

## Documentation

The full documentation is at https://phpmath.github.io/statistics/. Every release is described on the [releases page](https://github.com/phpmath/statistics/releases).

## Install

```bash
composer require phpmath/statistics
```

## Requirements

- PHP 8.4 or newer

## Usage

Every class is an immutable value object. Operations never change the instance they are called on, they return
a new instance or a plain value.

``` php
use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\NormalDistribution;
use PHPMath\Statistics\Sample;

$sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

$sample->mean();                      // 5.0
$sample->median();                    // 4.5
$sample->mode();                      // [4.0]
$sample->populationVariance();        // 4.0
$sample->sampleStandardDeviation();   // 2.138...
$sample->percentile(90);              // linear interpolation (R-7)
$sample->zScores();                   // Sample
$sample->map(fn (float $x): float => $x * 2); // new Sample

$x = new Sample([1, 2, 3, 4, 5]);
$y = new Sample([3, 5, 7, 9, 11]);

$x->correlation($y);                  // 1.0
$regression = LinearRegression::fit($x, $y);
$regression->predict(10.0);           // 21.0

$normal = new NormalDistribution(mean: 100.0, standardDeviation: 15.0);
$normal->cdf(115.0);                  // 0.8413...
$normal->quantile(0.975);             // 129.39...
```

## Features

* `Sample`: a non-empty list of finite floats with count, sum, min, max, range, mean, median, mode (all modes),
  population and sample variance and standard deviation, percentile, quartiles, interquartile range, skewness,
  population and sample covariance, Pearson correlation, z-scores and the derived samples `map()`, `plus()`,
  `times()` and `sorted()`.
* `NormalDistribution`: probability density, cumulative distribution and quantile.
* `LinearRegression`: ordinary least squares fit with slope, intercept, R squared and `predict()`.
* Specific exceptions in `PHPMath\Statistics\Exception`, all implementing `StatisticsException`.

Numerical choices: sums use Neumaier compensated (Kahan style) summation, variance, skewness and covariance use
single-pass Welford updates, and percentiles use linear interpolation between closest ranks (Hyndman and Fan
type 7, as in R, NumPy and Excel's `PERCENTILE.INC`).

## Development

```bash
composer test
composer test-coverage
composer analyse
composer check-style
composer fix-style
```

## Contributing

All contributions are welcome. Open an issue or a pull request on GitHub. Please follow the coding standard and add tests.

## Security

If you discover a security issue, please create an issue in the issue tracker.

## Credits

- [Walter Tamboer](https://github.com/waltertamboer)
- [All contributors](https://github.com/phpmath/statistics/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/phpmath/statistics.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ci]: https://img.shields.io/github/actions/workflow/status/phpmath/statistics/ci.yml?branch=master&style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/phpmath/statistics.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/phpmath/statistics
[link-ci]: https://github.com/phpmath/statistics/actions/workflows/ci.yml
[link-downloads]: https://packagist.org/packages/phpmath/statistics/stats
