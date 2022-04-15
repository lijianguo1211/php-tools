<?php

namespace Jay\List;

/**
 * @Notes:
 *
 * @File Name: Node.php
 * @Date: 2022/4/15
 * @Created By: Jay.Li
 */
class Node
{
    /**
     * @var  Node|null
     */
    public ?Node $next = null;

    /**
     * @var  Node|null
     */
    public ?Node $prev = null;

    /**
     * @var
     */
    protected $item;

    public function __construct(?Node $prev, $element, ?Node $next)
    {
        $this->item = $element;

        $this->prev = $prev;

        $this->next = $next;
    }

    public function getItem()
    {
        return $this->item;
    }
}