<?php

declare(strict_types=1);

namespace Francois\Humanjson;

use Francois\Humanjson\exceptions\CircularJSONException;
use Francois\Humanjson\exceptions\UndefinedIdentifier;
use Francois\Humanjson\ExpressionPart\Composition;
use Francois\Humanjson\ExpressionPart\Criterion;
use Francois\Humanjson\ExpressionPart\ExpressionPartI;

class PartProvider
{
    // constantes, mort aux magic strings!
    private const CRITERIA = 'criteria';
    private const COMPOSITION = 'composition';
    private const UUID = 'uuid';
    private const NAME = 'name';
    private const IDENTIFIERS = 'identifiers';
    private const TYPE = 'type';
    private const OPERATOR = 'operator';
    private const CRITERIA_TYPE = 'CRITERIA';
    private const COMPOSITION_TYPE = 'REFERENCE';


    private array $jsonSource;
    private array $partList = [];
    private string $rootId;

    public function mapParts(array $json): void
    {
        $this->jsonSource = $json;

        $rootComposition = $this->findRoot($json[self::COMPOSITION]);
        $this->rootId = $rootComposition[self::UUID];

        $this->mapCriteriaList($json[self::CRITERIA]);

        $this->mapCompositionsFromRoot($this->rootId);
    }

    /**
     * Produit le texte en partant de l'expressionPart root
     * @return Composition
     */
    public function getRootComposition(): Composition
    {
        return $this->partList[self::COMPOSITION_TYPE][$this->rootId];
    }

    /**
     * Trouve la composition root
     * @param array $compositionList
     * @return array
     * @throws CircularJSONException
     */
    private function findRoot(array $compositionList): array
    {
        $subPartIds = [];
        foreach ($compositionList as $composition) {
            foreach ($composition[self::IDENTIFIERS] as $identifier) {
                if ($identifier[self::TYPE] !== self::COMPOSITION_TYPE) continue;
                $subPartIds[] = $identifier[self::UUID];
            }
        }

        foreach ($compositionList as $composition)
        {
            if(!in_array($composition[self::UUID], $subPartIds)) {
                return $composition;
            }
        }

        throw new CircularJSONException('Probably circular reference in your JSON');
    }

    /**
     * Stocke l'ensemble des critères disponibles pour les compositions
     * @param array $criterionList
     * @return void
     */
    private function mapCriteriaList(array $criterionList): void
    {
        $this->partList[self::CRITERIA_TYPE] = array_reduce(
            $criterionList,
            function ($result, $criterion) {
                $result[$criterion[self::UUID]] = new Criterion(
                    uuid: $criterion[self::UUID],
                    name: $criterion[self::NAME]
                );
                return $result;
            }, []
        );
    }

    /**
     * Démarre le mapping en objet Composition en commençant par le Root
     * @param string $rootId
     * @return void
     * @throws UndefinedIdentifier
     */
    private function mapCompositionsFromRoot(string $rootId): void
    {
        $this->partList[self::COMPOSITION_TYPE][$rootId] = $this->get([
            self::UUID => $rootId,
            self::TYPE => self::COMPOSITION_TYPE
        ]);
    }

    /**
     * Récupère une expressionPart en la cherchant dans la liste, sinon la résout
     * Inspiré de l'autowiring dans un service container
     * @param array<string, string> $identifier
     * @return ExpressionPartI
     * @throws UndefinedIdentifier
     */
    public function get(array $identifier): ExpressionPartI
    {
        $id = $identifier[self::UUID];
        $type = $identifier[self::TYPE];

        if($this->has($id, $type)) {
            return $this->partList[$type][$id];
        }

        return $this->resolve($identifier);
    }

    /**
     * Détermine si l'expressionPart existe déjà
     * @param string $id
     * @param string $type
     * @return bool
     */
    public function has(string $id, string $type): bool
    {
        return isset($this->partList[$type][$id]);
    }

    /**
     * Résout l'expressionPart demandée, recursion sur ses sous parties
     * @param array $identifier
     * @return ExpressionPartI|null
     * @throws UndefinedIdentifier
     */
    public function resolve(array $identifier): ?ExpressionPartI
    {
        $resolvedReference = null;

        foreach ($this->jsonSource[self::COMPOSITION] as $jsonComposition )
        {
            if($jsonComposition[self::UUID] === $identifier[self::UUID]) {

                $subParts = array_map(
                    fn (array $identifier) => $this->get($identifier),
                    $jsonComposition[self::IDENTIFIERS]
                );

                $resolvedReference = new Composition(
                    uuid: $jsonComposition[self::UUID],
                    operator: $jsonComposition[self::OPERATOR],
                );

                foreach ($subParts as $subPart) {
                    $resolvedReference->addSubPart($subPart);
                }

                break;
            }
        }

        if ($resolvedReference) {
            $this->partList[self::COMPOSITION_TYPE][$identifier[self::UUID]] = $resolvedReference;
            return $resolvedReference;
        }

        throw new UndefinedIdentifier();
    }
}