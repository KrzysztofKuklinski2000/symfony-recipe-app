<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260528153451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recipe_rating (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, recipe_id INT NOT NULL, score INT NOT NULL, INDEX IDX_55973803F675F31B (author_id), INDEX IDX_5597380359D8A214 (recipe_id), UNIQUE INDEX unique_user_recipe_rating (author_id, recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe_rating ADD CONSTRAINT FK_55973803F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_rating ADD CONSTRAINT FK_5597380359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_rating DROP FOREIGN KEY FK_55973803F675F31B');
        $this->addSql('ALTER TABLE recipe_rating DROP FOREIGN KEY FK_5597380359D8A214');
        $this->addSql('DROP TABLE recipe_rating');
    }
}
