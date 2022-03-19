<?php

/**
 * @Notes: 单例模式
 *
 * @File Name: singleton.md.php
 * @Date: 2022/3/19
 * @Created By: Jay.Li
 */

class Singleton
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {

    }


    /**
     * @throws Exception
     */
    public function __wakeup()
    {
//        throw new \Exception("Cannot unserialize singleton");
        return new static();
    }


    final public function __invoke(): void
    {
        // TODO: Implement __invoke() method.
    }


    public static function getInstance():self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}

$obj = Singleton::getInstance();
$obj2 = Singleton::getInstance();

$obj4 = serialize($obj);

$obj4 = unserialize($obj4);

var_dump($obj4 === $obj);

//$obj1 = clone $obj;

$obj3 = $obj();
var_dump($obj3 === $obj);

//var_dump($obj === $obj1);
var_dump($obj === $obj2);
