# config-repository-files

# Config files loader
[![Build Status](https://travis-ci.com/opxcore/config-repository-files.svg?branch=main)](https://travis-ci.com/opxcore/config-repository-files)
[![Coverage Status](https://coveralls.io/repos/github/opxcore/config-repository-files/badge.svg)](https://coveralls.io/github/opxcore/config-repository-files)
[![Latest Stable Version](https://poser.pugx.org/opxcore/config-repository-files/v/stable)](https://packagist.org/packages/opxcore/config-repository-files)
[![Total Downloads](https://poser.pugx.org/opxcore/config-repository-files/downloads)](https://packagist.org/packages/opxcore/config-repository-files)
[![License](https://poser.pugx.org/opxcore/config-repository-files/license)](https://packagist.org/packages/opxcore/config-repository-files)
## Installing
```
composer require opxcore/config-repository-files
```

### Standalone usage:
```php
use OpxCore\Config\ConfigRepositoryFiles;

$configFiles = new ConfigRepositoryFiles($path);
```
### Usage with [container](https://github.com/opxcore/container)
```php
use OpxCore\Interfaces\ConfigRepositoryInterface;
use OpxCore\Config\ConfigRepositoryFiles;

$container->bind(
    ConfigRepositoryInterface::class, 
    ConfigRepositoryFiles::class, 
    ['path' => $path]
);

$configFiles = $container->make(ConfigRepositoryInterface::class);

// or

$container->bind(ConfigRepositoryInterface::class, ConfigRepositoryFiles::class);

$configFiles = $container->make(ConfigRepositoryInterface::class, ['path' => $path]);
```
Where `$path` is absolute path to folder with configuration files.

## Loading config
```php
$loaded = $configFiles->load($config, $profile, $overrides)
```
Loads array of configurations from path given in constructor. _Config files will
be loaded only from specified directory (see `$profile` for more details), no
subdirectories will be included._

`$config` is array with loaded configuration. In case of failure (config directory
is not existing or some error occurred while loading files) this variable will
not be modified. Otherwise it will contain array of read configs or empty array
if config directory is existing but there is no any config files.

`$profile` is profile name of configuration to load. Can be `null`, `'default'`
or some string identifier. `null` means config files are placed in directory
specified in `$path` in constructor. If `$profile` is `'default'` or any string
identifier configs will be loaded from `{$path}/{$profile}` directory.

`$overrides` is profile name to be overridden by `$profile`. It means profile
with name `$overrides` will be loaded and then all keys existing in `$profile`
will be recursively merged to result. So you can have a default profile and
override some values with another profile you need.

`true` will be returned in cases of directory exists and there is no errors reading files  
(or if directory exists, but empty). If directory is not existing or any error occurred
`false` will be returned.

## Saving config
Not implemented yet.

## Config file content
File with config must return array.
```php
// app.php
<?php

return [
    'name' => 'My awesome application',
    'enabled' => env('ENABLED', true),
    // and so on
];
```
You can use `env()` function provided by
[config-environment](https://github.com/opxcore/config-environment), installed as
dependency. If you wish to use this feature, you must load environment first.
```php
\OpxCore\Config\ConfigEnvironment::load();
``` 
See [config-environment](https://github.com/opxcore/config-environment) for details.
## Config files examples
```
/myapp/
    config/
        default/
            app.php
            cache.php 
        web/
            app.php
            cache.php 
        app.php
        cache.php 
```
```php
$configFiles = new ConfigRepositoryFiles('/myapp/config');

// loads config files from /myapp/config
$configFiles->load($config);

// loads config files from /myapp/config/default
$configFiles->load($config, 'default');

// loads config files from /myapp/config/web and merges result
// with /myapp/config/default. 'web' has higher priority.
$configFiles->load($config, 'web', 'default');
```