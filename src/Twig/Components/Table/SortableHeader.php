<?php

namespace App\Twig\Components\Table;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class SortableHeader
{
    public string $label;
    public string $sort;
    public SlidingPagination $pagination;
}
