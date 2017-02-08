<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\DataPool\KeyValue\Credis;

use Credis_Client;
use LizardsAndPumpkins\DataPool\KeyValueStore\Exception\KeyNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\DataPool\KeyValue\Credis\CRedisKeyValueStore
 */
class CredisKeyValueStoreTest extends TestCase
{
    /**
     * @var CredisKeyValueStore
     */
    private $store;

    /**
     * @var Credis_Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockClient;

    public function setUp()
    {
        $this->mockClient = $this->getMockBuilder(Credis_Client::class)
            ->setMethods(['get', 'set', 'exists', 'mGet', 'mSet'])
            ->getMock();
        $this->store = new CredisKeyValueStore($this->mockClient);
    }

    public function testSettingValueIsDelegatedToClient()
    {
        $key = 'key';
        $value = 'value';

        $this->mockClient->expects($this->once())->method('set')->with($key, $value);
        $this->store->set($key, $value);
    }

    public function testExceptionIsThrownDuringAttemptToGetAValueWhichIsNotSet()
    {
        $this->expectException(KeyNotFoundException::class);
        $this->mockClient->method('get')->willReturn(false);
        $this->store->get('not set key');
    }

    public function testGettingValueIsDelegatedToClient()
    {
        $key = 'key';
        $value = 'value';

        $this->mockClient->method('get')->with($key)->willReturn($value);

        $this->assertEquals($value, $this->store->get($key));
    }

    public function testCheckingKeyExistenceIsDelegatedToClient()
    {
        $key = 'key';
        $this->mockClient->method('exists')->with($key)->willReturn(true);

        $this->assertTrue($this->store->has($key));
    }

    public function testSettingMultipleKeysIsDelegatedToClient()
    {
        $items = ['key1' => 'foo', 'key2' => 'bar'];

        $this->mockClient->expects($this->once())->method('mSet')->with($items);
        $this->store->multiSet($items);
    }

    public function testEmptyArrayIsReturnedIfRequestedSnippetKeysArrayIsEmpty()
    {
        $this->assertSame([], $this->store->multiGet(...[]));
    }

    public function testGettingMultipleKeysIsDelegatedToClient()
    {
        $items = ['key1' => 'foo', 'key2' => 'bar'];
        $keys = array_keys($items);

        $this->mockClient->expects($this->once())->method('mGet')->with($keys)->willReturn($items);

        $this->assertSame($items, $this->store->multiGet(...$keys));
    }
}
