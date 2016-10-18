<?php


namespace Fervo\TypeChecker;


class TypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function getData(): array
    {
        return [
            ['string', 'foobar', true],
            ['string', 123, false],
            ['integer', 123, true],
            ['integer', "foobar", false],
            ['integer', "123", false],
            ['boolean', true, true],
            ['boolean', false, true],
            ['boolean', 1, false],
            ['boolean', "true", false],
            ['boolean', 0, false],
            ['double', 123.0, true],
            ['double', 123, false],
            ['double', "foobar", false],
            ['double', "123.1", false],
            ['DateTime', new \DateTime, true],
            ['Fervo\\TypeChecker\\TypeCheckerTest', $this, true],
            ['array', ['foo', 'bar'], true],
            ['array', "foo", false],
            ['array<string>', ['foo', 'bar'], true],
            ['array<integer>', ['foo', 'bar'], false],
            ['array<string>', ['foo', 1], false],
            ['array<string>', [['foo'], 'bar'], false],
            ['array<string>', [0 => 'foo', 5 => 'bar'], true],
            ['array<string>', ['quux' => 'foo', 'xyzzy' => 'bar'], true],
            ['array<integer, string>', ['foo', 'bar'], true],
            ['array<integer, string>', [0 => 'foo', 5 => 'bar'], true],
            ['array<integer, string>', ['quux' => 'foo', 'xyzzy' => 'bar'], false],
            ['array<string, string>', ['quux' => 'foo', 'xyzzy' => 'bar'], true],
            ['array<string, string>', ['foo', 'bar'], false],
            ['array<array<string>>', [['foo', 'baz'], ['bar']], true],
            ['array<array<string>>', [['foo', 'baz'], 'bar'], false],
        ];
    }

    public function getExceptionThrowers(): array
    {
        return [
            ['\\DateTime', new \DateTime, 'JMS\Parser\SyntaxErrorException'],
            ['\\Fervo\\TypeChecker\\TypeCheckerTest', $this, 'JMS\Parser\SyntaxErrorException'],
        ];
    }

    /**
     * @dataProvider getData
     */
    public function testTypeChecker(string $type, $value, bool $return)
    {
        $this->assertTrue(TypeChecker::checkType($type, $value) === $return);
    }

    /**
     * @dataProvider getExceptionThrowers
     */
    public function testExceptionThrowers($type, $value, $exceptionClass)
    {
        try {
            TypeChecker::checkType($type, $value);
        } catch (\Exception $e) {
            $this->assertInstanceOf($exceptionClass, $e);
            return;
        }

        $this->fail("Did not throw exception");
    }
}
