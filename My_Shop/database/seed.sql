-- Données de démonstration (optionnel). À importer APRÈS schema.sql.
-- Le compte administrateur se crée en ligne de commande : php bin/create_admin.php
USE my_shop;

INSERT INTO categories (id, name, parent_id) VALUES
    (1, 'Lounge', NULL),
    (2, 'Chaises', NULL),
    (3, 'Tables', NULL),
    (4, 'Fauteuils', 1);

INSERT INTO products (name, description, price, category_id, image_path) VALUES
    ('Coombes',  'Canapé lounge aux lignes douces, assise profonde.',        2600.00, 1, 'assets/photo/img1.jpeg'),
    ('Hartley',  'Fauteuil rembourré, pieds en chêne massif.',                 890.00, 4, 'assets/photo/img2.jpeg'),
    ('Marlow',   'Chaise de salle à manger, structure métal et cuir.',         240.00, 2, 'assets/photo/img3.jpeg'),
    ('Ashby',    'Table basse en noyer, plateau 90 cm.',                       420.00, 3, 'assets/photo/img4.jpeg'),
    ('Bexley',   'Banquette deux places, tissu bouclé.',                      1450.00, 1, 'assets/photo/img5.jpeg'),
    ('Corwin',   'Chaise empilable, polypropylène recyclé.',                    85.00, 2, 'assets/photo/img6.jpeg'),
    ('Denholm',  'Table à manger extensible, 6 à 10 couverts.',                1980.00, 3, 'assets/photo/img7.jpeg');
