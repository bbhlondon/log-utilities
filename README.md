# WIP - Log Utilities

## Install

Using composer:

```
php composer.phar require bbhlondon/log-utilities
```

## Usage

```php
require __DIR__.'/vendor/autoload.php';

use BBHLondon\LogUtilities\LogUtilities;

$logger = new BBHLondon\LogUtilities\LogUtilities;
$logger->scanDir('/var/log/apache/site1-access.log');
$logger->scanDir('/var/log/apache/site2-access.log');
$logger->find('/password-reset-request');
$logger->scan();
$logger->sortByDate();
$logger->dateFrom('2016-01-01 00:00:00');
$logger->dateTo('2016-02-01 23:59:59');
$logger->output('dir/export.log');

```
