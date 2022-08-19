<?php


namespace Mimmi20Test\ErrorHandling;

use Mimmi20\ErrorHandling\LogListener;
use Mimmi20\ErrorHandling\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    /**
     * @throws void
     */
    public function testGetConfig(): void
    {
        $object = new Module();
        $config = $object->getConfig();

        static::assertIsArray($config);
        static::assertCount(1, $config);
        static::assertArrayHasKey('service_manager', $config);

        static::assertIsArray($config['service_manager']);
        static::assertCount(1, $config['service_manager']);
        static::assertArrayHasKey('factories', $config['service_manager']);

        static::assertIsArray($config['service_manager']['factories']);
        static::assertCount(1, $config['service_manager']['factories']);
        static::assertArrayHasKey(LogListener::class, $config['service_manager']['factories']);
    }

    /**
     * @throws void
     */
    public function testGetServiceConfig(): void
    {
        $object = new Module();
        $config = $object->getServiceConfig();

        static::assertIsArray($config);
        static::assertCount(1, $config);
        static::assertArrayHasKey('factories', $config);

        static::assertIsArray($config['factories']);
        static::assertCount(1, $config['factories']);
        static::assertArrayHasKey(LogListener::class, $config['factories']);
    }

    /**
     * @throws void
     */
    public function testGetModuleDependencies(): void
    {
        $object = new Module();
        $config = $object->getModuleDependencies();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey(0, $config);
        self::assertContains('Laminas\Log', $config);
    }
}
