<?php

declare(strict_types=1);

namespace Onepix\WpStaticAnalysis\Tests\Util;

use ReflectionException;
use ReflectionMethod;

trait ExposeProtectedMethods
{
    /**
     * @param object $object
     * @param string $methodName
     * @param array $args
     * @return mixed
     * @throws ReflectionException
     */
    public function callProtectedMethod(
        object $object,
        string $methodName,
        array $args = []
    ): mixed {

        $method = new ReflectionMethod($object, $methodName);

        return $method->invokeArgs($object, $args);
    }
}
