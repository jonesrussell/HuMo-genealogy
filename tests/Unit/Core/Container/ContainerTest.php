<?php

namespace Tests\Unit\Core\Container;

use HumoGen\Core\Container\BindingResolutionException;
use HumoGen\Core\Container\Container;
use HumoGen\Core\Contracts\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testContainerImplementsInterface(): void
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->container);
    }

    public function testBindBasicConcrete(): void
    {
        $this->container->bind('foo', 'bar');
        $this->assertTrue($this->container->has('foo'));
    }

    public function testMakeBasicConcrete(): void
    {
        $this->container->bind('foo', fn() => 'bar');
        $this->assertEquals('bar', $this->container->get('foo'));
    }

    public function testSingletonMakesTheSameInstance(): void
    {
        $this->container->singleton('date', \DateTime::class);
        
        $date1 = $this->container->get('date');
        $date2 = $this->container->get('date');
        
        $this->assertSame($date1, $date2);
    }

    public function testBindingAnInstance(): void
    {
        $instance = new \stdClass();
        $this->container->instance('foo', $instance);
        
        $this->assertSame($instance, $this->container->get('foo'));
    }

    public function testAutomaticResolution(): void
    {
        // Test class with no dependencies
        $instance = $this->container->make(SimpleClass::class);
        $this->assertInstanceOf(SimpleClass::class, $instance);

        // Test class with constructor dependency
        $instance = $this->container->make(ComplexClass::class);
        $this->assertInstanceOf(ComplexClass::class, $instance);
        $this->assertInstanceOf(SimpleClass::class, $instance->simple);
    }

    public function testResolutionFailsWithoutBinding(): void
    {
        $this->expectException(BindingResolutionException::class);
        $this->container->make('NonExistentClass');
    }

    public function testResolutionOfPrimitiveWithoutDefault(): void
    {
        $this->expectException(BindingResolutionException::class);
        $this->container->make(ClassWithPrimitive::class);
    }
}

// Test classes
class SimpleClass {}

class ComplexClass {
    public function __construct(
        public SimpleClass $simple
    ) {}
}

class ClassWithPrimitive {
    public function __construct(string $primitive) {}
} 