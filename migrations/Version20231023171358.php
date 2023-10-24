<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023171358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE mark_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mark (id INT NOT NULL, users_id INT NOT NULL, recipe_id INT NOT NULL, mark INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6674F27167B3B43D ON mark (users_id)');
        $this->addSql('CREATE INDEX IDX_6674F27159D8A214 ON mark (recipe_id)');
        $this->addSql('COMMENT ON COLUMN mark.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F27167B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mark ADD CONSTRAINT FK_6674F27159D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE mark_id_seq CASCADE');
        $this->addSql('ALTER TABLE mark DROP CONSTRAINT FK_6674F27167B3B43D');
        $this->addSql('ALTER TABLE mark DROP CONSTRAINT FK_6674F27159D8A214');
        $this->addSql('DROP TABLE mark');
    }
}
