<?php

declare(strict_types=1);

namespace Francois\Humanjson\ExpressionPart;

class Composition implements ExpressionPartI
{
    private bool $isUsed = false;
    /** @var ExpressionPartI[]  */
    private array $subParts = [];

    public function __construct(
        public string $uuid,
        public string $operator,
    ) {}

    public function addSubPart(ExpressionPartI $subPart): void
    {
        $this->subParts[] = $subPart;
        $subPart->setIsUsed(true);
    }

    public function setIsUsed(bool $isUsed): void
    {
        $this->isUsed = $isUsed;
    }

    /**
     * Produit son texte avec les sous parties et l'operator
     * @return string
     */
    public function getText(): string
    {
        if(count($this->subParts) === 0) return '';

        $subPartTexts = array_map(
            fn(ExpressionPartI $subPart) => $subPart->getText(),
            $this->subParts
        );

        $text = join(
            " {$this->operator} ",
            array_filter($subPartTexts, fn (string $subPartText) => $subPartText !== '')
        );

        if($this->isUsed) {
            $text = '('.$text.')';
        }

        return $text;
    }
}