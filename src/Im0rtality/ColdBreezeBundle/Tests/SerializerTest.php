<?php


namespace Im0rtality\ColdBreezeBundle\Tests;

use Im0rtality\ColdBreezeBundle\Serializer;
use Im0rtality\ColdBreezeBundle\Tests\Fixtures\DummyA;
use Im0rtality\ColdBreezeBundle\Tests\Fixtures\DummyB;

class SerializerTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer */
    protected $serializer;

    public function setUp()
    {
        $this->serializer = new Serializer();
    }

    public function getTestSerializeData()
    {
        $out = [];

        // simple object
        $out[] = [
            new DummyA(['foo' => 1, 'bar' => 2]),
            [],
            [
                'DummyA' => [
                    'fields'     => ['foo'],
                    'expand'     => [],
                    'dontExpand' => [],
                ]
            ],
            [
                'foo' => 1
            ]
        ];

        // simple collection
        $out[] = [
            [new DummyA(['foo' => 1, 'bar' => 2]), new DummyA(['foo' => 3, 'bar' => 4])],
            [],
            [
                'DummyA' => [
                    'fields'     => ['foo'],
                    'expand'     => [],
                    'dontExpand' => [],
                ]
            ],
            [
                [
                    'foo' => 1
                ],
                [
                    'foo' => 3
                ]
            ]
        ];

        // nested object
        $out[] = [
            new DummyA(['foo' => new DummyB(['id' => 7, 'baz' => 'a']), 'bar' => 2]),
            [],
            [
                'DummyA' => [
                    'fields'     => ['foo'],
                    'expand'     => [],
                    'dontExpand' => [],
                ]
            ],
            [
                'foo' => 7
            ]
        ];

        // nested object with expand
        $out[] = [
            new DummyA(['foo' => new DummyB(['id' => 7, 'baz' => 'a']), 'bar' => 2]),
            [],
            [
                'DummyA' => [
                    'fields'     => ['foo'],
                    'expand'     => ['foo'],
                    'dontExpand' => [],
                ],
                'DummyB' => [
                    'fields'     => ['baz'],
                    'expand'     => [],
                    'dontExpand' => [],
                ]
            ],
            [
                'foo' => ['baz' => 'a']
            ]
        ];

        // is* getter support
        $out[] = [
            new DummyB(['qux' => 42]),
            [],
            [
                'DummyB' => [
                    'fields'     => ['qux'],
                    'expand'     => [],
                    'dontExpand' => [],
                ]
            ],
            [
                'qux' => 42
            ]
        ];

        // serialize ISO8601 timestamp
        $out[] = [
            new DummyA(['bar' => new \DateTime('2014-05-10 11:00:00 +0300')]),
            [],
            [
                'DummyA' => [
                    'fields'     => ['bar'],
                    'expand'     => [],
                    'dontExpand' => [],
                ]
            ],
            [
                'bar' => '2014-05-10T11:00:00+03:00'
            ]
        ];

        // serialize nested collection
        $out[] = [
            new DummyA(['bar' => [new DummyB(['qux' => 42]), new DummyB(['qux' => 43])]]),
            [],
            [
                'DummyA' => [
                    'fields'     => ['bar'],
                    'expand'     => ['bar'],
                    'dontExpand' => [],
                ],
                'DummyB' => [
                    'fields'     => ['qux'],
                    'expand'     => [],
                    'dontExpand' => [],
                ]
            ],
            [
                'bar' => [['qux' => 42], ['qux' => 43]]
            ]
        ];

        // serialize collection of id
        $out[] = [
            new DummyA(['bar' => [new DummyB(['id' => 42]), new DummyB(['id' => 43])]]),
            [],
            [
                'DummyA' => [
                    'fields'     => ['bar'],
                    'expand'     => [],
                    'dontExpand' => [],
                ],
                'DummyB' => [
                    'fields'     => ['qux'],
                    'expand'     => [],
                    'dontExpand' => [],
                ]
            ],
            [
                'bar' => [42, 43]
            ]
        ];

        return $out;
    }

    /**
     * @dataProvider getTestSerializeData
     */
    public function testSerialize($object, $expands, $mapping, $expected)
    {
        $this->serializer->setExpands($expands);
        $this->serializer->setMapping($mapping);

        $this->assertEquals($expected, $this->serializer->serialize($object));
    }

    private function buildObject($data)
    {
        $object = new \stdClass();
        return $object;
    }
}
