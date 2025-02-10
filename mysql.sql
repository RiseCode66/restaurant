CREATE DATABASE resto;
USE resto;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE ingredient (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    stock INT NOT NULL
);

CREATE TABLE recette (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE plat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prix FLOAT NOT NULL,
    id_recette INT NOT NULL,
    FOREIGN KEY (id_recette) REFERENCES recette(id) ON DELETE CASCADE
);

CREATE TABLE composition (
    id_recette INT NOT NULL,
    id_ingredient INT NOT NULL,
    quantite INT NOT NULL,
    PRIMARY KEY (id_recette, id_ingredient),
    FOREIGN KEY (id_recette) REFERENCES recette(id) ON DELETE CASCADE,
    FOREIGN KEY (id_ingredient) REFERENCES ingredient(id) ON DELETE CASCADE
);

CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_plat INT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    etat INT NOT NULL DEFAULT 0,
    id_client INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (id_plat) REFERENCES plat(id) ON DELETE CASCADE
);

-- Insérer des utilisateurs
INSERT INTO users (email, password) VALUES 
('admin@example.com', 'password123'), 
('client@example.com', 'securepass');

-- Insérer des ingrédients
INSERT INTO ingredient (nom, stock) VALUES 
('Tomate', 100),
('Pâte', 50),
('Oignon', 75),
('Bouillon de soupe', 40),
('Fromage', 60),
('Pain', 90),
('Charcuterie', 80),
('Riz', 120),
('Porc', 45),
('Oeuf', 200);

-- Insérer des recettes
INSERT INTO recette (nom) VALUES 
('Pizza Margherita'), 
('Burger Classic');

-- Insérer des plats
INSERT INTO plat (nom, prix, id_recette) VALUES 
('Pizza Margherita', 12.99, 1), 
('Burger Classic', 9.99, 2);

-- Insérer des compositions de recettes
INSERT INTO composition (id_recette, id_ingredient, quantite) VALUES 
(1, 1, 3),  -- 3 tomates pour la Pizza Margherita
(1, 3, 2),  -- 2 portions de fromage pour la Pizza Margherita
(2, 2, 1);  -- 1 oignon pour le Burger Classic

-- Insérer des utilisateurs
INSERT INTO client (email, password) VALUES 
('client@g.c', '123'), 
('client2@a.b', '123');

-- Insérer des commandes
INSERT INTO commande (id_plat, date, etat, id_client) VALUES 
(1, NOW(), 0, 1),
(2, NOW(), 1, 1);
