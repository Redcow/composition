<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Francois\Humanjson\HumanTranslator;

$translator = new HumanTranslator();

$expression = $translator->execute(__DIR__.'/../file/expression.json');

echo $expression;