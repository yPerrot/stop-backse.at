<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302231153 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create user table that store twitch accounts';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, twitch_id VARCHAR(180) NOT NULL, username VARCHAR(255) NOT NULL, displayed_username VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, twitch_token VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', understood TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649DA4E964C (twitch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `user`');
    }

    // Fix: https://github.com/doctrine/migrations/issues/1104
    public function isTransactional(): bool
    {
        return false;
    }
}
