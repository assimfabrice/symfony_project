<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241118131131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76E51E9644');
        $this->addSql('DROP INDEX IDX_D8698A76E51E9644 ON document');
        $this->addSql('ALTER TABLE document DROP company_type_id, DROP fields, DROP paragraphe');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document ADD company_type_id INT DEFAULT NULL, ADD fields JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD paragraphe LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76E51E9644 FOREIGN KEY (company_type_id) REFERENCES company_type (id)');
        $this->addSql('CREATE INDEX IDX_D8698A76E51E9644 ON document (company_type_id)');
    }
}
