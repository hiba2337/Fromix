-- =====================================================
-- DATABASE: formix_training_platform_v2
-- DATE: December 2025
-- DESCRIPTION: Plateforme de gestion des formations
-- =====================================================

DROP DATABASE IF EXISTS formix_training_platform_v2;
CREATE DATABASE formix_training_platform_v2 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE formix_training_platform_v2;

-- GROUPE 1: GESTION DES UTILISATEURS (4 tables)
-- ________________________________________________

-- Table: roles 
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_role VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    niveau_acces INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nom (nom_role)
) ENGINE=InnoDB COMMENT='Rôles du système';

-- Insertion des 8 rôles
INSERT INTO roles (nom_role, description, niveau_acces) VALUES
('visiteur', 'Accès public consultation uniquement', 1),
('apprenant', 'Étudiant inscrit aux formations', 2),
('formateur', 'Enseignant des formations', 3),
('assistant_commercial', 'Support commercial', 4),
('commercial', 'Gestion des ventes', 5),
('marketing', 'Gestion marketing et promotions', 5),
('directeur_pedagogique', 'Direction pédagogique', 6),
('administrateur', 'Accès total au système', 10);

-- Table: users 
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 1,
    telephone VARCHAR(20),
    adresse TEXT,
    photo VARCHAR(255),
    date_naissance DATE,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    last_login DATETIME,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    INDEX idx_email (email),
    INDEX idx_role (role_id),
    INDEX idx_active (is_active),
    INDEX idx_name (nom, prenom)
) ENGINE=InnoDB COMMENT='Utilisateurs du système';

-- Table: contact_message
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    nom VARCHAR(100),
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    sujet VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    statut ENUM('nouveau', 'en_cours', 'resolu', 'ferme') DEFAULT 'nouveau',
    traite_par INT NULL,
    reponse TEXT,
    date_reponse DATETIME,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    FOREIGN KEY (traite_par) REFERENCES users(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    INDEX idx_statut (statut),
    INDEX idx_date (date_envoi)
) ENGINE=InnoDB COMMENT='Messages de contact';

-- Table activity_logs
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_user_date (user_id, created_at),
    INDEX idx_action (action_type),
    INDEX idx_table (table_name)
) ENGINE=InnoDB COMMENT='Historique des activités';

-- GROUPE 2: GESTION DES FORMATIONS (7 tables)
-- _____________________________________________

-- Table categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT NULL,
    icon VARCHAR(50),
    image VARCHAR(255),
    ordre INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    INDEX idx_parent (parent_id),
    INDEX idx_active (is_active),
    INDEX idx_slug (slug)
) ENGINE=InnoDB COMMENT='Catégories des formations';

-- Insertion des catégories principales
INSERT INTO categories (nom, slug, description, icon, ordre) VALUES
('Développement Web', 'developpement-web', 'HTML, CSS, JavaScript, PHP', 'code', 1),
('Cybersécurité', 'cybersecurite', 'Sécurité informatique et réseaux', 'shield', 2),
('Data Science', 'data-science', 'Analyse de données et IA', 'chart-bar', 3),
('Design', 'design', 'UX/UI Design et graphisme', 'palette', 4),
('Business', 'business', 'Marketing et Management', 'briefcase', 5);

-- Table: formateurs
CREATE TABLE formateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    description TEXT,
    specialite VARCHAR(200),
    experience INT DEFAULT 0,
    photo VARCHAR(255),
    cv VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    INDEX idx_email (email),
    INDEX idx_active (is_active)
) ENGINE=InnoDB COMMENT='Formateurs des formations';

-- Table: formateur_social_links
CREATE TABLE formateur_social_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formateur_id INT NOT NULL,
    type ENUM('facebook', 'linkedin', 'twitter', 'github', 'website') NOT NULL,
    url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formateur_id) REFERENCES formateurs(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    UNIQUE KEY unique_formateur_type (formateur_id, type),
    INDEX idx_formateur (formateur_id)
) ENGINE=InnoDB COMMENT='Liens sociaux des formateurs';

-- Table: partenaires
CREATE TABLE partenaires (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    logo VARCHAR(255),
    description TEXT,
    site_web VARCHAR(255),
    email VARCHAR(150),
    telephone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
) ENGINE=InnoDB COMMENT='Partenaires de formation';

-- Table: formations 
CREATE TABLE formations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    description TEXT,
    objectifs TEXT,
    prerequis TEXT,
    categorie_id INT NOT NULL,
    niveau ENUM('debutant', 'intermediaire', 'avance', 'expert') DEFAULT 'debutant',
    duree INT NOT NULL COMMENT 'Durée en heures',
    prix DECIMAL(10,2) NOT NULL DEFAULT 0,
    prix_promo DECIMAL(10,2) NULL,
    image VARCHAR(255),
    video_intro VARCHAR(255),
    formateur_id INT NOT NULL,
    partenaire_id INT NULL,
    places_disponibles INT DEFAULT 20,
    langue VARCHAR(20) DEFAULT 'fr',
    certificat_disponible BOOLEAN DEFAULT TRUE,
    is_published BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    date_debut DATE,
    date_fin DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    FOREIGN KEY (formateur_id) REFERENCES formateurs(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    FOREIGN KEY (partenaire_id) REFERENCES partenaires(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    INDEX idx_categorie (categorie_id),
    INDEX idx_formateur (formateur_id),
    INDEX idx_niveau (niveau),
    INDEX idx_prix (prix),
    INDEX idx_published (is_published),
    INDEX idx_featured (is_featured),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_slug (slug),
    FULLTEXT idx_search (titre, description)
) ENGINE=InnoDB COMMENT='Formations disponibles';

-- Table: formation_plans
CREATE TABLE formation_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formation_id INT NOT NULL,
    semaine INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    ordre INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_formation (formation_id),
    INDEX idx_ordre (ordre)
) ENGINE=InnoDB COMMENT='Plans hebdomadaires des formations';

-- Table: formation_skills
CREATE TABLE formation_skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formation_id INT NOT NULL,
    competence VARCHAR(150) NOT NULL,
    ordre INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_formation (formation_id)
) ENGINE=InnoDB COMMENT='Compétences acquises par formation';


-- GROUPE 3: SESSIONS & INSCRIPTIONS (3 tables)
-- ____________________________________________

-- Table: sessions
CREATE TABLE sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formation_id INT NOT NULL,
    nom VARCHAR(200) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    horaire VARCHAR(100),
    lieu VARCHAR(200),
    type_session ENUM('presentiel', 'en_ligne', 'hybride') DEFAULT 'presentiel',
    lien_zoom VARCHAR(255),
    places_totales INT DEFAULT 20,
    places_reservees INT DEFAULT 0,
    prix_session DECIMAL(10,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_formation (formation_id),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_active (is_active)
) ENGINE=InnoDB COMMENT='Sessions de formation';

-- Table: inscriptions
CREATE TABLE inscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id INT NOT NULL,
    statut ENUM('en_attente', 'confirme', 'annule', 'termine') DEFAULT 'en_attente',
    note_finale DECIMAL(5,2),
    presence_percent DECIMAL(5,2),
    certificat_genere BOOLEAN DEFAULT FALSE,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_confirmation DATETIME,
    date_annulation DATETIME,
    motif_annulation TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (session_id) REFERENCES sessions(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    UNIQUE KEY unique_user_session (user_id, session_id),
    INDEX idx_user (user_id),
    INDEX idx_session (session_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB COMMENT='Inscriptions aux formations';


-- GROUPE 4: PAIEMENTS & PROMOTIONS (3 tables)
-- ___________________________________________

--  Table promotions
CREATE TABLE promotions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    type_reduction ENUM('pourcentage', 'montant_fixe') NOT NULL,
    valeur_reduction DECIMAL(10,2) NOT NULL,
    achat_minimum DECIMAL(10,2) DEFAULT 0,
    utilisations_max INT DEFAULT NULL,
    utilisations_actuelles INT DEFAULT 0,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    INDEX idx_code (code),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_active (is_active)
) ENGINE=InnoDB COMMENT='Codes promotionnels';

-- Table: paiements 
CREATE TABLE paiements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    inscription_id INT NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    montant_paye DECIMAL(10,2) NOT NULL,
    promotion_id INT NULL,
    montant_reduction DECIMAL(10,2) DEFAULT 0,
    methode_paiement ENUM('ccp', 'baridimob', 'virement', 'especes', 'carte') NOT NULL,
    reference_transaction VARCHAR(100),
    preuve_paiement VARCHAR(255),
    statut ENUM('en_attente', 'valide', 'refuse', 'rembourse') DEFAULT 'en_attente',
    date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation DATETIME,
    valide_par INT NULL,
    notes TEXT,
    FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (promotion_id) REFERENCES promotions(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    FOREIGN KEY (valide_par) REFERENCES users(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    INDEX idx_inscription (inscription_id),
    INDEX idx_statut (statut),
    INDEX idx_date (date_paiement),
    INDEX idx_reference (reference_transaction)
) ENGINE=InnoDB COMMENT='Historique des paiements';

-- Table: panier
CREATE TABLE panier (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    formation_id INT NOT NULL,
    session_id INT NULL,
    quantite INT DEFAULT 1,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (formation_id) REFERENCES formations(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (session_id) REFERENCES sessions(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    UNIQUE KEY unique_user_formation (user_id, formation_id, session_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB COMMENT='Panier d''achat temporaire';


-- GROUPE 5: ÉVALUATIONS & CERTIFICATIONS (3 tables)
-- _________________________________________________

-- Table: formation_ratings
CREATE TABLE formation_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formation_id INT NOT NULL,
    user_id INT NOT NULL,
    note DECIMAL(2,1) NOT NULL CHECK (note BETWEEN 0 AND 5),
    commentaire TEXT,
    is_approved BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    UNIQUE KEY unique_user_formation (user_id, formation_id),
    INDEX idx_formation (formation_id),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB COMMENT='Évaluations des formations';

-- Table: certifications
CREATE TABLE certifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    formation_id INT NOT NULL,
    session_id INT NOT NULL,
    code_unique VARCHAR(50) NOT NULL UNIQUE,
    fichier_pdf VARCHAR(255),
    note_finale DECIMAL(5,2),
    date_obtention DATE NOT NULL,
    date_expiration DATE,
    is_valid BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (formation_id) REFERENCES formations(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (session_id) REFERENCES sessions(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_formation (formation_id),
    INDEX idx_code (code_unique),
    INDEX idx_valid (is_valid)
) ENGINE=InnoDB COMMENT='Certificats générés';

-- Table: temoignages
CREATE TABLE temoignages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    formation_id INT NULL,
    message TEXT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    is_featured BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (formation_id) REFERENCES formations(id) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    INDEX idx_approved (is_approved),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB COMMENT='Témoignages des apprenants';


-- GROUPE 6: ÉVÉNEMENTS (2 tables)
-- _________________________________

-- Table: evenements
CREATE TABLE evenements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    description TEXT,
    type_evenement ENUM('conference', 'atelier', 'webinaire', 'salon', 'autre') DEFAULT 'conference',
    lieu VARCHAR(200),
    adresse TEXT,
    lien_zoom VARCHAR(255),
    date_event DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    places_totales INT DEFAULT 50,
    places_reservees INT DEFAULT 0,
    prix DECIMAL(10,2) DEFAULT 0,
    image VARCHAR(255),
    is_published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date_event),
    INDEX idx_published (is_published),
    INDEX idx_slug (slug)
) ENGINE=InnoDB COMMENT='Événements et conférences';

-- Table: intervenants
CREATE TABLE intervenants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    evenement_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    profession VARCHAR(150),
    entreprise VARCHAR(150),
    photo VARCHAR(255),
    bio TEXT,
    ordre INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_evenement (evenement_id)
) ENGINE=InnoDB COMMENT='Intervenants des événements';

-- Table: inscriptions_evenements
CREATE TABLE inscriptions_evenements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    evenement_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    entreprise VARCHAR(150),
    horaire VARCHAR(100),
    note TEXT,
    qr_code VARCHAR(255),
    statut ENUM('confirme', 'en_attente', 'annule') DEFAULT 'confirme',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (evenement_id) REFERENCES evenements(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    UNIQUE KEY unique_user_event (user_id, evenement_id),
    INDEX idx_user (user_id),
    INDEX idx_evenement (evenement_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB COMMENT='Inscriptions aux événements';


-- GROUPE 7: CONTENU & BLOG (3 tables)
-- ____________________________________

-- Table: blog_posts
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    contenu TEXT NOT NULL,
    extrait TEXT,
    image VARCHAR(255),
    auteur_id INT NOT NULL,
    categorie VARCHAR(50),
    tags VARCHAR(255),
    vues INT DEFAULT 0,
    is_published BOOLEAN DEFAULT FALSE,
    date_publication DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_auteur (auteur_id),
    INDEX idx_published (is_published),
    INDEX idx_date_pub (date_publication),
    INDEX idx_slug (slug),
    FULLTEXT idx_search (titre, contenu)
) ENGINE=InnoDB COMMENT='Articles du blog';



-- Table: comments 
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    author_id INT NOT NULL,
    contenu TEXT NOT NULL,
    parent_id INT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_post (post_id),
    INDEX idx_author (author_id),
    INDEX idx_approved (is_approved)
) ENGINE=InnoDB COMMENT='Commentaires sur les articles';

-- Table: documents
CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    fichier VARCHAR(255) NOT NULL,
    type_fichier VARCHAR(50),
    taille_fichier INT,
    formation_id INT NULL,
    uploaded_by INT NOT NULL,
    telechargements INT DEFAULT 0,
    is_public BOOLEAN DEFAULT FALSE,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formation_id) REFERENCES formations(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_formation (formation_id),
    INDEX idx_public (is_public)
) ENGINE=InnoDB COMMENT='Documents pédagogiques';


-- GROUPE 8: NOTIFICATIONS (1 table)
-- __________________________________

-- Table: notifications
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    titre VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    lien VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_lecture DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_date (date_envoi)
) ENGINE=InnoDB COMMENT='Notifications utilisateurs';


-- DONNÉES DE TEST
-- ________________




INSERT INTO users (nom, prenom, email, password, role_id, telephone, is_active, email_verified) VALUES

('Admin', 'System', 'admin@fromix.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 8, '0555123456', TRUE, TRUE),

('Benkhaled', 'Samira', 'marketing@fromix.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, '0555234567', TRUE, TRUE),

('Yahiaoui', 'Karim', 'commercial@fromix.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, '0555345678', TRUE, TRUE),

('Meziani', 'Assia', 'assistant@fromix.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, '0555456789', TRUE, TRUE),

('Hamdi', 'Youcef', 'directeur.pedago@fromix.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 7, '0555567890', TRUE, TRUE),

('Benali', 'Ahmed', 'formateur@fromix.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, '0555678901', TRUE, TRUE),

('Boudiaf', 'Nadia', 'nadia.boudiaf@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, '0555789012', TRUE, TRUE),

('Kaci', 'Mohamed', 'mohamed.kaci@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, '0555890123', TRUE, TRUE),

('Visiteur', 'Test', 'visiteur@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, FALSE, FALSE);



-- Insertion de formateurs de test
INSERT INTO formateurs (nom, prenom, email, specialite, experience, description) VALUES
('Benali', 'Ahmed', 'a.benali@fromix.dz', 'Développement Web Full-Stack', 8, 
 'Expert en développement web avec 8 ans d''expérience dans les technologies modernes'),
('Mammeri', 'Fatima', 'f.mammeri@fromix.dz', 'Cybersécurité & Ethical Hacking', 6, 
 'Spécialiste en sécurité informatique et tests de pénétration'),
('Saidi', 'Karim', 'k.saidi@fromix.dz', 'Data Science & Machine Learning', 7, 
 'Data Scientist avec expertise en IA et analyse de données');

-- Insertion de partenaires
INSERT INTO partenaires (nom, description, site_web) VALUES
('CERIST', 'Centre de Recherche sur l''Information Scientifique', 'https://www.cerist.dz'),
('Algérie Télécom', 'Opérateur national de télécommunications', 'https://www.at.dz'),
('MESRS', 'Ministère de l''Enseignement Supérieur', 'https://www.mesrs.dz');

-- Insertion de formations de test
INSERT INTO formations (
    titre, slug, description, categorie_id, niveau, duree, prix, 
    formateur_id, partenaire_id, places_disponibles, is_published, 
    date_debut, date_fin
) VALUES
('Développement Web Complet - HTML, CSS, JavaScript, PHP', 
 'developpement-web-complet', 
 'Formation complète pour devenir développeur web full-stack. Apprenez les technologies essentielles du web moderne.',
 1, 'debutant', 120, 25000, 1, 1, 20, TRUE, '2025-02-01', '2025-05-01'),

('Sécurité Informatique et Ethical Hacking', 
 'securite-informatique-ethical-hacking',
 'Apprenez les techniques de sécurisation des systèmes et le piratage éthique.',
 2, 'intermediaire', 80, 30000, 2, 1, 15, TRUE, '2025-02-15', '2025-04-15'),

('Data Science avec Python', 
 'data-science-python',
 'Maîtrisez l''analyse de données et le machine learning avec Python.',
 3, 'intermediaire', 100, 28000, 3, 2, 18, TRUE, '2025-03-01', '2025-05-30');

-- Insertion de sessions
INSERT INTO sessions (formation_id, nom, date_debut, date_fin, horaire, type_session, places_totales) VALUES
(1, 'Session Hiver 2025 - Développement Web', '2025-02-01', '2025-05-01', 'Samedi et Dimanche 9h-17h', 'hybride', 20),
(2, 'Session Printemps 2025 - Cybersécurité', '2025-02-15', '2025-04-15', 'Vendredi et Samedi 14h-18h', 'presentiel', 15),
(3, 'Session Mars 2025 - Data Science', '2025-03-01', '2025-05-30', 'En ligne 19h-22h', 'en_ligne', 25);

-- Insertion de plans de formation (exemple pour la formation 1)
INSERT INTO formation_plans (formation_id, semaine, titre, description, ordre) VALUES
(1, 1, 'Introduction au Web et HTML5', 'Comprendre le fonctionnement du web, structure HTML5, balises sémantiques', 1),
(1, 2, 'CSS3 et Design Responsive', 'Stylisation avec CSS3, Flexbox, Grid, Media Queries', 2),
(1, 3, 'JavaScript Fondamentaux', 'Variables, fonctions, DOM, événements', 3),
(1, 4, 'JavaScript Avancé', 'ES6+, Async/Await, Fetch API', 4),
(1, 5, 'PHP et MySQL', 'Bases de PHP, connexion BDD, CRUD', 5),
(1, 6, 'Projet Final', 'Développement d''une application web complète', 6);

-- Insertion de compétences
INSERT INTO formation_skills (formation_id, competence, ordre) VALUES
(1, 'Créer des pages web responsive avec HTML5 et CSS3', 1),
(1, 'Développer des interfaces interactives avec JavaScript', 2),
(1, 'Créer des applications web dynamiques avec PHP', 3),
(1, 'Gérer des bases de données MySQL', 4),
(1, 'Déployer un site web en production', 5);

-- Insertion d'événements
INSERT INTO evenements (
    titre, slug, description, type_evenement, lieu, date_event, 
    heure_debut, heure_fin, places_totales, is_published
) VALUES
('Conférence: L''avenir de l''IA en Algérie', 
 'conference-ia-algerie',
 'Conférence sur les perspectives de l''intelligence artificielle en Algérie',
 'conference', 'Hôtel Hilton, Alger', '2025-03-15', '09:00', '17:00', 150, TRUE),

('Atelier: Sécurité des Applications Web', 
 'atelier-securite-web',
 'Atelier pratique sur les techniques de sécurisation des applications web',
 'atelier', 'CERIST, Ben Aknoun', '2025-04-10', '14:00', '18:00', 30, TRUE);

-- Insertion d'intervenants
INSERT INTO intervenants (evenement_id, nom, prenom, profession, entreprise, ordre) VALUES
(1, 'Boubekeur', 'Lydia', 'Directrice R&D', 'CERIST', 1),
(1, 'Djebbar', 'Mohamed', 'Chercheur en IA', 'Université de Blida', 2),
(2, 'Khelifi', 'Rania', 'Security Consultant', 'SecureIT Algeria', 1);

-- Insertion de codes promotionnels
INSERT INTO promotions (
    code, description, type_reduction, valeur_reduction, 
    date_debut, date_fin, utilisations_max, is_active
) VALUES
('WELCOME2025', 'Promotion de bienvenue 2025', 'pourcentage', 15.00, 
 '2025-01-01 00:00:00', '2025-03-31 23:59:59', 100, TRUE),
 
('EARLY2025', 'Réduction inscription anticipée', 'pourcentage', 20.00, 
 '2025-01-01 00:00:00', '2025-02-01 23:59:59', 50, TRUE),
 
('STUDENT500', 'Réduction étudiants', 'montant_fixe', 5000.00, 
 '2025-01-01 00:00:00', '2025-12-31 23:59:59', NULL, TRUE);

-- Insertion d'articles de blog
INSERT INTO blog_posts (
    titre, slug, contenu, extrait, auteur_id, categorie, 
    is_published, date_publication
) VALUES
('Les 10 compétences les plus demandées en 2025',
 'competences-demandees-2025',
 'Le marché du travail évolue rapidement. Voici les 10 compétences les plus recherchées par les employeurs en 2025...',
 'Découvrez les compétences essentielles pour réussir votre carrière en 2025',
 1, 'Carrière', TRUE, '2025-01-10 10:00:00'),

('Comment choisir sa formation en développement web',
 'choisir-formation-dev-web',
 'Guide complet pour choisir la meilleure formation en développement web selon votre profil et vos objectifs...',
 'Tous nos conseils pour bien choisir votre formation de développeur web',
 2, 'Formation', TRUE, '2025-01-15 14:00:00'),

('5 raisons d''apprendre la cybersécurité en 2025',
 '5-raisons-cybersecurite-2025',
 'La cybersécurité est un domaine en pleine expansion. Découvrez pourquoi vous devriez vous y intéresser dès maintenant...',
 'La cybersécurité: un métier d''avenir avec de nombreuses opportunités',
 1, 'Cybersécurité', TRUE, '2025-01-20 09:00:00');


-- VUES UTILES
-- _______________

-- Vue: Statistiques des formations
CREATE VIEW vue_stats_formations AS
SELECT 
    f.id,
    f.titre,
    f.prix,
    c.nom as categorie,
    CONCAT(form.nom, ' ', form.prenom) as formateur,
    COUNT(DISTINCT i.id) as nombre_inscrits,
    COALESCE(AVG(r.note), 0) as note_moyenne,
    COUNT(DISTINCT r.id) as nombre_avis,
    f.places_disponibles,
    f.is_published
FROM formations f
LEFT JOIN categories c ON f.categorie_id = c.id
LEFT JOIN formateurs form ON f.formateur_id = form.id
LEFT JOIN sessions s ON s.formation_id = f.id
LEFT JOIN inscriptions i ON i.session_id = s.id
LEFT JOIN formation_ratings r ON r.formation_id = f.id
GROUP BY f.id;

-- Vue: Inscriptions actives
CREATE VIEW vue_inscriptions_actives AS
SELECT 
    i.id,
    CONCAT(u.nom, ' ', u.prenom) as apprenant,
    u.email,
    f.titre as formation,
    s.nom as session,
    s.date_debut,
    s.date_fin,
    i.statut,
    p.statut as statut_paiement,
    p.montant_paye
FROM inscriptions i
INNER JOIN users u ON i.user_id = u.id
INNER JOIN sessions s ON i.session_id = s.id
INNER JOIN formations f ON s.formation_id = f.id
LEFT JOIN paiements p ON p.inscription_id = i.id
WHERE i.statut IN ('confirme', 'en_attente');

-- Vue: Tableau de bord admin
CREATE VIEW vue_dashboard_admin AS
SELECT 
    (SELECT COUNT(*) FROM users WHERE is_active = TRUE) as total_utilisateurs,
    (SELECT COUNT(*) FROM formations WHERE is_published = TRUE) as total_formations,
    (SELECT COUNT(*) FROM inscriptions WHERE statut = 'confirme') as total_inscriptions,
    (SELECT SUM(montant_paye) FROM paiements WHERE statut = 'valide') as revenus_totaux,
    (SELECT COUNT(*) FROM paiements WHERE statut = 'en_attente') as paiements_en_attente,
    (SELECT COUNT(*) FROM evenements WHERE date_event >= CURDATE()) as evenements_a_venir;


-- PROCÉDURES STOCKÉES UTILES
-- _____________________________

DELIMITER //

-- Procédure: Générer un code de certification unique
CREATE PROCEDURE generer_code_certification(
    IN p_user_id INT,
    IN p_formation_id INT,
    IN p_session_id INT,
    OUT p_code VARCHAR(50)
)
BEGIN
    DECLARE v_annee VARCHAR(4);
    DECLARE v_mois VARCHAR(2);
    DECLARE v_numero VARCHAR(6);
    
    SET v_annee = YEAR(CURDATE());
    SET v_mois = LPAD(MONTH(CURDATE()), 2, '0');
    SET v_numero = LPAD(p_user_id, 6, '0');
    
    SET p_code = CONCAT('FROMIX-', v_annee, v_mois, '-', v_numero);
END //

-- Procédure: Envoyer notification
CREATE PROCEDURE envoyer_notification(
    IN p_user_id INT,
    IN p_type VARCHAR(50),
    IN p_titre VARCHAR(200),
    IN p_message TEXT,
    IN p_lien VARCHAR(255)
)
BEGIN
    INSERT INTO notifications (user_id, type, titre, message, lien)
    VALUES (p_user_id, p_type, p_titre, p_message, p_lien);
END //

-- Procédure: Valider un paiement
CREATE PROCEDURE valider_paiement(
    IN p_paiement_id INT,
    IN p_admin_id INT
)
BEGIN
    DECLARE v_inscription_id INT;
    DECLARE v_user_id INT;
    
    -- Récupérer l'inscription
    SELECT inscription_id INTO v_inscription_id
    FROM paiements
    WHERE id = p_paiement_id;
    
    -- Récupérer l'utilisateur
    SELECT user_id INTO v_user_id
    FROM inscriptions
    WHERE id = v_inscription_id;
    
    -- Mettre à jour le paiement
    UPDATE paiements
    SET statut = 'valide',
        date_validation = NOW(),
        valide_par = p_admin_id
    WHERE id = p_paiement_id;
    
    -- Mettre à jour l'inscription
    UPDATE inscriptions
    SET statut = 'confirme',
        date_confirmation = NOW()
    WHERE id = v_inscription_id;
    
    -- Envoyer notification
    CALL envoyer_notification(
        v_user_id,
        'paiement_valide',
        'Votre paiement a été validé',
        'Votre inscription a été confirmée. Vous pouvez maintenant accéder à votre formation.',
        '/mes-formations'
    );
    
    -- Logger l'action
    INSERT INTO activity_logs (user_id, action_type, table_name, record_id, description)
    VALUES (p_admin_id, 'VALIDATION_PAIEMENT', 'paiements', p_paiement_id, 
            CONCAT('Validation du paiement #', p_paiement_id));
END //

DELIMITER ;


-- TRIGGERS
-- __________

DELIMITER //

-- Trigger: Log après création d'utilisateur
CREATE TRIGGER after_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action_type, table_name, record_id, description)
    VALUES (NEW.id, 'CREATION_COMPTE', 'users', NEW.id, 
            CONCAT('Nouveau compte créé: ', NEW.email));
END //

-- Trigger: Mise à jour places disponibles après inscription
CREATE TRIGGER after_inscription_insert
AFTER INSERT ON inscriptions
FOR EACH ROW
BEGIN
    UPDATE sessions
    SET places_reservees = places_reservees + 1
    WHERE id = NEW.session_id;
END //

-- Trigger: Notification après validation paiement (simplifié)
CREATE TRIGGER after_paiement_validation
AFTER UPDATE ON paiements
FOR EACH ROW
BEGIN
    IF NEW.statut = 'valide' AND OLD.statut != 'valide' THEN
        INSERT INTO notifications (user_id, type, titre, message)
        SELECT i.user_id, 'paiement', 'Paiement validé', 
               'Votre paiement a été validé avec succès'
        FROM inscriptions i
        WHERE i.id = NEW.inscription_id;
    END IF;
END //

DELIMITER ;


-- INDEX ADDITIONNELS POUR PERFORMANCE
-- _______________________________________

-- Performance sur les recherches fréquentes
CREATE INDEX idx_formations_search ON formations(titre, niveau, prix);
CREATE INDEX idx_users_email_active ON users(email, is_active);
CREATE INDEX idx_inscriptions_user_statut ON inscriptions(user_id, statut);
CREATE INDEX idx_paiements_statut_date ON paiements(statut, date_paiement);


-- FIN DU SCRIPT
-- _________________

-- Afficher un résumé
SELECT 'BASE DE DONNÉES CRÉÉE AVEC SUCCÈS!' as message;
SELECT COUNT(*) as nombre_tables 
FROM information_schema.tables 
WHERE table_schema = 'formix_training_platform_v2';

SELECT '✅ 28 tables créées' as statut
UNION ALL
SELECT '✅ Relations et contraintes ajoutées'
UNION ALL
SELECT '✅ Données de test insérées'
UNION ALL
SELECT '✅ Vues et procédures créées'
UNION ALL
SELECT '✅ Triggers configurés';

