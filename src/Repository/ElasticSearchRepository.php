<?php declare(strict_types=1);

namespace EAdmin\Core\Repository;

use EAdmin\Core\ElasticSearch\Attribute\SearchableEntity;
use EAdmin\Core\ElasticSearch\Attribute\SearchField;
use EAdmin\Core\ElasticSearch\ElasticSearchFactory;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchAll;
use Elastica\Query\MultiMatch;
use Elastica\Query\Term;
use Elastica\Query\Wildcard;
use Elastica\Result;
use ReflectionClass;

class ElasticSearchRepository implements RepositoryInterface {
    private const ALLOWED_ORDERS = ['ASC' => 'asc', 'DESC' => 'desc'];
    public function __construct(private ElasticSearchFactory $factory) { }

    public function get(string $entityClass, RepositoryContext $context, array $sortingFields, array $searchFields): RepositoryResult
    {
        $indexName = $this->getIndexByClass($entityClass);

        if ($indexName === null) {
            throw new \Exception('Index not exist try add attribute SearchableEntity to Entity and run command eadmin:elastic:index');
        }

        $index = $this->factory->create($indexName);

        if (!$index) {
            throw new \Exception(sprintf('Elasticsearch index "%s" does not exist. Run eadmin:elastic:index.', $indexName));
        }

        $boolQuery = new BoolQuery();

        if ($context->search) {
            [$textFields, $keywordFields, $numericFields] = $this->splitFieldsByType($entityClass, $searchFields);

            $shouldQuery = new BoolQuery();
            $hasAnyClause = false;

            if (!empty($textFields)) {
                $shouldQuery->addShould($this->buildTextSearchQuery($textFields, $context->search));
                $hasAnyClause = true;
            }

            if (!empty($keywordFields)) {
                $shouldQuery->addShould($this->buildKeywordSearchQuery($keywordFields, $context->search));
                $hasAnyClause = true;
            }

            foreach ($numericFields as $field) {
                if (\ctype_digit($context->search)) {
                    $shouldQuery->addShould(new Term([$field => (int) $context->search]));
                    $hasAnyClause = true;
                }
            }

            $shouldQuery->setMinimumShouldMatch(1);

            $boolQuery->addMust($hasAnyClause ? $shouldQuery : new Term(['_index' => '__no_match__']));
        } else {
            $boolQuery->addMust(new MatchAll());
        }

        $query = new Query($boolQuery);

        if ($context->sortingBy !== null && $context->order !== null
            && \in_array($context->sortingBy, $sortingFields, true)
        ) {
            $order = self::ALLOWED_ORDERS[strtoupper($context->order)] ?? 'asc';
            $query->setSort([$context->sortingBy => ['order' => $order]]);
        } elseif ($context->searchBy === null) {
            $query->setSort(['id' => ['order' => 'asc']]);
        }

        $query->setFrom(($context->page - 1) * $context->limit);
        $query->setSize($context->limit);
        $query->setTrackTotalHits(true);

        $resultSet = $index->search($query);

        $items = array_map(
            static fn (Result $result) => $result->getData() + ['id' => $result->getId()],
            $resultSet->getResults()
        );

        $totalCount = $resultSet->getTotalHits();
        $pageCount = max(1, (int) ceil($totalCount / $context->limit));

        return new RepositoryResult(
            $items,
            new RepositoryPagination(
                $totalCount,
                $context->page,
                $pageCount,
                $context->page < $pageCount,
            )
        );
    }

    private function getIndexByClass(string $entityClass): ?string
    {
        $reflection = new ReflectionClass(($entityClass));
        $attributes = $reflection->getAttributes(SearchableEntity::class);

        if (empty($attributes)) return null;


        /**
         * @var SearchableEntity $attribute
         */
        $attribute = $attributes[0]->newInstance();

        return $attribute->indexName;
    }

    private function splitFieldsByType(string $entityClass, array $searchFields): array
    {
        $reflection = new \ReflectionClass($entityClass);
        $textFields = [];
        $keywordFields = [];
        $numericFields = [];

        foreach ($reflection->getProperties() as $property) {
            if (!\in_array($property->getName(), $searchFields, true)) {
                continue;
            }

            $attrs = $property->getAttributes(SearchField::class);
            if (empty($attrs)) {
                continue;
            }

            /** @var SearchField $field */
            $field = $attrs[0]->newInstance();

            match ($field->type) {
                'text' => $textFields[] = $property->getName(),
                'keyword' => $keywordFields[] = $property->getName(),
                'integer', 'long', 'short', 'byte', 'float', 'double' => $numericFields[] = $property->getName(),
                default => null,
            };
        }

        return [$textFields, $keywordFields, $numericFields];
    }

    private function buildTextSearchQuery(array $textFields, string $search): BoolQuery
    {
        $shouldQuery = new BoolQuery();

        $fuzzyMatch = new MultiMatch();
        $fuzzyMatch->setQuery($search)->setFields($textFields)->setFuzziness('AUTO');
        $shouldQuery->addShould($fuzzyMatch);

        $prefixMatch = new MultiMatch();
        $prefixMatch->setQuery($search)->setFields($textFields)->setType(MultiMatch::TYPE_PHRASE_PREFIX);
        $shouldQuery->addShould($prefixMatch);

        $shouldQuery->setMinimumShouldMatch(1);

        return $shouldQuery;
    }

    private function buildKeywordSearchQuery(array $keywordFields, string $search): BoolQuery
    {
        $shouldQuery = new BoolQuery();

        foreach ($keywordFields as $field) {
            $shouldQuery->addShould(new Wildcard($field, '*' . strtolower($search) . '*'));
        }

        return $shouldQuery;
    }
}