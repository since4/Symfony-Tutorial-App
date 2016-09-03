<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160902173420 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE genus_note (id INT AUTO_INCREMENT NOT NULL, genus_id INT NOT NULL, username VARCHAR(255) NOT NULL, user_avatar_filename VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_6478FCEC85C4074C (genus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE genus_note ADD CONSTRAINT FK_6478FCEC85C4074C FOREIGN KEY (genus_id) REFERENCES genus (id)');
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE genus_note');
        $this->addSql('ALTER TABLE user DROP roles');
    }
}
