<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218121926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_ingredients ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, CHANGE unit unit VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe_ingredients ADD CONSTRAINT FK_9F925F2BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_ingredients ADD CONSTRAINT FK_9F925F2B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F925F2BD17F50A6 ON recipe_ingredients (uuid)');
        $this->addSql('CREATE INDEX IDX_9F925F2BB03A8386 ON recipe_ingredients (created_by_id)');
        $this->addSql('CREATE INDEX IDX_9F925F2B896DBBDE ON recipe_ingredients (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_ingredients DROP FOREIGN KEY FK_9F925F2BB03A8386');
        $this->addSql('ALTER TABLE recipe_ingredients DROP FOREIGN KEY FK_9F925F2B896DBBDE');
        $this->addSql('DROP INDEX UNIQ_9F925F2BD17F50A6 ON recipe_ingredients');
        $this->addSql('DROP INDEX IDX_9F925F2BB03A8386 ON recipe_ingredients');
        $this->addSql('DROP INDEX IDX_9F925F2B896DBBDE ON recipe_ingredients');
        $this->addSql('ALTER TABLE recipe_ingredients DROP created_by_id, DROP updated_by_id, DROP uuid, DROP created_at, DROP updated_at, CHANGE unit unit VARCHAR(255) NOT NULL');
    }
}
