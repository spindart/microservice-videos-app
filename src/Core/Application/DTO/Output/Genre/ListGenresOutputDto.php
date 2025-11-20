<?php

namespace Core\Application\DTO\Output\Genre;

class ListGenresOutputDto
{
    public function __construct(
        public array $items,
        public int $total,
        public int $current_page,
        public int $per_page,
        public int $last_page,
        public int $from,
        public int $to,
        public int $first_page,
        public int $next_page,
        public int $previous_page,
    ) {}
}
