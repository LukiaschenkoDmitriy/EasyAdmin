<?php declare(strict_types=1);

namespace EAdmin\Core\Repository;

use EAdmin\Core\ElasticSearch\Attribute\SearchableEntity;
use EAdmin\Core\ElasticSearch\ElasticSearchFactory;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchAll;
use Elastica\Query\MatchQuery;
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

        if ($context->searchBy !== null && $context->search) {
            if (!\in_array($context->searchBy, $searchFields, true)) {
                throw new \InvalidArgumentException(sprintf('Field "%s" is not allowed for search.', $context->searchBy));
            }

            $boolQuery->addMust(
                (new MatchQuery())->setFieldQuery($context->searchBy, $context->search)->setFieldFuzziness($context->searchBy, 'AUTO')
            );
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
            $query->setSort(['_id' => ['order' => 'asc']]);
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
}