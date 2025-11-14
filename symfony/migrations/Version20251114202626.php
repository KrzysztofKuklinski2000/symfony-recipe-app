<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251114202626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment_vote (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, comment_id INT NOT NULL, value SMALLINT NOT NULL, INDEX IDX_7C262788F675F31B (author_id), INDEX IDX_7C262788F8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment_vote ADD CONSTRAINT FK_7C262788F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment_vote ADD CONSTRAINT FK_7C262788F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment_vote DROP FOREIGN KEY FK_7C262788F675F31B');
        $this->addSql('ALTER TABLE comment_vote DROP FOREIGN KEY FK_7C262788F8697D13');
        $this->addSql('DROP TABLE comment_vote');
    }
}
