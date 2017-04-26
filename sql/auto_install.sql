DROP TABLE IF EXISTS `civicrm_apilogginglog`;

-- /*******************************************************
-- *
-- * civicrm_apilogginglog
-- *
-- * A log of incoming REST API calls
-- *
-- *******************************************************/
CREATE TABLE `civicrm_apilogginglog` (
  `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique ApiloggingLog ID',
  `time_stamp` datetime NOT NULL,
  `calling_contact_id` int unsigned  COMMENT 'FK to the id of the contact who made the API call',
  `entity` char(100),
  `action` char(100),
  `parameters` text,
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civicrm_apilogginglog_calling_contact_id
    FOREIGN KEY (`calling_contact_id`)
    REFERENCES `civicrm_contact`(`id`)
    ON DELETE SET NULL
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
