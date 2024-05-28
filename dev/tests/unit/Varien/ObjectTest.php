<?php
declare(strict_types=1);

namespace unit\Varien;

use stdClass;
use Varien_Exception;
use Varien_Object;
use PHPUnit\Framework\TestCase;

class ObjectTest extends TestCase
{
    /**
     * @var Varien_Object
     */
    private Varien_Object $subject;

    public function setUp(): void
    {
        $this->subject = new Varien_Object();
    }

    /**
     * @dataProvider provideGetDataData
     * @param mixed $expectedResult
     * @param string $setKey
     * @param mixed $setValue
     * @param string $key
     * @param string|int|null $index
     * @return void
     */
    public function testGetData($expectedResult, $setKey, $setValue, string $key, $index = null): void
    {
        $this->subject->setData($setKey, $setValue);
        self::assertEquals($expectedResult, $this->subject->getData($key, $index));
    }

    /**
     * @return array<string, array<int, array<int|string, array<int|string, int|string>|int|string>|int|stdClass|string|Varien_Object|null>>
     */
    public function provideGetDataData(): array
    {
        return [
            'empty_key' => [
                ['empty_key' => ['empty_value']],
                'empty_key',
                ['empty_value'],
                ''
            ],
            'string' => [
                'value',
                'string',
                'value',
                'string'
            ],
            'int' => [
                1,
                'int',
                1,
                'int'
            ],
            'numeric' => [
                '1',
                'numeric',
                '1',
                'numeric'
            ],
            'array' => [
                ['string', 1],
                'array',
                ['string', 1],
                'array',
            ],
            'array_index_int' => [
                'string',
                'array_index_int',
                ['string', 1],
                'array_index_int',
                0,
            ],
            'array_index_int_invalid' => [
                null,
                'array_index_int_invalid',
                ['string', 1],
                'array_index_int_invalid',
                999,
            ],
            'array_index_string' => [
                1,
                'array_index_string',
                ['string' => 'string', 'int' => 1],
                'array_index_string',
                'int',
            ],
            'array_index_string_string' => [
                null,
                'array_index_string_string',
                'some_string',
                'array_index_string_string',
                'not-exists',
            ],
            'array_index_string_varien_object' => [
                [],
                'array_index_string_varien_object',
                new Varien_Object(['array' => []]),
                'array_index_string_varien_object',
                'array',
            ],
             'array_index_string_std_class' => [
                null,
                'array_index_string_std_class',
                new stdClass(),
                'array_index_string_std_class',
                'not-exists',
            ],
            'array_nested' => [
                1,
                'array_nested',
                ['nested' => ['string' => 'string', 'int' => 1]],
                'array_nested/nested/int',
            ],
            'array_nested_invalid_key' => [
                null,
                'array_nested',
                ['nested' => ['string' => 'string', 'int' => 1]],
                'array_nested/nested/invalid_key',
            ],
            'array_nested_empty_key' => [
                null,
                'array_nested',
                ['nested' => ['string' => 'string', 'int' => '']],
                'array_nested/nested/',
            ],
            'array_nested_string' => [
                'some"\n"string',
                'array_nested_string',
                ['nested' => 'some"\n"string'],
                'array_nested_string/nested',
            ],
             'array_nested_varien_object' => [
                null,
                'array_nested_varien_object',
                new Varien_Object(),
                'array_nested_varien_object/nested',
            ],
            'array_nested_std_class' => [
                null,
                'array_nested_std_class',
                new stdClass(),
                'array_nested_std_class/nested',
            ],
            'array_nested_key_not_exists' => [
                null,
                'array_nested_key_not_exists',
                ['nested' => ['string' => 'string', 'int' => 1]],
                'array_nested_key_not_exists_test/nested/int',
            ],
        ];
    }

    public function testToString(): void
    {
        $this->subject->setString1('open');
        $this->subject->setString2('mage');
        self::assertSame('open, mage', $this->subject->toString());
        self::assertSame('openmage', $this->subject->toString('{{string1}}{{string2}}'));
        self::assertSame('open', $this->subject->toString('{{string1}}{{string_not_exists}}'));
    }

    public function testGetSetUnsData(): void
    {
        self::assertTrue($this->subject->isEmpty());
        $this->subject->setABC('abc');
        $this->subject->setData('efg', 'efg');
        $this->subject->set123('123');
        $this->subject->setData('345', '345');
        $this->subject->setKeyAFirst('value_a_first');
        $this->subject->setData('key_a_2nd', 'value_a_2nd');
        $this->subject->setKeyA3rd('value_a_3rd');
        $this->subject->setData('left', 'over');
        self::assertFalse($this->subject->isEmpty());

        self::assertSame('abc', $this->subject->getData('a_b_c'));
        self::assertSame('abc', $this->subject->getABC());
        $this->subject->unsetData('a_b_c');

        self::assertSame('efg', $this->subject->getData('efg'));
        self::assertSame('efg', $this->subject->getEfg());
        $this->subject->unsEfg();

        self::assertSame('123', $this->subject->getData('123'));
        self::assertSame('123', $this->subject->get123());
        $this->subject->uns123();

        $this->subject->unsetData('345');

        self::assertSame('value_a_first', $this->subject->getData('key_a_first'));
        self::assertSame('value_a_first', $this->subject->getKeyAFirst());
        $this->subject->unsetData('key_a_first');

        self::assertSame('value_a_2nd', $this->subject->getData('key_a_2nd'));
        self::assertSame('value_a_2nd', $this->subject->getKeyA_2nd());
        $this->subject->unsetData('key_a_2nd');

        self::assertSame('value_a_3rd', $this->subject->getData('key_a3rd'));
        self::assertSame('value_a_3rd', $this->subject->getKeyA3rd());
        $this->subject->unsetData('key_a3rd');

        self::assertSame(['left' => 'over'], $this->subject->getData());

        $this->subject->unsetData();
        self::assertSame([], $this->subject->getData());
        self::assertTrue($this->subject->isEmpty());

        try {
            /** @phpstan-ignore-next-line */
            $this->subject->notData();
            self::fail('Invalid __call');
        } catch (Varien_Exception $exception) {
            self::assertStringStartsWith('Invalid method', $exception->getMessage());
        }
    }

    public function testOffset(): void
    {
        self::assertFalse($this->subject->offsetExists('off'));

        $this->subject->offsetSet('off', 'set');
        self::assertTrue($this->subject->offsetExists('off'));
        self::assertSame('set', $this->subject->offsetGet('off'));
        self::assertSame(null, $this->subject->offsetGet('not-exists'));

        $this->subject->offsetUnset('off');
        self::assertFalse($this->subject->offsetExists('off'));
    }
}
