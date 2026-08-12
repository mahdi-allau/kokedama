-- ---------------------------------------------------------
-- Kokedama Component - Install SQL
-- MySQL 8.0+ / utf8mb4_unicode_ci
-- ---------------------------------------------------------

-- Table: Services
CREATE TABLE IF NOT EXISTS `#__kokedama_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` mediumtext,
  `short_desc` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `image` varchar(255) DEFAULT NULL,
  `gallery` text COMMENT 'JSON array of image paths',
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) unsigned DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(500) DEFAULT NULL,
  `checked_out` int(11) unsigned DEFAULT 0,
  `checked_out_time` datetime DEFAULT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL DEFAULT '*',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `idx_published` (`published`),
  KEY `idx_featured` (`featured`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Projects / Portfolio
CREATE TABLE IF NOT EXISTS `#__kokedama_projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` mediumtext,
  `materials` varchar(255) DEFAULT NULL,
  `plant_type` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gallery` text COMMENT 'JSON array of image paths',
  `service_id` int(11) unsigned DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) unsigned DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(500) DEFAULT NULL,
  `checked_out` int(11) unsigned DEFAULT 0,
  `checked_out_time` datetime DEFAULT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL DEFAULT '*',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `idx_published` (`published`),
  KEY `idx_featured` (`featured`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Gallery
CREATE TABLE IF NOT EXISTS `#__kokedama_gallery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'general' COMMENT 'general, kokedama, workshop, event, atelier',
  `description` text,
  `project_id` int(11) unsigned DEFAULT NULL,
  `event_id` int(11) unsigned DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL DEFAULT '*',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `idx_published` (`published`),
  KEY `idx_category` (`category`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Events & Workshops
CREATE TABLE IF NOT EXISTS `#__kokedama_events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` mediumtext,
  `event_type` enum('workshop','evento','corso') NOT NULL DEFAULT 'workshop',
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `max_participants` int(11) NOT NULL DEFAULT 10,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) unsigned DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(500) DEFAULT NULL,
  `checked_out` int(11) unsigned DEFAULT 0,
  `checked_out_time` datetime DEFAULT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT 0,
  `language` char(7) NOT NULL DEFAULT '*',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `idx_published` (`published`),
  KEY `idx_featured` (`featured`),
  KEY `idx_event_date` (`event_date`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Bookings
CREATE TABLE IF NOT EXISTS `#__kokedama_bookings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `booking_type` enum('servizio','evento') NOT NULL DEFAULT 'servizio',
  `service_id` int(11) unsigned DEFAULT NULL,
  `event_id` int(11) unsigned DEFAULT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time DEFAULT NULL,
  `participants` int(11) NOT NULL DEFAULT 1,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `notes` text,
  `status` enum('pending','confirmed','rejected','cancelled','completed') NOT NULL DEFAULT 'pending',
  `admin_notes` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `consent_gdpr` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(11) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_booking_type` (`booking_type`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_booking_date` (`booking_date`),
  KEY `idx_email` (`email`),
  KEY `idx_created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Messages / Contacts
CREATE TABLE IF NOT EXISTS `#__kokedama_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `ip_address` varchar(45) DEFAULT NULL,
  `consent_gdpr` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` datetime DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data: Services
INSERT INTO `#__kokedama_services` (`id`, `title`, `alias`, `description`, `short_desc`, `price`, `duration`, `image`, `published`, `featured`, `ordering`, `created`, `created_by`, `meta_title`, `meta_desc`) VALUES
(1, 'Kokedama Classica', 'kokedama-classica', '<p>Una palla di muschio con una pianta scelta, perfetta per arredare ogni ambiente con eleganza naturale.</p>', 'Creazione artigianale di kokedama con pianta a scelta', 35.00, 60, 'images/kokedama/services/kokedama-classica.jpg', 1, 1, 1, NOW(), 0, 'Kokedama Classica - Kokedama & Sculture Naturali Ferrara', 'Kokedama classica realizzata a mano a Ferrara. Pianta naturale in sfera di muschio, perfetta come arredo o regalo.'),
(2, 'Kokedama Sospesa', 'kokedama-sospesa', '<p>Kokedama pensata per essere appesa, creando un giardino verticale unico e affascinante.</p>', 'Kokedama da appendere per creare un giardino sospeso', 45.00, 90, 'images/kokedama/services/kokedama-sospesa.jpg', 1, 1, 2, NOW(), 0, 'Kokedama Sospesa - Kokedama & Sculture Naturali Ferrara', 'Kokedama sospesa da appendere. Sculture vegetali pensili per interni ed esterni a Ferrara.'),
(3, 'Laboratorio Kokedama', 'laboratorio-kokedama', '<p>Partecipa al nostro workshop e impara a creare la tua kokedama guidato dalla nostra esperta. Include tutti i materiali.</p>', 'Workshop pratico per imparare l\'arte del kokedama', 55.00, 150, 'images/kokedama/services/laboratorio.jpg', 1, 1, 3, NOW(), 0, 'Laboratorio Kokedama - Workshop a Ferrara', 'Partecipa al laboratorio kokedama a Ferrara. Impara a creare la tua sfera di muschio con tutti i materiali inclusi.'),
(4, 'Composizione Personalizzata', 'composizione-personalizzata', '<p>Realizziamo composizioni su misura per eventi, regali aziendali o arredamenti specifici. Contattaci per un preventivo.</p>', 'Composizioni vegetali personalizzate su richiesta', NULL, NULL, 'images/kokedama/services/composizione.jpg', 1, 0, 4, NOW(), 0, 'Composizioni Personalizzate - Kokedama Ferrara', 'Composizioni kokedama personalizzate per eventi, regali e arredamenti a Ferrara.');

-- Sample Data: Projects
INSERT INTO `#__kokedama_projects` (`id`, `title`, `alias`, `description`, `materials`, `plant_type`, `image`, `published`, `featured`, `ordering`, `created`, `created_by`, `meta_title`, `meta_desc`) VALUES
(1, 'Kokedama Ficus Ginseng', 'kokedama-ficus-ginseng', '<p>Una bellissima realizzazione con Ficus Ginseng, pianta simbolo di longevit\u00e0 e buona fortuna.</p>', 'Muschio vivo, filo di juta, terriccio specifico', 'Ficus Ginseng', 'images/kokedama/projects/ficus-ginseng.jpg', 1, 1, 1, NOW(), 0, 'Progetto Ficus Ginseng - Kokedama Ferrara', 'Kokedama con Ficus Ginseng realizzata artigianalmente a Ferrara.'),
(2, 'Giardino Sospeso', 'giardino-sospeso', '<p>Composizione di tre kokedame sospese a diverse altezze per creare un effetto cascata verde.</p>', 'Muschio, spago naturale, ghiaia decorativa', 'Pothos, Felce, Filodendro', 'images/kokedama/projects/giardino-sospeso.jpg', 1, 1, 2, NOW(), 0, 'Giardino Sospeso - Composizione Kokedama Ferrara', 'Composizione di kokedame sospese a Ferrara. Giardino verticale artigianale.'),
(3, 'Centrotavola Evento', 'centrotavola-evento', '<p>Centrotavola realizzato per un matrimonio in stile rustico-naturale, con kokedame e candele.</p>', 'Muschio, tessuto naturale, base legno', 'Piante grasse assortite', 'images/kokedama/projects/centrotavola.jpg', 1, 0, 3, NOW(), 0, 'Centrotavola Kokedama - Eventi Ferrara', 'Centrotavola con kokedama per eventi e matrimoni a Ferrara.');

-- Sample Data: Events
INSERT INTO `#__kokedama_events` (`id`, `title`, `alias`, `description`, `event_type`, `event_date`, `event_time`, `end_time`, `location`, `max_participants`, `price`, `image`, `published`, `featured`, `ordering`, `created`, `created_by`, `meta_title`, `meta_desc`) VALUES
(1, 'Workshop Kokedama Base', 'workshop-kokedama-base', '<p>Workshop introduttivo all\'arte del kokedama. Imparerai a creare la tua prima palla di muschio con una pianta a scelta. Tutti i materiali sono inclusi.</p>', 'workshop', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '15:00:00', '17:30:00', 'Piazza della Repubblica 11, Ferrara', 8, 55.00, 'images/kokedama/events/workshop-base.jpg', 1, 1, 1, NOW(), 0, 'Workshop Kokedama Base - Ferrara', 'Workshop introduttivo kokedama a Ferrara. Impara a creare la tua palla di muschio.'),
(2, 'Kokedama e Aperitivo', 'kokedama-e-aperitivo', '<p>Una serata speciale dedicata alla creativit\u00e0 naturale. Crea la tua kokedama e brinda con un aperitivo bio.</p>', 'evento', DATE_ADD(CURDATE(), INTERVAL 21 DAY), '18:30:00', '21:00:00', 'Piazza della Repubblica 11, Ferrara', 12, 45.00, 'images/kokedama/events/aperitivo.jpg', 1, 1, 2, NOW(), 0, 'Kokedama e Aperitivo - Evento Ferrara', 'Serata creativa con kokedama e aperitivo bio a Ferrara.'),
(3, 'Corso Avanzato Terrari', 'corso-avanzato-terrari', '<p>Corso di due incontri per imparare a creare terrari chiusi e aperti con diverse tipologie di piante.</p>', 'corso', DATE_ADD(CURDATE(), INTERVAL 30 DAY), '16:00:00', '19:00:00', 'Piazza della Repubblica 11, Ferrara', 6, 90.00, 'images/kokedama/events/corso-terrari.jpg', 1, 0, 3, NOW(), 0, 'Corso Avanzato Terrari - Ferrara', 'Corso avanzato per creare terrari chiusi e aperti a Ferrara.');

-- Sample Data: Gallery
INSERT INTO `#__kokedama_gallery` (`id`, `title`, `alias`, `image`, `thumbnail`, `category`, `description`, `project_id`, `published`, `ordering`, `created`) VALUES
(1, 'Kokedama Classica Verde', 'kokedama-classica-verde', 'images/kokedama/gallery/kokedama-classica-1.jpg', 'images/kokedama/gallery/thumbs/kokedama-classica-1.jpg', 'kokedama', 'Kokedama con felce', 1, 1, 1, NOW()),
(2, 'Kokedama Sospesa', 'kokedama-sospesa-1', 'images/kokedama/gallery/kokedama-sospesa-1.jpg', 'images/kokedama/gallery/thumbs/kokedama-sospesa-1.jpg', 'kokedama', 'Kokedama da appendere', 2, 1, 2, NOW()),
(3, 'Workshop in corso', 'workshop-in-corso', 'images/kokedama/gallery/workshop-1.jpg', 'images/kokedama/gallery/thumbs/workshop-1.jpg', 'workshop', 'Momenti del laboratorio', NULL, 1, 3, NOW()),
(4, 'Dettaglio muschio', 'dettaglio-muschio', 'images/kokedama/gallery/dettaglio-1.jpg', 'images/kokedama/gallery/thumbs/dettaglio-1.jpg', 'general', 'Dettaglio della sfera di muschio', NULL, 1, 4, NOW()),
(5, 'Atelier', 'atelier-1', 'images/kokedama/gallery/atelier-1.jpg', 'images/kokedama/gallery/thumbs/atelier-1.jpg', 'atelier', 'Il nostro atelier a Ferrara', NULL, 1, 5, NOW());
