<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241117123739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE societe ADD company_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE societe ADD CONSTRAINT FK_19653DBDE51E9644 FOREIGN KEY (company_type_id) REFERENCES company_type (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_19653DBDE51E9644 ON societe (company_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE societe DROP FOREIGN KEY FK_19653DBDE51E9644');
        $this->addSql('DROP INDEX UNIQ_19653DBDE51E9644 ON societe');
        $this->addSql('ALTER TABLE societe DROP company_type_id');
    }
}
