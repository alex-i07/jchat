<?php

namespace App\CustomPagination;

use Illuminate\Pagination\LengthAwarePaginator;

class PrettyPaginator extends LengthAwarePaginator
{

    public $roomId;

    /**
     * Create a new pretty paginator instance.
     *
     * PrettyPaginator constructor.
     * @param mixed $items
     * @param int $total
     * @param int $perPage
     * @param null $currentPage
     * @param array $roomId
     */

    public function __construct($items, $total, $perPage, $currentPage = null, $roomId)
    {

        $this->roomId = $roomId;

        parent::__construct($items, $total, $perPage, $currentPage);

    }

    /**
     * Get the pretty URL for a given page number.
     *
     * @param  int  $page
     * @return string
     */
    public function url($page)
    {

        if ($page <= 0) {
            $page = 1;
        }

        $this->path = 'rooms/' . $this->roomId . '/messages/' . $page;

        $this->path .= $this->buildFragment();

        return $this->path;
    }

}