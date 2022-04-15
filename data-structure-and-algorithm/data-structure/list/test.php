<?php
/**
 * @Notes:
 *
 * @File Name: test.php
 * @Date: 2022/4/15
 * @Created By: Jay.Li
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . "vendor/autoload.php";

use Jay\List\LinkedList;

$linkedList = new LinkedList();

$linkedList->add(1);
$linkedList->add("ABC");
$linkedList->add(["A" => "C", "D" => ["E" => "F"]]);
$stdClass = new \stdClass();
$stdClass->id = 1;
$stdClass->name = 'test';
$stdClass->time = time();
$linkedList->add($stdClass);
$linkedList->add(function () {
    return 'callback~';
});
$linkedList->addFirst('A|1|一|one');

echo "linked list size = (" . $linkedList->size() . ")\n";


$node = $linkedList->getFirst();
while (true) {

    if ($node == null) {
        break;
    }

    print_r($node->getItem());

    $node = $node->next;
}

//var_dump($linkedList->removeFirst());
//var_dump($linkedList->removeLast());
//var_dump($linkedList->removeFirst());
//var_dump($linkedList->removeFirst());
//var_dump($linkedList->removeFirst());
$cb = $linkedList->removeIndex(5);
var_dump($cb());