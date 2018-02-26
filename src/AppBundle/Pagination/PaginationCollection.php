<?php
/**
 * Created by PhpStorm.
 * User: challouf
 * Date: 16/09/17
 * Time: 12:29
 */

namespace AppBundle\Pagination;


class PaginationCollection
{

    private $items;
    private $total;
    private $count;
    private $_links = array();

    public function __construct(array $items, $totalItems)
    {
        $this->items = $items;
        $this->total = $totalItems;
        $this->count = count($items);

    }

    public function addLink($ref, $url)
    {
        $this->_links[$ref] = $url;

    }

}