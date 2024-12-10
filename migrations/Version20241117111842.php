<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241117111842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE associate_associate DROP FOREIGN KEY FK_C3B7ADFC8E3A2C6F');
        $this->addSql('ALTER TABLE associate_associate DROP FOREIGN KEY FK_C3B7ADFC97DF7CE0');
        $this->addSql('DROP TABLE associate_associate');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE associate_associate (associate_source INT NOT NULL, associate_target INT NOT NULL, INDEX IDX_C3B7ADFC97DF7CE0 (associate_source), INDEX IDX_C3B7ADFC8E3A2C6F (associate_target), PRIMARY KEY(associate_source, associate_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE associate_associate ADD CONSTRAINT FK_C3B7ADFC8E3A2C6F FOREIGN KEY (associate_target) REFERENCES associate (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE associate_associate ADD CONSTRAINT FK_C3B7ADFC97DF7CE0 FOREIGN KEY (associate_source) REFERENCES associate (id) ON DELETE CASCADE');
    }
}
