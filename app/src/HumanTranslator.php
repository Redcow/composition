<?php

declare(strict_types=1);

namespace Francois\Humanjson;

class HumanTranslator
{
    private PartProvider $provider;

    public function __construct () {
        $this->provider = new PartProvider();
    }

    public function execute(string $jsonFilePath): string
    {
        $json = $this->loadJSONFile($jsonFilePath);

        $this->provider->mapParts($json);

        return $this->provider->getRootComposition()->getText();
    }

    /**
     * Charge le fichier JSON
     * @param string $jsonFilePath
     * @return array
     */
    private function loadJSONFile(string $jsonFilePath): array
    {
        $json = file_get_contents($jsonFilePath);

        return json_decode($json, true);
    }

}