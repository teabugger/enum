<?php

declare(strict_types=1);

namespace Teabugger\Enum\Tests;

use Teabugger\Enum\EnumException;
use Exception;
use PHPUnit\Framework\TestCase;
use Teabugger\Enum\Enum;

class EnumTest extends TestCase
{

    public function testValidCreate(): void
    {
        try {
            new EnumFixture(EnumFixture::FOO);
            new EnumFixture('foo');
            new EnumFixture(EnumFixture::BAR);
            self::assertTrue(true);
        } catch (Exception $exception) {
            self::fail('Unexpected exception.');
        }
    }

    /**
     * @depends testValidCreate
     */
    public function testInvalidCreate(): void
    {
        try {
            new EnumFixture('Foo');
            self::fail('Unexpected exception.');
        } catch (Exception $exception) {
            self::assertInstanceOf(EnumException::class, $exception);
            $class = EnumFixture::class;
            $expectedMessage = <<< EOD
                Invalid ENUM option for class {$class}.
                Given: Foo. Expected one of:
                foo
                bar
                EOD;
            self::assertEquals($expectedMessage, $exception->getMessage());
        }
    }

    /**
     * @depends testInvalidCreate
     */
    public function testInvalidEnumClass(): void
    {
        try {
            new EnumWithoutConstantsFixture('Foo');
            self::fail('Unexpected exception.');
        } catch (Exception $exception) {
            self::assertInstanceOf(EnumException::class, $exception);
            $class = EnumWithoutConstantsFixture::class;
            $expectedMessage = "Invalid ENUM: missing options. Class: {$class}.";
            self::assertEquals($expectedMessage, $exception->getMessage());
        }
    }

    /**
     * @depends testInvalidEnumClass
     */
    public function testGetValue(): void
    {
        $enum = new EnumFixture(EnumFixture::BAR);
        self::assertEquals(EnumFixture::BAR, (string)$enum);
    }

    /**
     * @depends testGetValue
     */
    public function testDebugInfo(): void
    {
        $enum = new EnumFixture(EnumFixture::BAR);
        self::assertEquals([
            'value' => EnumFixture::BAR,
            'options' => [
                'FOO' => EnumFixture::FOO,
                'BAR' => EnumFixture::BAR,
            ]
        ], $enum->__debugInfo());
    }
}
