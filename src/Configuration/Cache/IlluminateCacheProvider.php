<?php

declare(strict_types=1);

namespace LaravelDoctrine\ORM\Configuration\Cache;

use Illuminate\Contracts\Cache\Factory;
use InvalidArgumentException;
use LaravelDoctrine\ORM\Configuration\Driver;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

use function trigger_error;

use const E_USER_DEPRECATED;

class IlluminateCacheProvider implements Driver
{
    protected string|null $store = null;

    public function __construct(protected Factory $cache)
    {
    }

    /** @param mixed[] $settings */
    public function resolve(array $settings = []): CacheItemPoolInterface
    {
        $store = $this->store ?? $settings['store'] ?? null;

        if ($store === null) {
            throw new InvalidArgumentException('Please specify the `store` when using the "illuminate" cache driver.');
        }

        if ($this->store && isset($settings['store'])) {
            trigger_error('Using driver "' . $this->store . '" with a custom store is deprecated. Please use the "illuminate" driver.', E_USER_DEPRECATED);
        }

        return new Psr16Adapter($this->cache->store($store), $settings['namespace'] ?? '', $settings['default_lifetime'] ?? 0);
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
