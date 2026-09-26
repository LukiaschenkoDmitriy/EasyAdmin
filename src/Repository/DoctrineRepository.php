<?php declare(strict_types=1);

namespace EAdmin\Core\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use Doctrine\ORM\Tools\Pagination\Window;

class DoctrineRepository implements RepositoryInterface {
    public function __construct(private EntityManagerInterface $manager) { }
    public function get(string $entityClass, RepositoryContext $context, array $sortingFields, array $searchFields): RepositoryResult
    {
        $repository = $this->manager->getRepository($entityClass);
        $builder = $repository->createQueryBuilder('e');

        $allowedOrders = ['ASC', 'DESC'];

        if ($context->searchBy !== null && $context->search) {
            if (!\is_array($searchFields) || !\in_array($context->searchBy, $searchFields, true)) {
                throw new \InvalidArgumentException(sprintf('Field "%s" is not allowed for search.', $context->searchBy));
            }

            $builder->andWhere($builder->expr()->like('e.' . $context->searchBy, ':search'))->setParameter('search', '%' . $context->search . '%');
        }

        if ($context->sortingBy !== null && $context->order !== null && $sortingFields !== null) {
            $sortField = \in_array($context->sortingBy, $sortingFields, true) ? $context->sortingBy : 'id';
            $order = \in_array(strtoupper($context->order), $allowedOrders, true) ? strtoupper($context->order) : 'ASC';
            $builder->orderBy('e.' . $sortField, $order);
        } else {
            $builder->orderBy('e.id', 'ASC');
        }

        $window = Window::fromPageNumberAndSize($context->page, $context->limit);
        $windowPage = (new OffsetPaginator())->paginate($builder->getQuery(), $window);

        return new RepositoryResult(
            iterator_to_array($windowPage),
            new RepositoryPagination(
                $windowPage->getTotalCount(),
                $windowPage->getPageNumber(),
                $windowPage->getPageCount(),
                $windowPage->hasNextPage()
            )
        );
    }
}