<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\DataPool\KeyValue\Credis;

use Credis_Client;
use LizardsAndPumpkins\DataPool\KeyValueStore\Exception\KeyNotFoundException;
use LizardsAndPumpkins\DataPool\KeyValueStore\KeyValueStore;

class CredisKeyValueStore implements KeyValueStore
{
    /**
     * @var Credis_Client
     */
    private $client;

    public function __construct(Credis_Client $client)
    {
        $this->client = $client;
    }

    public function get(string $key) : string
    {
        $value = $this->client->get($key);

        if (false === $value) {
            throw new KeyNotFoundException(sprintf('Key not found "%s"', $key));
        }

        return $value;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        $this->client->set($key, $value);
    }

    public function has(string $key) : bool
    {
        return (bool) $this->client->exists($key);
    }

    /**
     * @param string[] $keys
     * @return string[]
     */
    public function multiGet(string ...$keys) : array
    {
        if (count($keys) === 0) {
            return [];
        }

        $values = $this->client->mGet($keys);
        $items = array_combine($keys, $values);

        return array_filter($items);
    }

    /**
     * @param string[] $items
     */
    public function multiSet(array $items)
    {
        $this->client->mSet($items);
    }
}
