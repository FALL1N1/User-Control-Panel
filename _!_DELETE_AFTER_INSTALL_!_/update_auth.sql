-- Comment it -- if not 1st time use this SQL
-- DO NOT FORGET CHANGE TO "0" TO WHICH REALM YOU DON`T WANT MAKE AVAIBLE TRANSFER
ALTER TABLE `realmlist` ADD COLUMN `TransferAvailable` INT(1) DEFAULT 1 NULL AFTER `gamebuild`;

DROP TABLE IF EXISTS `account_transfer`;
CREATE TABLE `account_transfer` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Migration ID',
  `cStatus` BOOL DEFAULT NULL,
  `cDump` TEXT,
  `cNameOLD` CHAR(16) NOT NULL DEFAULT '',
  `cNameNEW` CHAR(16) NOT NULL DEFAULT '', 
  `cAccount` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `cRealm` INT(2) UNSIGNED NOT NULL DEFAULT 1,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_checked` TIMESTAMP DEFAULT 0,
  `oPassword` CHAR(40) NOT NULL DEFAULT '',
  `oAccount` CHAR(16) NOT NULL DEFAULT '',
  `oServer` TEXT,
  `oRealm` TEXT,
  `oRealmlist` TEXT,
  `GUID` INT(11) UNSIGNED NOT NULL DEFAULT 0,
   PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `account_transfer` ADD COLUMN `cItemRow` TEXT AFTER `date_checked`;
ALTER TABLE `account_transfer` ADD COLUMN `gmAccount` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `cAccount`;
ALTER TABLE `account_transfer` CHANGE `oRealm` `oRealm` VARCHAR(32);
-- FOR COMMENTS
ALTER TABLE `account_transfer` ADD COLUMN `Reason` TEXT AFTER `GUID`;
UPDATE `account_transfer` SET `Reason` = "Not meet requirements";
--
ALTER TABLE `account_transfer` CHANGE `cDump` `cDump` MEDIUMTEXT;
ALTER TABLE `account_transfer` CHANGE `cItemRow` `cItemRow` MEDIUMTEXT;
--
ALTER TABLE `account_transfer` CHANGE `oPassword` `oPassword` VARCHAR(255);
-- OPTION: ONE CHARNAME PER 1 REALMNAME, for disable:
/* ALTER TABLE `account_transfer` ENGINE = INNODB;
CREATE UNIQUE INDEX `idx_name_realm` ON `account_transfer`(`cNameOLD`,`oRealm`); */

DROP TABLE IF EXISTS `account_transfer_blacklist`;
CREATE TABLE `account_transfer_blacklist` (
  `b_address` CHAR(255) NOT NULL DEFAULT '',
   PRIMARY KEY (`b_address`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
-- EXAMPLE HOW TO FEEL BLACKLIST
INSERT INTO `account_transfer_blacklist` VALUES ("localhost"),("127.0.0.1");
-- Delete not existed gm acccess
DELETE FROM `account_access` WHERE `id` NOT IN (SELECT `id` FROM `account`);
-- NOT NEED NOW!
DROP TABLE IF EXISTS `account_transfer_queue`;

DROP TABLE IF EXISTS `account_transfer_guid`;
CREATE TABLE `account_transfer_guid` (
  `RealmID` INT(11) NOT NULL DEFAULT 0,
  `GUID` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`RealmID`)
) ENGINE=MYISAM DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `account_details`;
CREATE TABLE `account_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ACC ID',
  `myth_coins` INT(11) NOT NULL DEFAULT 0,
   PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

DROP TABLE IF EXISTS `account_billing_history`;
CREATE TABLE `account_billing_history` (
  `id` INT(11) NOT NULL DEFAULT 0,
  `action` INT(3) NOT NULL DEFAULT 0,
  `text` VARCHAR(255) NOT NULL DEFAULT '',
  `charName` VARCHAR(12) NOT NULL DEFAULT '',
  `charGUID` INT(11) NOT NULL DEFAULT 0,
  `realmName` VARCHAR(32) NOT NULL DEFAULT '',
  `realmID` INT(3) UNSIGNED NOT NULL DEFAULT 0,
  `whenItDone` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `myth_coins_spend` INT(11) NOT NULL DEFAULT 0,
  `myth_coins_balance` INT(11) NOT NULL DEFAULT 0,
  `IP` varchar(32) NOT NULL default '127.0.0.1'
) ENGINE=MYISAM DEFAULT CHARSET = utf8;

-- ALTER TABLE `account_billing_history` DROP COLUMN `id`;
-- ALTER TABLE `account_billing_history` ADD COLUMN `myth_coins_balance` INT(11) NOT NULL DEFAULT 0 AFTER `myth_coins_spend`;
-- ALTER TABLE `account_billing_history` CHANGE `AccountID` `id` INT(11) NOT NULL DEFAULT 0;