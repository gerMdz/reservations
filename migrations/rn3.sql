CREATE TABLE delay_mail (id VARCHAR(36) NOT NULL, email VARCHAR(255) NOT NULL, template VARCHAR(255) NOT NULL, creacion_at DATETIME NOT NULL, prioridad INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
