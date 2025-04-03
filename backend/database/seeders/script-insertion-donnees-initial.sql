INSERT INTO universite (nom) VALUES 
('Université Libre de Bruxelles'),
('KU Leuven'),
('Ghent University'),
('Université catholique de Louvain'),
('Vrije Universiteit Brussel'),
('Université de Liège'),
('University of Antwerp'),
('Hasselt University'),
('University of Mons'),
('University of Namur');

INSERT INTO site (id_universite, nom, heure_ouverture, heure_fermeture) VALUES
(1, 'Campus Solbosch', '08:30:00', '18:00:00'),
(2, 'Campus Heverlee', '08:00:00', '17:00:00'),
(3, 'Campus Gand', '08:30:00', '18:00:00'),
(4, 'Campus Louvain-la-Neuve', '08:00:00', '17:30:00'),
(5, 'Campus Etterbeek', '08:30:00', '18:00:00'),
(6, 'Campus Sart-Tilman', '08:00:00', '17:30:00'),
(7, 'Campus Antwerpen', '08:30:00', '18:00:00'),
(8, 'Campus Hasselt', '08:00:00', '17:00:00'),
(9, 'Campus Mons', '08:30:00', '18:00:00'),
(10, 'Campus Namur', '08:00:00', '17:30:00');

INSERT INTO salle (id_site, nom_salle, capacite_max) VALUES
(1, 'Salle 101', 50),
(2, 'Salle 201', 60),
(3, 'Salle 301', 70),
(4, 'Salle 401', 80),
(5, 'Salle 501', 90),
(6, 'Salle 601', 50),
(7, 'Salle 701', 55),
(8, 'Salle 801', 65),
(9, 'Salle 901', 75),
(10, 'Salle 1001', 85);

INSERT INTO materiel (type_materiel, is_mobile, id_salle_fixe, id_site_affectation) VALUES
('Projecteur', FALSE, 1, NULL),
('Tableau interactif', FALSE, 2, NULL),
('Enceinte', TRUE, NULL, 3),
('Microphone', TRUE, NULL, 4),
('Ordinateur', FALSE, 5, NULL),
('Caméra', TRUE, NULL, 6),
('Imprimante', FALSE, 7, NULL),
('Scanner', TRUE, NULL, 8),
('Pointeur', TRUE, NULL, 9),
('Ecran tactile', FALSE, 10, NULL);

INSERT INTO cours (nom_cours, details, duree, code_cours) VALUES
('Mathématiques', 'Cours de mathématiques fondamentales', 1, 'COURSE_1'),
('Physique', 'Cours de physique générale', 1, 'COURSE_2'),
('Chimie', 'Cours de chimie organique et inorganique', 1, 'COURSE_3'),
('Biologie', 'Cours de biologie cellulaire', 1, 'COURSE_4'),
('Histoire', 'Cours d’histoire moderne et contemporaine', 1, 'COURSE_5'),
('Géographie', 'Cours de géographie humaine et physique', 1, 'COURSE_6'),
('Informatique', 'Cours d’informatique théorique et pratique', 1, 'COURSE_7'),
('Philosophie', 'Cours de philosophie et éthique', 1, 'COURSE_8'),
('Langues', 'Cours de langues étrangères (Anglais, Français, etc.)', 1, 'COURSE_9'),
('Économie', 'Cours d’économie et de gestion', 1, 'COURSE_10');

UPDATE cours
  SET code_cours = CONCAT('COURSE_', id_cours)
  WHERE code_cours = '';

INSERT INTO cours_site (id_cours, id_site) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

INSERT INTO cours_materiel (id_cours, id_materiel) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

INSERT INTO groupe (nom_groupe, nb_etudiants, id_universite) VALUES
('Groupe A', 25, 1),
('Groupe B', 30, 2),
('Groupe C', 35, 3),
('Groupe D', 20, 4),
('Groupe E', 40, 5),
('Groupe F', 28, 6),
('Groupe G', 32, 7),
('Groupe H', 27, 8),
('Groupe I', 38, 9),
('Groupe J', 22, 10);

INSERT INTO groupe_site (id_groupe, id_site, is_principal) VALUES
(1, 1, TRUE),
(2, 2, TRUE),
(3, 3, TRUE),
(4, 4, TRUE),
(5, 5, TRUE),
(6, 6, TRUE),
(7, 7, TRUE),
(8, 8, TRUE),
(9, 9, TRUE),
(10, 10, TRUE);

INSERT INTO cours_groupe (id_cours, id_groupe) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

INSERT INTO planning (id_salle, id_cours, id_groupe, date_heure_debut, date_heure_fin, annee_academique) VALUES
(1, 1, 1, '2025-04-01 08:30:00', '2025-04-01 09:30:00', '2024-2025'),
(2, 2, 2, '2025-04-01 09:45:00', '2025-04-01 10:45:00', '2024-2025'),
(3, 3, 3, '2025-04-01 11:00:00', '2025-04-01 12:00:00', '2024-2025'),
(4, 4, 4, '2025-04-01 12:15:00', '2025-04-01 13:15:00', '2024-2025'),
(5, 5, 5, '2025-04-01 13:30:00', '2025-04-01 14:30:00', '2024-2025'),
(6, 6, 6, '2025-04-01 14:45:00', '2025-04-01 15:45:00', '2024-2025'),
(7, 7, 7, '2025-04-01 16:00:00', '2025-04-01 17:00:00', '2024-2025'),
(8, 8, 8, '2025-04-01 15:00:00', '2025-04-01 16:00:00', '2024-2025'),
(9, 9, 9, '2025-04-01 17:00:00', '2025-04-01 18:00:00', '2024-2025'),
(10, 10, 10, '2025-04-01 16:00:00', '2025-04-01 17:00:00', '2024-2025');

INSERT INTO deplacement (id_site_depart, id_site_arrivee, duree) VALUES
(1, 2, 60),
(2, 3, 60),
(3, 4, 60),
(4, 5, 60),
(5, 6, 60),
(6, 7, 60),
(7, 8, 60),
(8, 9, 60),
(9, 10, 60),
(10, 1, 60);

INSERT INTO materiel_affectation (id_materiel, id_planning, date_heure_debut, date_heure_fin) VALUES
(1, 1, '2025-04-01 08:30:00', '2025-04-01 09:30:00'),
(2, 2, '2025-04-01 09:45:00', '2025-04-01 10:45:00'),
(3, 3, '2025-04-01 11:00:00', '2025-04-01 12:00:00'),
(4, 4, '2025-04-01 12:15:00', '2025-04-01 13:15:00'),
(5, 5, '2025-04-01 13:30:00', '2025-04-01 14:30:00'),
(6, 6, '2025-04-01 14:45:00', '2025-04-01 15:45:00'),
(7, 7, '2025-04-01 16:00:00', '2025-04-01 17:00:00'),
(8, 8, '2025-04-01 15:00:00', '2025-04-01 16:00:00'),
(9, 9, '2025-04-01 17:00:00', '2025-04-01 18:00:00'),
(10, 10, '2025-04-01 16:00:00', '2025-04-01 17:00:00');

INSERT INTO audit_log (table_name, record_id, operation, message, created_at) VALUES
('universite', 1, 'INSERT', 'Insertion initiale de Université Libre de Bruxelles', '2025-03-31 10:00:00'),
('universite', 2, 'INSERT', 'Insertion initiale de KU Leuven', '2025-03-31 10:05:00'),
('site', 1, 'INSERT', 'Insertion du Campus Solbosch', '2025-03-31 10:10:00'),
('salle', 1, 'INSERT', 'Insertion de Salle 101 pour Campus Solbosch', '2025-03-31 10:15:00'),
('materiel', 1, 'INSERT', 'Insertion du Projecteur dans Salle 101', '2025-03-31 10:20:00'),
('cours', 1, 'INSERT', 'Insertion du cours Mathématiques', '2025-03-31 10:25:00'),
('groupe', 1, 'INSERT', 'Insertion de Groupe A', '2025-03-31 10:30:00'),
('planning', 1, 'INSERT', 'Création du planning pour Groupe A, Mathématiques', '2025-03-31 10:35:00'),
('deplacement', 1, 'INSERT', 'Insertion du déplacement de Campus Solbosch vers Campus Heverlee', '2025-03-31 10:40:00'),
('materiel_affectation', 1, 'INSERT', 'Affectation du Projecteur au planning de Groupe A', '2025-03-31 10:45:00');
