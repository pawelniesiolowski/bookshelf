<?php

namespace App\BookAction\Infrastructure\Repository;

use App\BookAction\Domain\BookChangeEvent;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookChangeEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookChangeEvent::class);
    }

    /**
     * @param string $id
     * @return BookChangeEvent[]
     */
    public function findAllByBookId(string $id): array
    {
        return $this->findBy(['bookId' => $id], ['date' => 'ASC']);
    }

    public function findAllByBookIdAfterDate(string $bookId, DateTime $date): array
    {
        $sql = <<<EOD
            SELECT e FROM App\BookAction\Domain\BookChangeEvent e
            WHERE e.bookId = :bookId AND e.date >= :date
            ORDER BY e.date
EOD;
        return $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter(':bookId', $bookId)
            ->setParameter(':date', $date)
            ->getResult();
    }
}
