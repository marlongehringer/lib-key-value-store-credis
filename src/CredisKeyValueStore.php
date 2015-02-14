<?php

namespace Brera\DaraPool\KeyValue\Credis;

use Brera\DaraPool\KeyValue\KeyNotFoundException;
use Brera\DaraPool\KeyValue\KeyValueStore;
use Credis_Client;

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
     * @return bool|string
     * @throws KeyNotFoundException
     */
    public function get($key)
    {
        if (!$value = $this->client->get($key)) {
            throw new KeyNotFoundException(sprintf('Key not found "%s"', $key));
        }

        return $value;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return null
     */
    public function set($key, $value)
    {
        $this->client->set($key, $value);
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
	    return (bool) $this->client->exists($key);
    }

	/**
	 * @param array $keys
	 * @return array
	 */
	public function multiGet(array $keys)
	{
		$values = $this->client->mGet($keys);
        $items = array_combine($keys, $values);

        return array_filter($items);
	}

	/**
	 * @param array $items
	 * @return null
	 */
	public function multiSet(array $items)
	{
		$this->client->mSet($items);
	}
}
