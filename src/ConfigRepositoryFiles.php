<?php
/*
 * This file is part of the OpxCore.
 *
 * Copyright (c) Lozovoy Vyacheslav <opxcore@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpxCore\Config;

use Error;
use Exception;
use OpxCore\Arr\Arr;
use OpxCore\Config\Exceptions\ConfigRepositoryException;
use OpxCore\Config\Interfaces\ConfigRepositoryInterface;

class ConfigRepositoryFiles implements ConfigRepositoryInterface
{
    /**
     * Path to directory with config files to load.
     *
     * @var string|null
     */
    protected ?string $configPath;

    /**
     * ConfigRepositoryFile constructor.
     *
     * @param string|null $path
     */
    public function __construct($path = null)
    {
        $this->configPath = $path ? rtrim($path, '\/') : $path;
    }

    /**
     * Load config from cache.
     *
     * @param array $config
     * @param string|null $profile
     * @param string|null $overrides
     *
     * @return  bool
     *
     * @throws  ConfigRepositoryException
     */
    public function load(array &$config, $profile = null, $overrides = null): bool
    {
        if ($this->configPath === null) {
            return false;
        }

        $loaded = null;

        $path = $this->configPath . ($profile ? DIRECTORY_SEPARATOR . $profile : $profile);

        $loaded = $this->loadFromPath($path);

        if ($loaded === null) {
            return false;
        }

        if ($overrides) {
            $path = $this->configPath . DIRECTORY_SEPARATOR . $overrides;

            $toOverride = $this->loadFromPath($path);

            if ($toOverride !== null || $toOverride !== []) {
                foreach (Arr::dot($loaded) as $key => $value) {
                    Arr::set($toOverride, $key, $value);
                }

                $loaded = $toOverride;
            }
        }

        $config = $loaded ?? [];

        return true;
    }

    /**
     * Loads all files from given path to array.
     *
     * @param string $path
     *
     * @return  array|null
     *
     * @throws  ConfigRepositoryException
     */
    protected function loadFromPath(string $path): ?array
    {
        if (!is_dir($path)) {
            return null;
        }

        $files = glob($path . DIRECTORY_SEPARATOR . '*.php');

        if ($files === [] || $files === null || $files === false) {
            return [];
        }

        $config = [];

        // Iterate all files and get content
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $name = pathinfo($file, PATHINFO_FILENAME);

            if (trim($name) !== '') {
                try {
                    $config[$name] = require $file;

                } catch (Error | Exception $e) {

                    throw new ConfigRepositoryException("Error reading configuration file {$e->getFile()}:{$e->getLine()} {$e->getMessage()}", 0, $e);
                }
            }
        }

        return $config;
    }

    /**
     * Save config to cache.
     *
     * @param array $config
     * @param null $profile
     *
     * @return  bool
     */
    public function save(array $config, $profile = null): bool
    {
        return false;
    }
}