<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241214113142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA88B137D17F50A6 ON recipe (uuid)');
        $this->addSql('CREATE INDEX IDX_DA88B137B03A8386 ON recipe (created_by_id)');
        $this->addSql('CREATE INDEX IDX_DA88B137896DBBDE ON recipe (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137B03A8386');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137896DBBDE');
        $this->addSql('DROP INDEX UNIQ_DA88B137D17F50A6 ON recipe');
        $this->addSql('DROP INDEX IDX_DA88B137B03A8386 ON recipe');
        $this->addSql('DROP INDEX IDX_DA88B137896DBBDE ON recipe');
        $this->addSql('ALTER TABLE recipe DROP created_by_id, DROP updated_by_id, DROP uuid, DROP created_at, DROP updated_at');
    }
}
