<?php

declare(strict_types=1);

namespace Francois\Humanjson\ExpressionPart;

class Criterion implements ExpressionPartI
{
    private bool $isUsed = true;
    public function __construct(
        public string $uuid,
        public string $name
    ){}
    public function getText(): string
    {
        return $this->name;
    }

    public function setIsUsed(bool $isUsed): void
    {
        $this->isUsed = $isUsed;
    }
}