<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class Version20200224074837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates initial DB schema';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'CREATE TABLE user (
                 id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\',
                 email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
                 roles JSON DEFAULT NULL,
                 password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
                 salt VARCHAR(40) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`,
                 UNIQUE INDEX `user__unique_email` (email),
                 PRIMARY KEY(id)
             ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE user');
    }
}
