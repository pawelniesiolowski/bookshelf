<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Receiver;
use App\Entity\BookChangeEvent;

class FunctionalTestCase extends WebTestCase
{
    protected $registry;
    protected $entityManager;

    public function setUp()
    {
        self::bootKernel();
        $this->registry = self::$kernel->getContainer()
            ->get('doctrine');

        $this->entityManager = $this->registry->getManager();
        $this->truncateEntities(
            [
                Author::class,
                Book::class,
                Receiver::class,
                BookChangeEvent::class,
            ],
            [
                'books_authors',
            ]
        );
    }

    private function truncateEntities(array $entities, array $joinTables)
    {
        $connection = $this->entityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->entityManager->getClassMetadata($entity)->getTableName()
            );
            $connection->executeUpdate($query);
        }

        foreach ($joinTables as $joinTableName) {
            $query = $databasePlatform->getTruncateTableSQL(
                $joinTableName
            );
            $connection->executeUpdate($query);
        }

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}

