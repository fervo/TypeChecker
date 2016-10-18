<?php

namespace Fervo\TypeChecker;

function check_type(string $type, $value): bool {
    return TypeChecker::checkType($type, $value);
}