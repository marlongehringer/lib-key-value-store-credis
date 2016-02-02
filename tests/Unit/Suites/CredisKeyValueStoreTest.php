<?php

namespace LizardsAndPumpkins\DataPool\KeyValue\Credis;

use Credis_Client;
use LizardsAndPumpkins\DataPool\KeyValue\Exception\KeyNotFoundException;

/**
 * @covers \LizardsAndPumpkins\DataPool\KeyValue\Credis\CRedisKeyValueStore
 */
class CredisKeyValueStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CredisKeyValueStore
     */
    private $store;

	/**
	 * @var Credis_Client|\PHPUnit_Framework_MockObject_MockObject
	 */
	private $stubClient;

    public function setUp()
    {
	    $this->stubClient = $this->getMockBuilder(Credis_Client::class)
	        ->setMethods(['get', 'set', 'exists', 'mGet', 'mSet'])
	        ->getMock();
        $this->store = new CredisKeyValueStore($this->stubClient);
    }

    public function testValueIsSetAndRetrieved()
    {
        $key = 'key';
        $value = 'value';

	    $this->stubClient->expects($this->once())->method('set');
	    $this->stubClient->method('get')->willReturn($value);

        $this->store->set($key, $value);
        $this->assertEquals($value, $this->store->get($key));
    }

    public function testTrueIsReturnedOnlyAfterValueIsSet()
    {
        $key = 'key';
        $value = 'value';

        $this->assertFalse($this->store->has($key));

	    $this->stubClient->expects($this->once())->method('exists')->willReturn(true);

        $this->store->set($key, $value);
        $this->assertTrue($this->store->has($key));
    }

    public function testExceptionIsThrownIfValueIsNotSet()
    {
        $this->setExpectedException(KeyNotFoundException::class);
        $this->store->get('not set key');
    }

	public function testMultipleKeysAreSetAndRetrieved()
	{
        $items = ['key1' => 'foo', 'key2' => 'bar'];
        $keys = array_keys($items);

		$this->stubClient->expects($this->once())->method('mSet');

		$this->store->multiSet($items);

		$this->stubClient->expects($this->once())->method('mGet')->willReturn($items);

		$result = $this->store->multiGet($keys);

		$this->assertSame($items, $result);
	}
}
