<?php

namespace Modules\Annotation;

/**
 * @Annotation
 * @DefaultAttribute value
 * @Attribute('value', required: true)
 * @Attribute('named', setter: 'setNamed')
 * @Attribute('array', type: {'string', 'int'})
 * @Attribute('enum', type: @Enum({'foo', 'bar', 'foobar'}))
 * @Target('class');
 */
class FooAnnotation
{
    const BAR = 'foobar';
    public $value;
    public $enum;
    private $named;

    public function setNamed($named)
    {
        $this->named = $named;
    }

    public function getNamed()
    {
        return $this->named;
    }
}

/**
 * @Annotation
 * @Attribute('simple', type: {'string'})
 * @Attribute('complex', type: {{'string', 'int'}})
 */
class ArrayAnnotation
{
    public $simple;
}

/**
 * Test class.
 * @see foo
 * @FooAnnotation('foo', named: 'foobar', enum: 'bar', array: {'string', 2})
 * @FooAnnotation(value: FooAnnotation::BAR)
 */
class TestClass
{
    /**
     * Property
     */
    public $property;

    /**
     * @foo
     */
    public function method()
    {
    }
}

/**
 * @ArrayAnnotation(simple: {'foo', 'bar', 'baz'})
 */
class SimpleArray
{
}

/**
 * @ArrayAnnotation(complex: {{'foo', 1}, {'bar', 2}, {'baz', 3}})
 */
class ComplexArray
{
}

/**
 * @ArrayAnnotation(simple: {'foo', 'bar', 'baz', 2})
 */
class InvalidSimpleArray
{
}

/**
 * @ArrayAnnotation(complex: {{'foo', 'bar'}, {'bar', 2}, {'baz', 3}})
 */
class InvalidComplexArray
{
}

/**
 * @FooAnnotation('foo', enum: 'br')
 */
class WrongEnumValueClass
{
}

/**
 * Function docs.
 */
function fooFunction()
{
}

class AnnotationReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnnotationReader
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new AnnotationReader;
    }

    public function testReadClass()
    {
        $comment = $this->object->readClass('Modules\Annotation\TestClass');
        $this->assertInstanceOf('Modules\Annotation\Comment', $comment);
        $this->assertTrue($comment->hasAnnotationType('Modules\\Annotation\\FooAnnotation'));
        $annotations = $comment->getAnnotationType('Modules\\Annotation\\FooAnnotation');
        $this->assertEquals('foo', $comment->get('see'));
        $this->assertEquals('foo', $annotations[0]->value);
        $this->assertEquals('foobar', $annotations[1]->value);
        $this->assertEquals('foobar', $annotations[0]->getNamed());
    }

    public function testArrays()
    {
        $this->object->readClass('Modules\Annotation\SimpleArray');
        $this->object->readClass('Modules\Annotation\ComplexArray');
    }

    /**
     * @expectedException \Modules\Annotation\Exceptions\AnnotationException
     */
    public function testArrayTypeExceptions()
    {
        $this->object->readClass('Modules\Annotation\InvalidSimpleArray');
    }

    /**
     * @expectedException \Modules\Annotation\Exceptions\AnnotationException
     */
    public function testArrayComplexTypeExceptions()
    {
        $this->object->readClass('Modules\Annotation\InvalidComplexArray');
    }

    /**
     * @expectedException \Modules\Annotation\Exceptions\AnnotationException
     */
    public function testExceptionIsThrownWhenValueIsNotInEnum()
    {
        $this->object->readClass('Modules\Annotation\WrongEnumValueClass');
    }

    public function testReadFunction()
    {
        $this->assertInstanceOf(
            'Modules\Annotation\Comment',
            $this->object->readFunction('Modules\Annotation\fooFunction')
        );
    }

    public function testReadMethod()
    {
        $this->assertInstanceOf(
            'Modules\Annotation\Comment',
            $this->object->readMethod('Modules\Annotation\TestClass', 'method')
        );
    }

    public function testReadProperty()
    {
        $this->assertInstanceOf(
            'Modules\Annotation\Comment',
            $this->object->readProperty('Modules\Annotation\TestClass', 'property')
        );
    }
}
