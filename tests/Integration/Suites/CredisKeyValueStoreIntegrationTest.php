<?php

namespace Brera\KeyValue\Credis;

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
		$keys = ['key1', 'key2'];
		$values = ['foo', 'bar'];
		$items = array_combine($keys, $values);

		$this->keyValueStore->multiSet($items);
		$result = $this->keyValueStore->multiGet($keys);

		$this->assertSame($values, $result);
	}

	/**
	 * @test
	 */
	public function itShouldReturnFalseItKeyDoesNotExist()
	{
		$this->assertFalse($this->keyValueStore->has('foo'));
	}
}
