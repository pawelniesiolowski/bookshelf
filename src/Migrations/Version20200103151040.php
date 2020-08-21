<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200103151040 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_change_event DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book_change_event CHANGE uuid id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE book_uuid book_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',  CHANGE receiver_uuid receiver_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE book_change_event ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE book DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book CHANGE uuid id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE authors_names authors VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE receiver DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE receiver CHANGE uuid id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE receiver ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user CHANGE uuid id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book CHANGE id uuid CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE authors authors_names VARCHAR(500) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE book ADD PRIMARY KEY (uuid)');
        $this->addSql('ALTER TABLE book_change_event DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book_change_event ADD uuid CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', ADD book_uuid CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', DROP id, DROP book_id, CHANGE receiver_id receiver_uuid CHAR(36) DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE book_change_event ADD PRIMARY KEY (uuid)');
        $this->addSql('ALTER TABLE receiver DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE receiver CHANGE id uuid CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE receiver ADD PRIMARY KEY (uuid)');
        $this->addSql('ALTER TABLE user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user CHANGE id uuid CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (uuid)');
    }
}
