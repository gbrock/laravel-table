# Laravel Tables
[![Made for Laravel 5](https://img.shields.io/badge/laravel-5.0-red.svg)](http://laravel.com/)
[![Latest Tag](https://img.shields.io/github/tag/gbrock/laravel-table.svg)](https://github.com/gbrock/laravel-table/releases)
<!--[![Build Status](https://img.shields.io/travis/gbrock/laravel-table.svg)](https://travis-ci.org/gbrock/laravel-table)-->

Adds database-level sorting to Laravel models.  **This project is under ongoing development.**


## Installation

Require the package in your `composer.json`:

```
"gbrock/laravel-table": "dev-master"
```

## Usage

Add the trait to the models you wish to sort.

```php
use Gbrock\Table\TableSortable;

class User extends Model {

	use TableSortable;
```

Since no fields are allowed to be sorted by default for security reasons, add a `$sortable` array to your model 
describing which fields are allowed to be sorted:

```php
/**
 * The attributes which may be used for sorting dynamically.
 *
 * @var array
 */
protected $sortable = ['id', 'username', 'email', 'last_login'];
```

Last, use the model's `sortable` [scope](http://laravel.com/docs/5.0/eloquent#query-scopes) to get your sorted data:

```php
// This is one possible way you could get what the user requested to be sorted.
$sortField = Input::get('sort');
$sortDir = Input::get('dir', 'desc'); // default desc

// Get the collection of rows using the "sorted" scope
$rows = User::sorted($sortField, $sortDir);
```

