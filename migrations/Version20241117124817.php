<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241117124817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE societe_company_type (societe_id INT NOT NULL, company_type_id INT NOT NULL, INDEX IDX_569209AFCF77503 (societe_id), INDEX IDX_569209AE51E9644 (company_type_id), PRIMARY KEY(societe_id, company_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE societe_company_type ADD CONSTRAINT FK_569209AFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE societe_company_type ADD CONSTRAINT FK_569209AE51E9644 FOREIGN KEY (company_type_id) REFERENCES company_type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE societe_company_type DROP FOREIGN KEY FK_569209AFCF77503');
        $this->addSql('ALTER TABLE societe_company_type DROP FOREIGN KEY FK_569209AE51E9644');
        $this->addSql('DROP TABLE societe_company_type');
    }
}
