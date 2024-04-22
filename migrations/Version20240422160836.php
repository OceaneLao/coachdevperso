<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422160836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844A76ED395');
        $this->addSql('DROP INDEX IDX_FE38F844A76ED395 ON appointment');
        $this->addSql('ALTER TABLE appointment ADD started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD ended_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP user_id, DROP start_at, DROP end_at, CHANGE permission status TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment ADD user_id INT NOT NULL, ADD start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP started_at, DROP ended_at, CHANGE status permission TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FE38F844A76ED395 ON appointment (user_id)');
    }
}
