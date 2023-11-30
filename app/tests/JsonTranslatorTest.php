<?php

declare(strict_types=1);

namespace Test;

use Francois\Humanjson\exceptions\UndefinedIdentifier;
use PHPUnit\Framework\TestCase;

use Francois\Humanjson\exceptions\CircularJSONException;
use Francois\Humanjson\HumanTranslator;

final class JsonTranslatorTest extends TestCase
{
    public function testCircularJSON(): void
    {
        $translator = new HumanTranslator();

        $this->expectException(CircularJSONException::class);
        $translator->execute(__DIR__.'/jsonFiles/circularComposition.json');
    }

    public function testSnapShot(): void
    {
        $translator = new HumanTranslator();

        $expression = $translator->execute(__DIR__.'/jsonFiles/validSnapshot.json');

        $this->assertSame(
            '(critère_4 OR (critère_5 AND critère_6 AND critère_7) OR critère_3) AND critère_2 AND critère_1',
            $expression
        );
    }

    public function testFindInvalidIdentifier(): void
    {
        $translator = new HumanTranslator();

        $this->expectException(UndefinedIdentifier::class);
        $translator->execute(__DIR__.'/jsonFiles/invalidIdentifier.json');
    }
}