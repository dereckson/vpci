# VPCI (Vectorface PHP Caching Interface)
***

VPCI is a lightweight, straight forward cache interface that employs a strategy pattern to allow users to use multiple types of cache with a single unified interface.  It has optional built in write through caching to handle information retrieval with a simple, single function call.

Using VPCI allows developers to manage information retrieval and caching without having to worry about imlpementation.

***

## Contents

1. [Currently Supported Cache Types](#currently-supported-cache-types)
1. [Configurability](#configurability)
1. [Use](#use)
	2. [Get](#get)
	2. [Set](#set)
	2. [Clean](#clean)
	2. [Flush](#flush)

***

## Currently Supported Cache Types:

* APC User Cache (for both PHP 5.4 and PHP 5.5)
* Memcache
* TempFileCache (A caching system that uses temporary files)

***
[Top](#vpci-vectorface-php-caching-interface) - [Contents](#contents)
***

## Configurability

VPCI comes with a easy to set-up configuration system.  Its default behavior is to select the "best" (ordered as in the [Currently Supported](#currently-supported-cache-types) section) caching system.  The retrieval of the cache is done with a singleton, meaning that you will not loose your reference to your cache once it has been created.  Instead, simply call CacheSingleton::GetCache() whenever the reference is needed.

***
[Top](#vpci-vectorface-php-caching-interface) - [Contents](#contents)
***

## Use

VPCI is designed to run entirely on four commands: `get()`, `set()`, `clean()` and `flush()`.

### Get

The get function takes a single `$key` parameter.  This is the key of the item in the cache.  It returns the value stored with the given key.

```php
$key = "cache_item";
$cachedData = $cache->get(key);
```php

#### Write-Through

VPCI has an optional write-through cache feature built into the `use()` method.  [Above](#get) ignores the write-through functionality, using VPCI as a standard caching interface.  The `use()` method can actually take three parameters and is defined as `use($key, $callableArray = [], $ttl = -1)`.  The parameters do the following:

1. `$key` - The key used to retrieve the cached item
1. `$callableArray` - An array with optional callable object("function") and argument array("args") that will be called if no item exists in the cache.  The result will be returned
1. `$ttl` - The amount of time to store the item in the cache, if the callable is called.  If this is not set, the result will be returned but not stored in the cache.

```php
// Will return data if it is in the cache
// Otherwise it will return false
$cachedData = $cache->get("key");

// Will return data if it is in the cache
// Otherwise will return result of callable
// Cache will not be updated
$cachedData = $cache->get("key", ["function" => "callable", "args" => []]);

// Will return data if it is in the cache
// Otherwise will return result of callable
// The result of the callable will be stored in the cache with key = "key" and ttl=3600
$cachedData = $cache->get("key", ["function" => "callable", "args" => []], 3600);
```

### Set

The set function is used to store data in the cache.  It takes 3 parameters:

1. `$key` the key used to retrieve the cached item later
1. `$data` the data to be stored in the cache
1. `$ttl` the time to live of the cache item (the amount of time that the item should remain valid)

```php
$key = "cache_item";
$data = "This will be stored in the cache"
$ttl = 3600 //This cache item will expire in one hour
$cache->set($key, $data, $ttl);
```

### Clean

The clean function is used to clear any expired items out of the cache.  Note that for many caching implementations this might not be necessary.  Some cache implementations such as APC and Memcache clean themselves automatically.

	$cache->clean();

### Flush

The flush function deletes all items in the cache.

	$cache->flush();

***
[Top](#vpci-vectorface-php-caching-interface) - [Contents](#contents)
***