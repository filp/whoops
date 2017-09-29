<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @return Run
     */
    protected function getRunInstance()
    {
        $run = new Run();
        $run->allowQuit(false);

        return $run;
    }

    /**
     * @param  object|string $class_or_object
     * @param  string        $method
     * @param  mixed[]       $args
     * @return mixed
     */
    public static function callPrivateMethod($class_or_object, $method, $args = [])
    {
        $ref = new \ReflectionMethod($class_or_object, $method);
        $ref->setAccessible(true);
        $object = is_object($class_or_object) ? $class_or_object : null;

        return $ref->invokeArgs($object, $args);
    }
}
