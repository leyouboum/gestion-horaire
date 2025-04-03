-- Passage en mode changement de delimiteur pour permettre l'utilisation de blocs BEGIN...END
DELIMITER //

/* =========================
   Triggers pour la table: universite
   ========================= */
CREATE TRIGGER trg_universite_after_insert
AFTER INSERT ON universite
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('universite', NEW.id_universite, 'INSERT', CONCAT('Insertion de l\'université: ', NEW.nom));
END; //

CREATE TRIGGER trg_universite_after_update
AFTER UPDATE ON universite
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('universite', NEW.id_universite, 'UPDATE', CONCAT('Mise à jour de l\'université: ', NEW.nom));
END; //

CREATE TRIGGER trg_universite_after_delete
AFTER DELETE ON universite
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('universite', OLD.id_universite, 'DELETE', CONCAT('Suppression de l\'université: ', OLD.nom));
END; //

/* =========================
   Triggers pour la table: site
   ========================= */
CREATE TRIGGER trg_site_after_insert
AFTER INSERT ON site
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('site', NEW.id_site, 'INSERT', CONCAT('Insertion du site: ', NEW.nom));
END; //

CREATE TRIGGER trg_site_after_update
AFTER UPDATE ON site
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('site', NEW.id_site, 'UPDATE', CONCAT('Mise à jour du site: ', NEW.nom));
END; //

CREATE TRIGGER trg_site_after_delete
AFTER DELETE ON site
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('site', OLD.id_site, 'DELETE', CONCAT('Suppression du site: ', OLD.nom));
END; //

/* =========================
   Triggers pour la table: salle
   ========================= */
CREATE TRIGGER trg_salle_after_insert
AFTER INSERT ON salle
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('salle', NEW.id_salle, 'INSERT', CONCAT('Insertion de la salle: ', NEW.nom_salle));
END; //

CREATE TRIGGER trg_salle_after_update
AFTER UPDATE ON salle
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('salle', NEW.id_salle, 'UPDATE', CONCAT('Mise à jour de la salle: ', NEW.nom_salle));
END; //

CREATE TRIGGER trg_salle_after_delete
AFTER DELETE ON salle
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('salle', OLD.id_salle, 'DELETE', CONCAT('Suppression de la salle: ', OLD.nom_salle));
END; //

/* =========================
   Triggers pour la table: materiel
   ========================= */
CREATE TRIGGER trg_materiel_after_insert
AFTER INSERT ON materiel
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('materiel', NEW.id_materiel, 'INSERT', CONCAT('Insertion du matériel: ', NEW.type_materiel));
END; //

CREATE TRIGGER trg_materiel_after_update
AFTER UPDATE ON materiel
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('materiel', NEW.id_materiel, 'UPDATE', CONCAT('Mise à jour du matériel: ', NEW.type_materiel));
END; //

CREATE TRIGGER trg_materiel_after_delete
AFTER DELETE ON materiel
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('materiel', OLD.id_materiel, 'DELETE', CONCAT('Suppression du matériel: ', OLD.type_materiel));
END; //

/* =========================
   Triggers pour la table: cours
   ========================= */
CREATE TRIGGER trg_cours_after_insert
AFTER INSERT ON cours
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours', NEW.id_cours, 'INSERT', CONCAT('Insertion du cours: ', NEW.nom_cours));
END; //

CREATE TRIGGER trg_cours_after_update
AFTER UPDATE ON cours
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours', NEW.id_cours, 'UPDATE', CONCAT('Mise à jour du cours: ', NEW.nom_cours));
END; //

CREATE TRIGGER trg_cours_after_delete
AFTER DELETE ON cours
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours', OLD.id_cours, 'DELETE', CONCAT('Suppression du cours: ', OLD.nom_cours));
END; //

/* =========================
   Triggers pour la table: groupe
   ========================= */
CREATE TRIGGER trg_groupe_after_insert
AFTER INSERT ON groupe
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('groupe', NEW.id_groupe, 'INSERT', CONCAT('Insertion du groupe: ', NEW.nom_groupe));
END; //

CREATE TRIGGER trg_groupe_after_update
AFTER UPDATE ON groupe
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('groupe', NEW.id_groupe, 'UPDATE', CONCAT('Mise à jour du groupe: ', NEW.nom_groupe));
END; //

CREATE TRIGGER trg_groupe_after_delete
AFTER DELETE ON groupe
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('groupe', OLD.id_groupe, 'DELETE', CONCAT('Suppression du groupe: ', OLD.nom_groupe));
END; //

/* =========================
   Triggers pour la table: planning
   ========================= */
CREATE TRIGGER trg_planning_after_insert
AFTER INSERT ON planning
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('planning', NEW.id_planning, 'INSERT', CONCAT('Insertion du planning de ', NEW.date_heure_debut, ' à ', NEW.date_heure_fin));
END; //

CREATE TRIGGER trg_planning_after_update
AFTER UPDATE ON planning
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('planning', NEW.id_planning, 'UPDATE', CONCAT('Mise à jour du planning de ', NEW.date_heure_debut, ' à ', NEW.date_heure_fin));
END; //

CREATE TRIGGER trg_planning_after_delete
AFTER DELETE ON planning
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('planning', OLD.id_planning, 'DELETE', CONCAT('Suppression du planning de ', OLD.date_heure_debut, ' à ', OLD.date_heure_fin));
END; //

/* =========================
   Triggers pour la table: deplacement
   ========================= */
CREATE TRIGGER trg_deplacement_after_insert
AFTER INSERT ON deplacement
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('deplacement', NEW.id_deplacement, 'INSERT', CONCAT('Insertion du déplacement: de ', NEW.id_site_depart, ' vers ', NEW.id_site_arrivee, ' (', NEW.duree, ' min)'));
END; //

CREATE TRIGGER trg_deplacement_after_update
AFTER UPDATE ON deplacement
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('deplacement', NEW.id_deplacement, 'UPDATE', CONCAT('Mise à jour du déplacement: de ', NEW.id_site_depart, ' vers ', NEW.id_site_arrivee, ' (', NEW.duree, ' min)'));
END; //

CREATE TRIGGER trg_deplacement_after_delete
AFTER DELETE ON deplacement
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('deplacement', OLD.id_deplacement, 'DELETE', CONCAT('Suppression du déplacement: de ', OLD.id_site_depart, ' vers ', OLD.id_site_arrivee, ' (', OLD.duree, ' min)'));
END; //

/* =========================
   Triggers pour la table: materiel_affectation
   ========================= */
CREATE TRIGGER trg_materiel_affectation_after_insert
AFTER INSERT ON materiel_affectation
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('materiel_affectation', NEW.id_affectation, 'INSERT', CONCAT('Insertion de l\'affectation de matériel pour le planning ', NEW.id_planning));
END; //

CREATE TRIGGER trg_materiel_affectation_after_update
AFTER UPDATE ON materiel_affectation
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('materiel_affectation', NEW.id_affectation, 'UPDATE', CONCAT('Mise à jour de l\'affectation de matériel pour le planning ', NEW.id_planning));
END; //

CREATE TRIGGER trg_materiel_affectation_after_delete
AFTER DELETE ON materiel_affectation
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('materiel_affectation', OLD.id_affectation, 'DELETE', CONCAT('Suppression de l\'affectation de matériel pour le planning ', OLD.id_planning));
END; //

/* =========================
   Triggers pour les tables de liaison
   ========================= */

-- Table: cours_site
CREATE TRIGGER trg_cours_site_after_insert
AFTER INSERT ON cours_site
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours_site', 0, 'INSERT', CONCAT('Association ajoutée : cours ', NEW.id_cours, ' - site ', NEW.id_site));
END; //

CREATE TRIGGER trg_cours_site_after_delete
AFTER DELETE ON cours_site
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours_site', 0, 'DELETE', CONCAT('Association supprimée : cours ', OLD.id_cours, ' - site ', OLD.id_site));
END; //

-- Table: cours_materiel
CREATE TRIGGER trg_cours_materiel_after_insert
AFTER INSERT ON cours_materiel
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours_materiel', 0, 'INSERT', CONCAT('Association ajoutée : cours ', NEW.id_cours, ' - matériel ', NEW.id_materiel));
END; //

CREATE TRIGGER trg_cours_materiel_after_delete
AFTER DELETE ON cours_materiel
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours_materiel', 0, 'DELETE', CONCAT('Association supprimée : cours ', OLD.id_cours, ' - matériel ', OLD.id_materiel));
END; //

-- Table: groupe_site
CREATE TRIGGER trg_groupe_site_after_insert
AFTER INSERT ON groupe_site
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('groupe_site', 0, 'INSERT', CONCAT('Association ajoutée : groupe ', NEW.id_groupe, ' - site ', NEW.id_site, ' (principal: ', NEW.is_principal, ')'));
END; //

CREATE TRIGGER trg_groupe_site_after_delete
AFTER DELETE ON groupe_site
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('groupe_site', 0, 'DELETE', CONCAT('Association supprimée : groupe ', OLD.id_groupe, ' - site ', OLD.id_site, ' (principal: ', OLD.is_principal, ')'));
END; //

-- Table: cours_groupe
CREATE TRIGGER trg_cours_groupe_after_insert
AFTER INSERT ON cours_groupe
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours_groupe', 0, 'INSERT', CONCAT('Association ajoutée : cours ', NEW.id_cours, ' - groupe ', NEW.id_groupe));
END; //

CREATE TRIGGER trg_cours_groupe_after_delete
AFTER DELETE ON cours_groupe
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, record_id, operation, message)
    VALUES ('cours_groupe', 0, 'DELETE', CONCAT('Association supprimée : cours ', OLD.id_cours, ' - groupe ', OLD.id_groupe));
END; //

-- Rétablissement du délimiteur par défaut
DELIMITER ;
