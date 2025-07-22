<?php

namespace App\Traits;

trait Pagination
{
    public function formatPagination($paginated)
    {
        return [
            'Next' => $paginated->nextPageUrl(),
            'Previous' => $paginated->previousPageUrl(),
        ];
    }
}
