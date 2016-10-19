<?php

namespace Fervo\TypeChecker;

function check_type(string $type, $value): bool {
    return TypeChecker::checkType($type, $value);
}

function assert_type(string $type, $value) {
    TypeChecker::assertType($type, $value);
}
