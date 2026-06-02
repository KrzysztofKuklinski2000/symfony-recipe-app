<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260602165240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meal_plan_item (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recipe_id INT NOT NULL, planned_for DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', meal_type VARCHAR(30) NOT NULL, servings INT NOT NULL, INDEX IDX_B4A2EBE7A76ED395 (user_id), INDEX IDX_B4A2EBE759D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meal_plan_item ADD CONSTRAINT FK_B4A2EBE7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meal_plan_item ADD CONSTRAINT FK_B4A2EBE759D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan_item DROP FOREIGN KEY FK_B4A2EBE7A76ED395');
        $this->addSql('ALTER TABLE meal_plan_item DROP FOREIGN KEY FK_B4A2EBE759D8A214');
        $this->addSql('DROP TABLE meal_plan_item');
    }
}
