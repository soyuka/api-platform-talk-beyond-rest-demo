<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\AbstractSearchFilter;

/**
 * Adds fuzziness compared to the default match filter.
 */
final class MatchFilter extends AbstractSearchFilter
{
    /**
     * {@inheritdoc}
     */
    protected function getQuery(string $property, array $values, ?string $nestedPath): array
    {
        $matches = [];

        foreach ($values as $value) {
            $matches[] = ['match' => [$property => ['query' => $value, 'fuzziness' => 'AUTO']]];
        }

        $matchQuery = isset($matches[1]) ? ['bool' => ['should' => $matches]] : $matches[0];

        if (null !== $nestedPath) {
            $matchQuery = ['nested' => ['path' => $nestedPath, 'query' => $matchQuery]];
        }

        return $matchQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(array $clauseBody, string $resourceClass, ?string $operationName = null, array $context = []): array
    {
        $data = parent::apply($clauseBody, $resourceClass, $operationName, $context);

        if (isset($data['bool']['must'])) {
            $data['bool']['should'] = $data['bool']['must'];
            unset($data['bool']['must']);
        }

        return $data;
    }
}
