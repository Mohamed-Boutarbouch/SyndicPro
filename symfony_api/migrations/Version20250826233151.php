<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826233151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assessment (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, issued_by_id INT DEFAULT NULL, date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', total_amount NUMERIC(12, 2) NOT NULL, distribution_method VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, issued_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', due_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F7523D704D2A7E12 (building_id), INDEX IDX_F7523D70784BB717 (issued_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assessment_item (id INT AUTO_INCREMENT NOT NULL, assessment_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, amount NUMERIC(12, 2) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8D681812DD3DD5F1 (assessment_id), INDEX IDX_8D681812F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contribution_schedule (id INT AUTO_INCREMENT NOT NULL, regular_contribution_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, frequency VARCHAR(255) NOT NULL, amount_per_payment NUMERIC(12, 2) NOT NULL, next_due_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', is_active TINYINT(1) NOT NULL, changed_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B61AC2AD22DF1E81 (regular_contribution_id), INDEX IDX_B61AC2ADF8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, assessment_item_id INT DEFAULT NULL, contribution_schedule_id INT DEFAULT NULL, recorder_by_id INT DEFAULT NULL, amount NUMERIC(12, 2) NOT NULL, date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', method VARCHAR(255) NOT NULL, reference_number VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, recorded_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6D28840DB891C390 (assessment_item_id), INDEX IDX_6D28840DB34F4703 (contribution_schedule_id), INDEX IDX_6D28840DD4543C8F (recorder_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regular_contribution (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, year INT NOT NULL, total_annual_amount NUMERIC(12, 2) NOT NULL, amount_per_unit NUMERIC(12, 2) NOT NULL, start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_189DCCE64D2A7E12 (building_id), INDEX IDX_189DCCE6B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, approved_by_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, amount NUMERIC(12, 2) NOT NULL, date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', description LONGTEXT DEFAULT NULL, expense_category VARCHAR(255) NOT NULL, vendor VARCHAR(255) DEFAULT NULL, reference_number VARCHAR(255) DEFAULT NULL, payment_method VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, approved_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_723705D14D2A7E12 (building_id), INDEX IDX_723705D1F8BD700D (unit_id), INDEX IDX_723705D12D234F6A (approved_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, floor INT NOT NULL, number VARCHAR(255) NOT NULL, start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DCBB0C534D2A7E12 (building_id), INDEX IDX_DCBB0C53A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, syndic_id INT DEFAULT NULL, building_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, is_active TINYINT(1) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8D93D649F0654A02 (syndic_id), INDEX IDX_8D93D6494D2A7E12 (building_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assessment ADD CONSTRAINT FK_F7523D704D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE assessment ADD CONSTRAINT FK_F7523D70784BB717 FOREIGN KEY (issued_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE assessment_item ADD CONSTRAINT FK_8D681812DD3DD5F1 FOREIGN KEY (assessment_id) REFERENCES assessment (id)');
        $this->addSql('ALTER TABLE assessment_item ADD CONSTRAINT FK_8D681812F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE contribution_schedule ADD CONSTRAINT FK_B61AC2AD22DF1E81 FOREIGN KEY (regular_contribution_id) REFERENCES regular_contribution (id)');
        $this->addSql('ALTER TABLE contribution_schedule ADD CONSTRAINT FK_B61AC2ADF8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DB891C390 FOREIGN KEY (assessment_item_id) REFERENCES assessment_item (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DB34F4703 FOREIGN KEY (contribution_schedule_id) REFERENCES contribution_schedule (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DD4543C8F FOREIGN KEY (recorder_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE regular_contribution ADD CONSTRAINT FK_189DCCE64D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE regular_contribution ADD CONSTRAINT FK_189DCCE6B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D14D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D12D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C534D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE unit ADD CONSTRAINT FK_DCBB0C53A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F0654A02 FOREIGN KEY (syndic_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assessment DROP FOREIGN KEY FK_F7523D704D2A7E12');
        $this->addSql('ALTER TABLE assessment DROP FOREIGN KEY FK_F7523D70784BB717');
        $this->addSql('ALTER TABLE assessment_item DROP FOREIGN KEY FK_8D681812DD3DD5F1');
        $this->addSql('ALTER TABLE assessment_item DROP FOREIGN KEY FK_8D681812F8BD700D');
        $this->addSql('ALTER TABLE contribution_schedule DROP FOREIGN KEY FK_B61AC2AD22DF1E81');
        $this->addSql('ALTER TABLE contribution_schedule DROP FOREIGN KEY FK_B61AC2ADF8BD700D');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DB891C390');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DB34F4703');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DD4543C8F');
        $this->addSql('ALTER TABLE regular_contribution DROP FOREIGN KEY FK_189DCCE64D2A7E12');
        $this->addSql('ALTER TABLE regular_contribution DROP FOREIGN KEY FK_189DCCE6B03A8386');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D14D2A7E12');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F8BD700D');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D12D234F6A');
        $this->addSql('ALTER TABLE unit DROP FOREIGN KEY FK_DCBB0C534D2A7E12');
        $this->addSql('ALTER TABLE unit DROP FOREIGN KEY FK_DCBB0C53A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F0654A02');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494D2A7E12');
        $this->addSql('DROP TABLE assessment');
        $this->addSql('DROP TABLE assessment_item');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE contribution_schedule');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE regular_contribution');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE user');
    }
}
