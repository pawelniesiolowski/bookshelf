<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200103145650 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE books_authors DROP FOREIGN KEY FK_877EACC2F675F31B');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE books_authors');
        $this->addSql('ALTER TABLE book_change_event MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_3E9F7DB2D17F50A6 ON book_change_event');
        $this->addSql('ALTER TABLE book_change_event DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book_change_event DROP id, DROP book_id, DROP receiver_id');
        $this->addSql('ALTER TABLE book_change_event ADD PRIMARY KEY (uuid)');
        $this->addSql('ALTER TABLE book MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_CBE5A331D17F50A6 ON book');
        $this->addSql('ALTER TABLE book DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book DROP id');
        $this->addSql('ALTER TABLE book ADD PRIMARY KEY (uuid)');
        $this->addSql('ALTER TABLE receiver MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_3DB88C96D17F50A6 ON receiver');
        $this->addSql('ALTER TABLE receiver DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE receiver DROP id');
        $this->addSql('ALTER TABLE receiver ADD PRIMARY KEY (uuid)');
        $this->addSql('ALTER TABLE user MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649D17F50A6 ON user');
        $this->addSql('ALTER TABLE user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user DROP id');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, surname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, uuid CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_BDAFD8C8D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE books_authors (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_877EACC216A2B381 (book_id), INDEX IDX_877EACC2F675F31B (author_id), PRIMARY KEY(book_id, author_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE books_authors ADD CONSTRAINT FK_877EACC216A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_authors ADD CONSTRAINT FK_877EACC2F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book ADD id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBE5A331D17F50A6 ON book (uuid)');
        $this->addSql('ALTER TABLE book ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE book_change_event DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book_change_event ADD id INT AUTO_INCREMENT NOT NULL, ADD book_id INT NOT NULL, ADD receiver_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3E9F7DB2D17F50A6 ON book_change_event (uuid)');
        $this->addSql('ALTER TABLE book_change_event ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE receiver DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE receiver ADD id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3DB88C96D17F50A6 ON receiver (uuid)');
        $this->addSql('ALTER TABLE receiver ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user ADD id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON user (uuid)');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (id)');
    }
}
