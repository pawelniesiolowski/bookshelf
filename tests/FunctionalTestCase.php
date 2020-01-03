<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Catalog\Persistence\Book;
use App\Receiver\Persistence\Receiver;
use App\BookAction\Persistence\BookChangeEvent;

class FunctionalTestCase extends WebTestCase
{
    protected $registry;
    protected $entityManager;

    public function setUp(): void
    {
        self::bootKernel();
        $this->registry = self::$kernel->getContainer()
            ->get('doctrine');

        $this->entityManager = $this->registry->getManager();
        $this->truncateEntities(
            [
                Book::class,
                Receiver::class,
                BookChangeEvent::class,
            ],
            []
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

