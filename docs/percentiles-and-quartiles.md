---
title: Percentiles and quartiles
intro: Find the value below which a given share of the data falls.
group: Guides
order: 40
---

## Percentiles

A percentile is the value below which a given percentage of the data falls. The 90th percentile is larger than about 90 percent of the values. `percentile()` takes a number from 0 to 100, where 0 is the minimum and 100 is the maximum.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

$sample->percentile(0);   // 2.0
$sample->percentile(50);  // 4.5
$sample->percentile(90);  // 7.6
$sample->percentile(100); // 9.0
```

## How the value is calculated

Data rarely has a value at exactly the requested position, so the library interpolates. It sorts the values, finds the position `p / 100 * (n - 1)` counting from zero, and draws a straight line between the two closest values. This is the method known as Hyndman and Fan type 7. It is the default of R and NumPy, and it matches Excel's `PERCENTILE.INC`.

With eight values, the 90th percentile sits at position 6.3. That is 30 percent of the way from the seventh value (7) to the eighth value (9), which gives 7.6.

A percentile outside 0 to 100 throws [`InvalidPercentileException`](errors.md). `NAN` is rejected in the same way.

```php
use PHPMath\Statistics\Exception\InvalidPercentileException;
use PHPMath\Statistics\Sample;

try {
    (new Sample([1, 2, 3]))->percentile(101);
} catch (InvalidPercentileException $exception) {
    // A percentile must be between 0 and 100.
}
```

## Quartiles

Quartiles split the sorted data into four equal parts. The first quartile is the 25th percentile, the second quartile is the 50th percentile (the median) and the third quartile is the 75th percentile. `quartiles()` returns a `Quartiles` object with three public readonly properties: `first`, `second` and `third`.

```php
use PHPMath\Statistics\Sample;

$quartiles = (new Sample([2, 4, 4, 4, 5, 5, 7, 9]))->quartiles();

$quartiles->first;  // 4.0
$quartiles->second; // 4.5
$quartiles->third;  // 5.5
```

The quartiles use the same interpolation as `percentile()`, so `quartiles()->second` always equals `median()`.

You can also create a `Quartiles` object yourself. The constructor requires finite values with `first <= second <= third`, otherwise [`NonFiniteValueException`](errors.md) or [`InvalidQuartilesException`](errors.md) is thrown.

```php
use PHPMath\Statistics\Exception\InvalidQuartilesException;
use PHPMath\Statistics\Quartiles;

try {
    new Quartiles(3.0, 2.0, 4.0);
} catch (InvalidQuartilesException $exception) {
    echo $exception->getMessage(); // The quartiles must satisfy first <= second <= third.
}
```

## Interquartile range

The interquartile range (IQR) is the distance between the third and the first quartile. It describes the spread of the middle half of the data, and unlike the range it is not pulled around by a single extreme value.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

$sample->interquartileRange(); // 1.5
```

## Spotting outliers

A common rule marks a value as an outlier when it lies more than 1.5 times the IQR below the first quartile or above the third quartile. You can build this from the pieces above.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([10, 12, 12, 13, 12, 11, 14, 13, 95]);

$quartiles = $sample->quartiles();
$limit = 1.5 * $sample->interquartileRange();
$upper = $quartiles->third + $limit;

$upper; // 14.5
array_values(array_filter($sample->toArray(), fn (float $value): bool => $value > $upper)); // [95.0]
```
