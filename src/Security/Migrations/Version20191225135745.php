<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191225135745 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_change_event DROP FOREIGN KEY FK_3E9F7DB216A2B381');
        $this->addSql('ALTER TABLE book_change_event DROP FOREIGN KEY FK_3E9F7DB2CD53EDB6');
        $this->addSql('DROP INDEX IDX_3E9F7DB2CD53EDB6 ON book_change_event');
        $this->addSql('DROP INDEX IDX_3E9F7DB216A2B381 ON book_change_event');
        $this->addSql('ALTER TABLE book_change_event ADD book_title VARCHAR(255) DEFAULT NULL, ADD receiver_name VARCHAR(510) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_change_event DROP book_title, DROP receiver_name');
        $this->addSql('ALTER TABLE book_change_event ADD CONSTRAINT FK_3E9F7DB216A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_change_event ADD CONSTRAINT FK_3E9F7DB2CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES receiver (id)');
        $this->addSql('CREATE INDEX IDX_3E9F7DB2CD53EDB6 ON book_change_event (receiver_id)');
        $this->addSql('CREATE INDEX IDX_3E9F7DB216A2B381 ON book_change_event (book_id)');
    }
}
