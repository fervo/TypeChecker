<?php


namespace Fervo\TypeChecker;


class TypeChecker
{
    private static $parser;
    private static $cache = [];

    private static function getParser(): TypeParser
    {
        if (!self::$parser) {
            self::$parser = new TypeParser();
        }

        return self::$parser;
    }

    private static function getTypeData(string $type): array
    {
        if (!isset(self::$cache[$type])) {
            self::$cache[$type] = self::getParser()->parse($type);
        }

        return self::$cache[$type];
    }

    public static function checkType(string $type, $value): bool
    {
        $typeData = self::getTypeData($type);

        return self::checkTypeData($typeData, $value);
    }

    public static function assertType(string $type, $value)
    {
        if (!self::checkType($type, $value)) {
            throw new \InvalidArgumentException("Expected a value of type \"".$type."\"");
        }
    }

    private static function checkTypeData(array $typeData, $value): bool
    {
        switch ($typeData['name']) {
            case 'boolean':
                $correctType = is_bool($value);
                break;
            case 'string':
            case 'integer':
            case 'double':
            case 'array':
                $checker = 'is_' . $typeData['name'];
                $correctType = $checker($value);
                break;
            default:
                $correctType = $value instanceof $typeData['name'];
                break;
        }

        if (!$correctType) {
            return false;
        }

        if (is_array($value) || $value instanceof \Traversable) {
            if (count($typeData['params']) == 1) {
                foreach ($value as $elem) {
                    if (!self::checkTypeData($typeData['params'][0], $elem)) {
                        return false;
                    }
                }
            } elseif (count($typeData['params']) == 2) {
                foreach ($value as $key => $elem) {
                    if (!self::checkTypeData($typeData['params'][0], $key) || !self::checkTypeData($typeData['params'][1], $elem)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}