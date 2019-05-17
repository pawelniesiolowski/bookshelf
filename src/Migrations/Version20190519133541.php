<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190519133541 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE book_change_event (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, receiver_id INT NOT NULL, name VARCHAR(255) NOT NULL, num INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_3E9F7DB216A2B381 (book_id), INDEX IDX_3E9F7DB2CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receiver (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_change_event ADD CONSTRAINT FK_3E9F7DB216A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_change_event ADD CONSTRAINT FK_3E9F7DB2CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES receiver (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_change_event DROP FOREIGN KEY FK_3E9F7DB2CD53EDB6');
        $this->addSql('DROP TABLE book_change_event');
        $this->addSql('DROP TABLE receiver');
    }
}
