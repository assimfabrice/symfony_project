<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241117181749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_societe DROP FOREIGN KEY FK_BD0A9B61FCF77503');
        $this->addSql('ALTER TABLE document_societe DROP FOREIGN KEY FK_BD0A9B61C33F7837');
        $this->addSql('DROP TABLE document_societe');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_societe (document_id INT NOT NULL, societe_id INT NOT NULL, INDEX IDX_BD0A9B61C33F7837 (document_id), INDEX IDX_BD0A9B61FCF77503 (societe_id), PRIMARY KEY(document_id, societe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE document_societe ADD CONSTRAINT FK_BD0A9B61FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document_societe ADD CONSTRAINT FK_BD0A9B61C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
    }
}
