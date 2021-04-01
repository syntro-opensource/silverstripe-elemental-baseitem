# SilverStripe elemental base item

![🎭 Tests](https://github.com/syntro-opensource/silverstripe-elemental-baseitem/workflows/%F0%9F%8E%AD%20Tests/badge.svg)
[![phpstan](https://img.shields.io/badge/PHPStan-enabled-success)](https://github.com/phpstan/phpstan)
[![codecov](https://codecov.io/gh/syntro-opensource/silverstripe-elemental-baseitem/branch/master/graph/badge.svg)](https://codecov.io/gh/syntro-opensource/silverstripe-elemental-baseitem)
[![composer](https://img.shields.io/packagist/dt/syntro/silverstripe-elemental-baseitem?color=success&logo=composer)](https://packagist.org/packages/syntro/silverstripe-elemental-baseitem)



This module adds a blank base item which you can use to add sub-items to elements.



## Requirements

* SilverStripe ^4
* Silverstripe elemental ^4

## Installation

```
composer require syntro/silverstripe-elemental-baseitem
```


## License
See [License](license.md)

## Documentation

Simply extend the base item:

```php
use Syntro\SilverStripeElementalBaseitems\Model\BaseItem;

class Teaser extends BaseItem
{
    // ...
}
```
By default, the baseItem has a `Title` and a `ShowTitle` field, similar to the
BaseElement in elemental. They will also use the title composite field.

This behaviour can be disabled by setting `displays_title_in_template` to false
in yaml config or directly in the class.

## Maintainers
 * Matthias Leutenegger <hello@syntro.ch>

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
 Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
