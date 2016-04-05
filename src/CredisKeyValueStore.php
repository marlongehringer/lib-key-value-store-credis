<?php

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

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        $value = $this->client->get($key);

        if (false === $value) {
            throw new KeyNotFoundException(sprintf('Key not found "%s"', $key));
        }

        return $value;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        $this->client->set($key, $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return (bool) $this->client->exists($key);
    }

    /**
     * @param string[] $keys
     * @return string[]
     */
    public function multiGet(array $keys)
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
