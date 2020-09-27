<?php

declare(strict_types=1);

namespace Teabugger\Enum;

use ReflectionClass;
use ReflectionException;

abstract class Enum
{
    private string $value;

    /**
     * Enum constructor.
     * @param string $value
     * @throws EnumException
     */
    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    /**
     * @param string $value
     * @throws EnumException
     */
    private function validate(string $value): void
    {
        $constants = $this->getConstants();
        $valid = in_array($value, $constants, true);
        if (!$valid) {
            $class = get_class($this);
            if (!empty($constants)) {
                $implodedConstants = implode(PHP_EOL, $constants);
                $message = <<< EOD
                Invalid ENUM option for class $class.
                Given: $value. Expected one of:
                $implodedConstants
                EOD;
            } else {
                $message = "Invalid ENUM: missing options. Class: $class.";
            }
            throw new EnumException($message);
        }
    }

    /**
     * @return string[]
     * @throws ReflectionException
     */
    private function getConstants(): array
    {
        $class = new ReflectionClass($this);
        return $class->getConstants();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return mixed[]
     * @throws ReflectionException
     */
    public function __debugInfo(): array
    {
        return [
            'value' => $this->value,
            'options' => $this->getConstants(),
        ];
    }
}
