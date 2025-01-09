<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109092845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingredients_food_group (ingredients_id INT NOT NULL, food_group_id INT NOT NULL, INDEX IDX_5AA229B73EC4DCE (ingredients_id), INDEX IDX_5AA229B7D619FE05 (food_group_id), PRIMARY KEY(ingredients_id, food_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ingredients_food_group ADD CONSTRAINT FK_5AA229B73EC4DCE FOREIGN KEY (ingredients_id) REFERENCES ingredients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredients_food_group ADD CONSTRAINT FK_5AA229B7D619FE05 FOREIGN KEY (food_group_id) REFERENCES food_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredients DROP FOREIGN KEY FK_4B60114FD619FE05');
        $this->addSql('DROP INDEX IDX_4B60114FD619FE05 ON ingredients');
        $this->addSql('ALTER TABLE ingredients DROP food_group_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredients_food_group DROP FOREIGN KEY FK_5AA229B73EC4DCE');
        $this->addSql('ALTER TABLE ingredients_food_group DROP FOREIGN KEY FK_5AA229B7D619FE05');
        $this->addSql('DROP TABLE ingredients_food_group');
        $this->addSql('ALTER TABLE ingredients ADD food_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ingredients ADD CONSTRAINT FK_4B60114FD619FE05 FOREIGN KEY (food_group_id) REFERENCES food_group (id)');
        $this->addSql('CREATE INDEX IDX_4B60114FD619FE05 ON ingredients (food_group_id)');
    }
}
