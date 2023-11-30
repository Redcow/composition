<?php

declare(strict_types=1);

namespace Francois\Humanjson\ExpressionPart;

/**
 * Inspirée du composite pattern
 * https://refactoring.guru/fr/design-patterns/composite
 * Qu'un objet Composition ou un objet Criterion soit utilisé
 * Le comportement pour fournir le texte de ce 'node' reste le même
 */
interface ExpressionPartI
{
    public function setIsUsed(bool $isUsed): void;
    public function getText(): string;

}