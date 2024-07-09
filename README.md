<h1 align="center">Repository · Persistence</h1>

[![Packagist](https://img.shields.io/packagist/v/tomkyle/repository-persistence.svg?style=flat)](https://packagist.org/packages/tomkyle/repository-persistence)
[![PHP version](https://img.shields.io/packagist/php-v/tomkyle/repository-persistence.svg)](https://packagist.org/packages/tomkyle/repository-persistence)
[![PHP Composer](https://github.com/tomkyle/repository-persistence/actions/workflows/php.yml/badge.svg)](https://github.com/tomkyle/repository-persistence/actions/workflows/php.yml) 
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

**Scaffold for Repository-and-Persistence design pattern.**

---

## Installation

```bash
$ composer require tomkyle/repository-persistence
```

## Setup

**The repository needs a persistence.**

```php 
<?php

use tomkyle\RepositoryPersistence\Repositories\Repository;
use tomkyle\RepositoryPersistence\Persistence;

$repo = new Repository(
	new Persistence\JsonFilePersistence('path/to/json')
);
```

In this example, the Persistence works on a directory `path/to/json` in which the items are stored in JSON files named by their ID. — Example: **john-doe.json**

```json
{
  "age": 30,
  "city": "New York",
  "name": "John Doe"
}
```

## Usage

**Get item.** This method may throw `\OutOfBoundsException`

```php
try {
  $person = $repo->get('john-doe');
  print_r($person);
} 
catch (\OutOfBoundsException) {
  // Not found
}
```

Output will be like:

```text
Array  (
  [age] => 30
  [city] => New York
  [name] => John Doe
)
```

**Find one item by criteria.** This method my return `null`.

```php
$repo->findOneBy([
  'name' => 'John'
]);  
```

**Get all items:**

```php
$repo->findAll();
```

**Find items by criteria**

```php
$repo->findBy([
  'color' => 'blue'
]);
```

**Update item**

```php
$saved = $repo->save(['id' => 43, 'name' => 'John']));
```

**Delete item**

```php
$repo->delete(43);
```

**Create new item**

```php
$saved = $repo->save(['name' => 'Angie']));
```

If you need the new ID onbeforehand in your App controller, e.g. for redirecting the client to the new resource, you can obtain a new ID from the repo. It then looks exactly like updating, but the *Repository* implementation will figure out if the item has to be created or updated.

```php
$new_id = $repo->getNextId();
$repo->save(['id' => $new_id, 'name' => 'Angie']));
```



---

## Persistence

Inside a repository, the *Persistence* actually manages the data storage.

### Instantiation

```php
<?php
use tomkyle\RepositoryPersistence\Repositories;
use tomkyle\RepositoryPersistence\Persistence;

$persistence = new Persistence\JsonFilePersistence('path/to/json');
$persistence = new Persistence\YamlFilePersistence('path/to/yaml');
```

### Methods API

| Method  | Parameters      | Return       | Description   |
| ------- | --------------- | ------------ | ------------- |
| create  | `array` data    | `string|int` | New ID        |
| read    | `string|int` id | `array`      | The record    |
| readAll |                 | array        | All records   |
| update  | `array` data    | `int`        | Affected rows |
| delete  | `string|int`    | `int`        | Affected rows |

### Special implementations

#### FrontmatterFilePersistence

If your JSON or YAML files have frontmatters:

```php
$persistence = new Persistence\FrontmatterFilePersistence(
	new Persistence\JsonFilePersistence('path/to/json')
);
```

#### PersistenceChain

```php
$persistence = new Persistence\PersistenceChain(
	new Persistence\JsonFilePersistence('path/to/json'),
	new Persistence\YamlFilePersistence('path/to/yaml')
);
```

#### InMemoryPersistence

An empty *Persistence* you can write and read to.

```php
$persistence = new Persistence\InMemoryPersistence();
```

#### NoPersistence

Mock implementation of *Persistence* that simulates data persistence operations without actually storing data. Note that *read* method will always throw `\OutOfBoundsException` as it does not contain any data!

```php
$persistence = new Persistence\NoPersistence();
```



---

## Repository

The repository is the thing you work with in your app.

```php
<?php
use tomkyle\RepositoryPersistence\Repositories\Repository;
use tomkyle\RepositoryPersistence\Persistence; 

// Feed a persistence to the repo:
$persistence = new Persistence\InMemoryPersistence();
$repository = new Repository($persistence)
```

### Methods API

| Method    | Required Parameters   | Optional                                                 | Return              | Description  |
| --------- | --------------------- | -------------------------------------------------------- | ------------------- | ------------ |
| get       | `string|id` id        |                                                          | `array|object`      | The record   |
| findOneBy | `array` criteria      |                                                          | `array|object|null` | One record   |
| findAll   |                       |                                                          | `iterable`          | All records  |
| findBy    | `array` criteria      | `?array` orderBy,<br /> `?int` limit<br /> `?int` offset | `iterable`          | Some records |
| save      | `array|object` entity |                                                          | `bool`              |              |
| delete    | `array|object` entity |                                                          | `bool`              |              |

---

## Development

### Install requirements

```bash
$ composer install
$ npm install
```

### Watch source and run various tests

This will watch changes inside the **src/** and **tests/** directories and run a series of tests:

1. Find and run the according unit test with *PHPUnit*.
2. Find possible bugs and documentation isses using *phpstan*. 
3. Analyse code style and give hints on newer syntax using *Rector*.

```bash
$ npm run watch
```

### Run all tests

Choose to your taste:

```bash
$ npm run phpunit
$ composer test
```

