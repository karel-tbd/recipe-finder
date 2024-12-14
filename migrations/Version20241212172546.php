<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212172546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredients ADD food_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ingredients ADD CONSTRAINT FK_4B60114FD619FE05 FOREIGN KEY (food_group_id) REFERENCES food_group (id)');
        $this->addSql('CREATE INDEX IDX_4B60114FD619FE05 ON ingredients (food_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredients DROP FOREIGN KEY FK_4B60114FD619FE05');
        $this->addSql('DROP INDEX IDX_4B60114FD619FE05 ON ingredients');
        $this->addSql('ALTER TABLE ingredients DROP food_group_id');
    }
}
