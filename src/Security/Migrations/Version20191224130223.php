<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191224130223 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE author SET uuid = UUID()');
        $this->addSql('UPDATE book SET uuid = UUID()');
        $this->addSql('UPDATE receiver SET uuid = UUID()');
        $this->addSql('UPDATE user SET uuid = UUID()');
        $this->addSql('UPDATE book_change_event SET uuid = UUID()');
    }

    public function down(Schema $schema): void
    {
    }
}
