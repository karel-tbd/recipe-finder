<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103130114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE time time DOUBLE PRECISION DEFAULT NULL, CHANGE instructions instructions LONGTEXT DEFAULT NULL, CHANGE people people INT DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_recipe_rating DROP FOREIGN KEY FK_BB19955359D8A214');
        $this->addSql('ALTER TABLE user_recipe_rating CHANGE recipe_id recipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_recipe_rating ADD CONSTRAINT FK_BB19955359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe CHANGE name name VARCHAR(255) NOT NULL, CHANGE time time DOUBLE PRECISION NOT NULL, CHANGE instructions instructions LONGTEXT NOT NULL, CHANGE people people INT NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_recipe_rating DROP FOREIGN KEY FK_BB19955359D8A214');
        $this->addSql('ALTER TABLE user_recipe_rating CHANGE recipe_id recipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_recipe_rating ADD CONSTRAINT FK_BB19955359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
    }
}
