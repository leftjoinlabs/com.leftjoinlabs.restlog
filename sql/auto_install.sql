DROP TABLE IF EXISTS `civicrm_apilogginglog`;

-- /*******************************************************
-- *
-- * civicrm_apilogginglog
-- *
-- * A log of incoming REST API calls
-- *
-- *******************************************************/
CREATE TABLE `civicrm_apilogginglog` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Unique ApiloggingLog ID',
  `time_stamp`         DATETIME     NOT NULL,
  `calling_contact_id` INT UNSIGNED
  COMMENT 'FK to the id of the contact who made the API call',
  `entity`             CHAR(100),
  `action`             CHAR(100),
  `parameters`         TEXT,
  PRIMARY KEY (`id`),
  INDEX `index_time_stamp`(time_stamp),
  INDEX `index_calling_contact_id`(calling_contact_id),
  INDEX `index_entity`(entity),
  INDEX `index_action`(action),
  CONSTRAINT FK_civicrm_apilogginglog_calling_contact_id
  FOREIGN KEY (`calling_contact_id`)
  REFERENCES `civicrm_contact` (`id`)
    ON DELETE SET NULL
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci;