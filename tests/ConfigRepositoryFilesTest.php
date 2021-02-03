<?php

use OpxCore\Config\ConfigRepositoryFiles;
use OpxCore\Config\Exceptions\ConfigRepositoryException;
use PHPUnit\Framework\TestCase;

class ConfigRepositoryFilesTest extends TestCase
{
    protected string $path;

    public function setUp(): void
    {
        $this->path = __DIR__ . DIRECTORY_SEPARATOR . 'config';
    }

    public function test_Wrong_Path(): void
    {
        $conf = new ConfigRepositoryFiles($this->path . 'wrong');
        $config = [];
        $loaded = $conf->load($config);
        self::assertFalse($loaded);
        self::assertEquals([], $config);
    }

    public function test_Null_Path(): void
    {
        $conf = new ConfigRepositoryFiles(null);
        $config = [];
        $loaded = $conf->load($config);
        self::assertFalse($loaded);
        self::assertEquals([], $config);
    }

    public function test_Empty_Path(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $loaded = $conf->load($config, 'empty');
        self::assertTrue($loaded);
        self::assertEquals([], $config);
    }

    public function test_Null_Profile(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $loaded = $conf->load($config);
        self::assertTrue($loaded);
        self::assertEquals([
            'app' => [
                'name' => 'null',
                'key' => ['null1', 'null2'],
            ],
            'test' => [
                'test' => 'another test',
            ],
        ], $config);
    }

    public function test_Default_Profile(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $loaded = $conf->load($config, 'default');
        self::assertTrue($loaded);
        self::assertEquals([
            'app' => [
                'name' => 'default',
                'key' => ['default1', 'default2'],
            ],
            'test' => [
                'test' => 'another test',
                'key' => ['val1', 'val2'],
            ],
        ], $config);
    }

    public function test_Default_Profile_Override_Test(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $loaded = $conf->load($config, 'default', 'test');
        self::assertTrue($loaded);
        self::assertEquals([
            'app' => [
                'name' => 'default',
                'key' => ['default1', 'default2'],
            ],
            'test' => [
                'test' => 'another test',
                'test2' => 'test',
                'key' => ['val1', 'val2'],
            ],
        ], $config);
    }

    public function test_Test_Profile_Override_Default(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $loaded = $conf->load($config, 'test', 'default');
        self::assertTrue($loaded);
        self::assertEquals([
            'app' => [
                'name' => 'test',
                'key' => ['default1', 'default2'],
            ],
            'test' => [
                'test' => 'another test',
                'test2' => 'test',
                'key' => ['test1', 'test2'],
            ],
        ], $config);
    }

    public function test_Default_Profile_Override_Empty(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $loaded = $conf->load($config, 'default', 'empty');
        self::assertTrue($loaded);
        self::assertEquals([
            'app' => [
                'name' => 'default',
                'key' => ['default1', 'default2'],
            ],
            'test' => [
                'test' => 'another test',
                'key' => ['val1', 'val2'],
            ],
        ], $config);
    }

    public function test_Empty_Profile_Override_Default(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $loaded = $conf->load($config, 'empty', 'default');
        self::assertTrue($loaded);
        self::assertEquals([
            'app' => [
                'name' => 'default',
                'key' => ['default1', 'default2'],
            ],
            'test' => [
                'test' => 'another test',
                'key' => ['val1', 'val2'],
            ],
        ], $config);
    }

    public function test_Save(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $saved = $conf->save($config, 'empty');
        self::assertFalse($saved);
    }

    public function testBadFileLoad(): void
    {
        $conf = new ConfigRepositoryFiles($this->path);
        $config = [];
        $this->expectException(ConfigRepositoryException::class);
        $conf->load($config, 'bad');
    }
}
