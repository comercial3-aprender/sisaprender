CREATE DATABASE IF NOT EXISTS sisaprender
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sisaprender;

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS municipalities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    state CHAR(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    municipality_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    inep_code VARCHAR(20),
    CONSTRAINT fk_schools_municipality
        FOREIGN KEY (municipality_id)
        REFERENCES municipalities(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    municipality_id INT,
    school_id INT,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_users_municipality
        FOREIGN KEY (municipality_id)
        REFERENCES municipalities(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_users_school
        FOREIGN KEY (school_id)
        REFERENCES schools(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS dimensions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS action_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT NOT NULL,
    created_by INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    start_date DATE,
    end_date DATE,
    status VARCHAR(40) DEFAULT 'rascunho',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_action_plans_school
        FOREIGN KEY (school_id)
        REFERENCES schools(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_action_plans_user
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS action_plan_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_plan_id INT NOT NULL,
    dimension_id INT NOT NULL,
    objective TEXT NOT NULL,
    activities TEXT,
    responsible VARCHAR(150),
    deadline DATE,
    indicators TEXT,
    progress TINYINT UNSIGNED DEFAULT 0,
    CONSTRAINT fk_items_action_plan
        FOREIGN KEY (action_plan_id)
        REFERENCES action_plans(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_items_dimension
        FOREIGN KEY (dimension_id)
        REFERENCES dimensions(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (name, slug) VALUES
    ('Administrador', 'administrador'),
    ('Coordenador', 'coordenador'),
    ('Formador', 'formador'),
    ('Secretaria de Educação', 'secretaria-educacao'),
    ('Gestor Escolar', 'gestor-escolar')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO dimensions (name, slug) VALUES
    ('Político-Institucional', 'politico-institucional'),
    ('Pedagógico', 'pedagogico'),
    ('Administrativo-Financeira', 'administrativo-financeira'),
    ('Pessoal e Relacional', 'pessoal-relacional')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (role_id, name, email, password_hash)
SELECT roles.id,
       'Administrador do Sistema',
       'admin@sisaprender.local',
       '$2y$10$KRNSa4rZtiUaXEjDJc/6bOfMLIlZJVdRwNfQRYAmkjmgNF/iDZc2e'
FROM roles
WHERE roles.slug = 'administrador'
ON DUPLICATE KEY UPDATE name = VALUES(name), role_id = VALUES(role_id);
