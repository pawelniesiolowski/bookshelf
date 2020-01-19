<?php

namespace App\BookAction\Repository;

use App\BookAction\Persistence\BookChangeEvent;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BookChangeEventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BookChangeEvent::class);
    }

    public function findAllOrderedByDateDesc(): array
    {
        return $this->findBy([], ['date' => 'DESC']);
    }

    public function findAllByBookId(string $id): array
    {
        return $this->findBy(['bookId' => $id]);
    }

    public function findAllByBookIdAfterDate(string $bookId, DateTime $date): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT name, num, book_title, book_author, receiver_name, comment, date
        FROM book_change_event
        WHERE book_id = :bookId AND date > :date
        ORDER BY date DESC';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['bookId' => $bookId, 'date' => $date->format('Y-m-d H:i:s')]);
        $data =  $stmt->fetchAll();
        $preparedData = [];
        foreach ($data as $event) {
            $preparedData[] = new BookChangeEventDTO(
                $event['name'],
                $event['num'],
                $event['book_title'],
                $event['book_author'],
                $event['receiver_name'],
                $event['comment'],
                $event['date']
            );
        }
        return $preparedData;
    }
}
