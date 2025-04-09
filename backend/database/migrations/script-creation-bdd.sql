-- =========================================================
-- Script complet de création de la base de données
-- Projet : Gestion d'horaires universitaires
-- Intégration des améliorations :
--    4) Table annee_academique pour la gestion centralisée des années académiques
--    5) Ajout du champ statut dans planning
-- =========================================================

-- =========================================================
-- 1. Table universite
-- =========================================================
CREATE TABLE IF NOT EXISTS universite (
    id_universite INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    CONSTRAINT unique_universite_nom UNIQUE (nom)
);

-- =========================================================
-- 2. Table site
-- Chaque site est rattaché à une université et possède sa plage horaire
-- =========================================================
CREATE TABLE IF NOT EXISTS site (
    id_site INT AUTO_INCREMENT PRIMARY KEY,
    id_universite INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    heure_ouverture TIME NOT NULL,
    heure_fermeture TIME NOT NULL,
    CONSTRAINT FK_site_universite FOREIGN KEY (id_universite)
        REFERENCES universite(id_universite)
        ON DELETE CASCADE,
    CONSTRAINT unique_site_nom UNIQUE (id_universite, nom)
);

-- =========================================================
-- 3. Table salle
-- Chaque salle est rattachée à un site et possède une capacité maximale
-- =========================================================
CREATE TABLE IF NOT EXISTS salle (
    id_salle INT AUTO_INCREMENT PRIMARY KEY,
    id_site INT NOT NULL,
    nom_salle VARCHAR(100) NOT NULL,
    capacite_max INT NOT NULL,
    CONSTRAINT FK_salle_site FOREIGN KEY (id_site)
        REFERENCES site(id_site)
        ON DELETE CASCADE,
    CONSTRAINT unique_salle_nom UNIQUE (id_site, nom_salle)
);

-- =========================================================
-- 4. Table materiel
-- Le matériel peut être fixe (associé à une salle) ou mobile.
-- Une colonne optionnelle permet d’affecter du matériel mobile à un site.
-- =========================================================
CREATE TABLE IF NOT EXISTS materiel (
    id_materiel INT AUTO_INCREMENT PRIMARY KEY,
    type_materiel VARCHAR(100) NOT NULL,
    is_mobile BOOLEAN NOT NULL DEFAULT FALSE,
    id_salle_fixe INT NULL,
    id_site_affectation INT NULL,
    CONSTRAINT FK_materiel_salle FOREIGN KEY (id_salle_fixe)
        REFERENCES salle(id_salle)
        ON DELETE SET NULL,
    CONSTRAINT FK_materiel_site_affectation FOREIGN KEY (id_site_affectation)
        REFERENCES site(id_site)
        ON DELETE SET NULL
);

-- =========================================================
-- 5. Table cours
-- Le cours est défini par un nom, des éventuels détails, sa durée (par défaut 1h)
-- et un code unique généré automatiquement pour les enregistrements existants.
-- =========================================================
CREATE TABLE IF NOT EXISTS cours (
    id_cours INT AUTO_INCREMENT PRIMARY KEY,
    nom_cours VARCHAR(100) NOT NULL,
    details TEXT NULL,
    duree INT NOT NULL DEFAULT 1,  -- durée en heures (par défaut 1h)
    code_cours VARCHAR(20) NOT NULL,
    CONSTRAINT unique_code_cours UNIQUE (code_cours)
);

-- =========================================================
-- 6. Table cours_site
-- Indique sur quels sites un cours peut être proposé.
-- =========================================================
CREATE TABLE IF NOT EXISTS cours_site (
    id_cours INT NOT NULL,
    id_site INT NOT NULL,
    PRIMARY KEY (id_cours, id_site),
    CONSTRAINT FK_cours_site_cours FOREIGN KEY (id_cours)
        REFERENCES cours(id_cours)
        ON DELETE CASCADE,
    CONSTRAINT FK_cours_site_site FOREIGN KEY (id_site)
        REFERENCES site(id_site)
        ON DELETE CASCADE
);

-- =========================================================
-- 7. Table cours_materiel
-- Un cours peut nécessiter plusieurs matériels.
-- =========================================================
CREATE TABLE IF NOT EXISTS cours_materiel (
    id_cours INT NOT NULL,
    id_materiel INT NOT NULL,
    PRIMARY KEY (id_cours, id_materiel),
    CONSTRAINT FK_cours_materiel_cours FOREIGN KEY (id_cours)
        REFERENCES cours(id_cours)
        ON DELETE CASCADE,
    CONSTRAINT FK_cours_materiel_materiel FOREIGN KEY (id_materiel)
        REFERENCES materiel(id_materiel)
        ON DELETE CASCADE
);

-- =========================================================
-- 8. Table groupe
-- Un groupe est défini par son nom, le nombre d'étudiants (entre 20 et 40) 
-- et il est rattaché à une université.
-- =========================================================
CREATE TABLE IF NOT EXISTS groupe (
    id_groupe INT AUTO_INCREMENT PRIMARY KEY,
    nom_groupe VARCHAR(100) NOT NULL,
    nb_etudiants INT NOT NULL,
    id_universite INT NOT NULL,
    CONSTRAINT CHK_groupe_nb_etudiants CHECK (nb_etudiants >= 20 AND nb_etudiants <= 40),
    CONSTRAINT unique_groupe_nom UNIQUE (id_universite, nom_groupe),
    CONSTRAINT FK_groupe_universite FOREIGN KEY (id_universite)
        REFERENCES universite(id_universite)
        ON DELETE CASCADE
);

-- =========================================================
-- 9. Table groupe_site
-- Permet d'associer un groupe à un ou plusieurs sites.
-- Le champ 'is_principal' indique le site principal du groupe.
-- (L'unicité du site principal devra être gérée via la logique applicative.)
-- =========================================================
CREATE TABLE IF NOT EXISTS groupe_site (
    id_groupe INT NOT NULL,
    id_site INT NOT NULL,
    is_principal BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (id_groupe, id_site),
    CONSTRAINT FK_groupe_site_groupe FOREIGN KEY (id_groupe)
        REFERENCES groupe(id_groupe)
        ON DELETE CASCADE,
    CONSTRAINT FK_groupe_site_site FOREIGN KEY (id_site)
        REFERENCES site(id_site)
        ON DELETE CASCADE
);

-- =========================================================
-- 10. Table cours_groupe
-- Un cours peut être attribué à plusieurs groupes.
-- =========================================================
CREATE TABLE IF NOT EXISTS cours_groupe (
    id_cours INT NOT NULL,
    id_groupe INT NOT NULL,
    PRIMARY KEY (id_cours, id_groupe),
    CONSTRAINT FK_cours_groupe_cours FOREIGN KEY (id_cours)
        REFERENCES cours(id_cours)
        ON DELETE CASCADE,
    CONSTRAINT FK_cours_groupe_groupe FOREIGN KEY (id_groupe)
        REFERENCES groupe(id_groupe)
        ON DELETE CASCADE
);

-- =========================================================
-- 11. Table annee_academique
-- Permet une gestion centralisée des années académiques
-- =========================================================
CREATE TABLE IF NOT EXISTS annee_academique (
    id_annee INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(20) NOT NULL, -- ex: "2024-2025"
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    CONSTRAINT unique_annee UNIQUE (libelle)
);

-- =========================================================
-- 12. Table planning
-- Chaque créneau planifié lie un cours, un groupe et une salle à un horaire donné,
-- pour une année académique donnée.
-- Intégration de l'amélioration : ajout de id_annee et du champ statut
-- =========================================================
CREATE TABLE IF NOT EXISTS planning (
    id_planning INT AUTO_INCREMENT PRIMARY KEY,
    id_salle INT NOT NULL,
    id_cours INT NOT NULL,
    id_groupe INT NOT NULL,
    date_heure_debut DATETIME NOT NULL,
    date_heure_fin DATETIME NOT NULL,
    id_annee INT NOT NULL,
    statut ENUM('planifie', 'annule', 'modifie', 'valide') DEFAULT 'planifie',
    CONSTRAINT FK_planning_salle FOREIGN KEY (id_salle)
        REFERENCES salle(id_salle)
        ON DELETE CASCADE,
    CONSTRAINT FK_planning_cours FOREIGN KEY (id_cours)
        REFERENCES cours(id_cours)
        ON DELETE CASCADE,
    CONSTRAINT FK_planning_groupe FOREIGN KEY (id_groupe)
        REFERENCES groupe(id_groupe)
        ON DELETE CASCADE,
    CONSTRAINT FK_planning_annee FOREIGN KEY (id_annee)
        REFERENCES annee_academique(id_annee)
        ON DELETE RESTRICT,
    INDEX idx_date_debut (date_heure_debut)
);

-- =========================================================
-- 13. Table deplacement
-- Permet de gérer le temps de déplacement entre deux sites.
-- On prévoit ici une durée en minutes (par exemple 60)
-- =========================================================
CREATE TABLE IF NOT EXISTS deplacement (
    id_deplacement INT AUTO_INCREMENT PRIMARY KEY,
    id_site_depart INT NOT NULL,
    id_site_arrivee INT NOT NULL,
    duree INT NOT NULL, -- durée en minutes
    CONSTRAINT FK_deplacement_site_depart FOREIGN KEY (id_site_depart)
        REFERENCES site(id_site)
        ON DELETE CASCADE,
    CONSTRAINT FK_deplacement_site_arrivee FOREIGN KEY (id_site_arrivee)
        REFERENCES site(id_site)
        ON DELETE CASCADE,
    CONSTRAINT unique_deplacement UNIQUE (id_site_depart, id_site_arrivee)
);

-- =========================================================
-- 14. Table materiel_affectation
-- Permet d'affecter du matériel à un créneau planifié.
-- =========================================================
CREATE TABLE IF NOT EXISTS materiel_affectation (
    id_affectation INT AUTO_INCREMENT PRIMARY KEY,
    id_materiel INT NOT NULL,
    id_planning INT NOT NULL,
    date_heure_debut DATETIME NOT NULL,
    date_heure_fin DATETIME NOT NULL,
    CONSTRAINT FK_materiel_affectation_materiel FOREIGN KEY (id_materiel)
        REFERENCES materiel(id_materiel)
        ON DELETE CASCADE,
    CONSTRAINT FK_materiel_affectation_planning FOREIGN KEY (id_planning)
        REFERENCES planning(id_planning)
        ON DELETE CASCADE
);

-- =========================================================
-- 15. Table audit_log
-- Suivi des opérations sur la base (insertions, mises à jour, suppressions).
-- =========================================================
CREATE TABLE IF NOT EXISTS audit_log (
    id_audit INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100) NOT NULL,
    record_id INT DEFAULT NULL,
    operation VARCHAR(10) NOT NULL, -- 'INSERT', 'UPDATE', 'DELETE'
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
