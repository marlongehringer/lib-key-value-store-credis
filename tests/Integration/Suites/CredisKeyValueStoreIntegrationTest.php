<?php

namespace Brera\DaraPool\KeyValue\Credis;

class CredisKeyValueStoreIntegrationTest extends \PHPUnit_Framework_TestCase
{
	const REDIS_HOST = 'localhost';

	const REDIS_PORT = '6379';

	/**
	 * @var CredisKeyValueStore
	 */
	private $keyValueStore;

	protected function setUp()
	{
		if (!class_exists(\Credis_Client::class)) {
			$this->markTestSkipped(sprintf('CRedis not available on %s:%s', self::REDIS_HOST, self::REDIS_PORT));
		}

		$client = new \Credis_Client(self::REDIS_HOST, self::REDIS_PORT);
		$client->del('foo');
		$client->del('key1');
		$client->del('key2');

		$this->keyValueStore = new CredisKeyValueStore($client);
	}

	/**
	 * @test
	 */
	public function itShouldSetAndGetAValue()
	{
		$this->keyValueStore->set('foo', 'bar');
		$result = $this->keyValueStore->get('foo');

		$this->assertEquals('bar', $result);
	}

	/**
	 * @test
	 */
	public function itShouldSetAndGetMultipleValues()
	{
		$items = ['key1' => 'foo', 'key2' => 'bar'];
		$keys = array_keys($items);

		$this->keyValueStore->multiSet($items);
		$result = $this->keyValueStore->multiGet($keys);

		$this->assertSame($items, $result);
	}

	/**
	 * @test
	 */
	public function itShouldExcludeMissingValuesFromResultArray()
	{
		$items = ['key1' => 'foo', 'key2' => 'bar'];
		$keys = array_keys($items);

		$this->keyValueStore->multiSet($items);

		array_push($keys, 'key3');
		$result = $this->keyValueStore->multiGet($keys);

		$this->assertSame($items, $result);
	}


	/**
	 * @test
	 */
	public function itShouldReturnFalseItKeyDoesNotExist()
	{
		$this->assertFalse($this->keyValueStore->has('foo'));
	}
}
