<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210325221820 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign ADD user_id INT NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1F1512DDA76ED395 ON campaign (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DDA76ED395');
        $this->addSql('DROP INDEX IDX_1F1512DDA76ED395 ON campaign');
        $this->addSql('ALTER TABLE campaign DROP user_id, DROP name, DROP active');
    }
}
