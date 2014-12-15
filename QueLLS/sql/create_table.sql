SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `smartplug` DEFAULT CHARACTER SET latin5 ;
USE `smartplug` ;

-- -----------------------------------------------------
-- Table `smartplug`.`plug`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `smartplug`.`plug` ;

CREATE  TABLE IF NOT EXISTS `smartplug`.`plug` (
  `id_plug` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL COMMENT 'Nom de la prise' ,
  `ip_address` VARCHAR(45) NULL COMMENT 'Adresse IP de la prise' ,
  PRIMARY KEY (`id_plug`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `smartplug`.`group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `smartplug`.`group` ;

CREATE  TABLE IF NOT EXISTS `smartplug`.`group` (
  `id_group` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL COMMENT 'Nom du groupe' ,
  `f_enabled` TINYINT(1) NULL DEFAULT true COMMENT 'Groupe actif (défaut) / inactif' ,
  `f_follow_sun` TINYINT(1) NULL DEFAULT false COMMENT 'Groupe dépendant de l\'heure du lever/coucher du soleil ou non (défaut)' ,
  `f_fixed_hour` TINYINT(1) NULL DEFAULT true COMMENT 'Heure de début/fin des actions fixes (défaut) ou variable à plus ou moins x minutes' ,
  `max_offset` INT NULL COMMENT 'Nombre de minutes max en plus ou en moins par rapport à l\'heure de début/fin pourles heures variables' ,
  PRIMARY KEY (`id_group`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `smartplug`.`action`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `smartplug`.`action` ;

CREATE  TABLE IF NOT EXISTS `smartplug`.`action` (
  `id_action` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL COMMENT 'Nom del\'action (en français)' ,
  `type` VARCHAR(45) NULL COMMENT 'Type d\'action : immédiate ou planifiée' ,
  `command` VARCHAR(45) NULL COMMENT 'Nom de la commande envoyée au serveur' ,
  `parameters` TEXT NULL ,
  PRIMARY KEY (`id_action`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `smartplug`.`plan`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `smartplug`.`plan` ;

CREATE  TABLE IF NOT EXISTS `smartplug`.`plan` (
  `id_plan` INT NOT NULL AUTO_INCREMENT ,
  `id_group` INT NOT NULL COMMENT 'Groupe d\'appartenance' ,
  `id_plug` INT NOT NULL ,
  `time_slot` INT NULL COMMENT 'Emplacement horaire alloué à l\'action (de 1 à 4)' ,
  `id_action` INT NOT NULL ,
  `dow_monday` TINYINT(1) NULL DEFAULT false COMMENT 'Vaut 1 si l\'action est active le lundi' ,
  `dow_tuesday` TINYINT(1) NULL DEFAULT false COMMENT 'Vaut 1 si l\'action est active le mardi' ,
  `dow_wednesday` TINYINT(1) NULL DEFAULT false COMMENT 'Vaut 1 si l\'action est active le mercredi' ,
  `dow_thursday` TINYINT(1) NULL DEFAULT false COMMENT 'Vaut 1 si l\'action est active le jeudi' ,
  `dow_friday` TINYINT(1) NULL DEFAULT false COMMENT 'Vaut 1 si l\'action est active le vendredi' ,
  `dow_saturday` TINYINT(1) NULL DEFAULT false COMMENT 'Vaut 1 si l\'action est active le samedi' ,
  `dow_sunday` TINYINT(1) NULL COMMENT 'Vaut 1 si l\'action est active le dimanche' ,
  `start_time` TIME NULL COMMENT 'Heure de début' ,
  `end_time` TIME NULL COMMENT 'Heure de fin' ,
  PRIMARY KEY (`id_plan`) ,
  INDEX `fk_plan_group1_idx` (`id_group` ASC) ,
  INDEX `fk_plan_plug1_idx` (`id_plug` ASC) ,
  INDEX `fk_plan_action1_idx` (`id_action` ASC) ,
  CONSTRAINT `fk_plan_group1`
    FOREIGN KEY (`id_group` )
    REFERENCES `smartplug`.`group` (`id_group` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plan_plug1`
    FOREIGN KEY (`id_plug` )
    REFERENCES `smartplug`.`plug` (`id_plug` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plan_action1`
    FOREIGN KEY (`id_action` )
    REFERENCES `smartplug`.`action` (`id_action` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `smartplug`.`state_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `smartplug`.`state_history` ;

CREATE  TABLE IF NOT EXISTS `smartplug`.`state_history` (
  `id_state_history` INT NOT NULL AUTO_INCREMENT ,
  `id_plug` INT NOT NULL COMMENT 'Identifiant de la prise' ,
  `record_date` DATETIME NULL COMMENT 'Date d\'enregistrement' ,
  `intensity` DOUBLE NULL COMMENT 'Intensité instantanée, en Ampères (A)' ,
  `voltage` DOUBLE NULL COMMENT 'Tension instatanée, en Vols (V)' ,
  `power` DOUBLE NULL COMMENT 'Puissance instantanée, en Watts (W)' ,
  `agr_power` DOUBLE NULL COMMENT 'Puissance cumulée (depuis le dernier reset de la prise), en Watts (W)' ,
  PRIMARY KEY (`id_state_history`) ,
  INDEX `fk_state_history_plug1_idx` (`id_plug` ASC) ,
  INDEX `INDEX_1` (`id_plug` ASC, `record_date` ASC) ,
  CONSTRAINT `fk_state_history_plug1`
    FOREIGN KEY (`id_plug` )
    REFERENCES `smartplug`.`plug` (`id_plug` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `smartplug` ;

-- -----------------------------------------------------
-- Placeholder table for view `smartplug`.`group_plugs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `smartplug`.`group_plugs` (`group_name` INT, `plug_name` INT, `ip_address` INT);

-- -----------------------------------------------------
-- Placeholder table for view `smartplug`.`plug_plan`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `smartplug`.`plug_plan` (`plug_name` INT, `ip_address` INT, `time_slot` INT, `action_name` INT, `start_time` INT, `end_time` INT);

-- -----------------------------------------------------
-- Placeholder table for view `smartplug`.`action_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `smartplug`.`action_type` (`"immediate"` INT);

-- -----------------------------------------------------
-- Placeholder table for view `smartplug`.`time_slot`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `smartplug`.`time_slot` (`slot` INT);

-- -----------------------------------------------------
-- Placeholder table for view `smartplug`.`exec_plan`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `smartplug`.`exec_plan` (`plug_name` INT, `ip_address` INT, `command` INT, `parameters` INT, `time_slot` INT, `days_of_week` INT, `start_time` INT, `start_time_hr` INT, `start_time_mn` INT, `end_time` INT, `end_time_hr` INT, `end_time_mn` INT, `f_enabled` INT, `f_follow_sun` INT, `f_fixed_hour` INT, `max_offset` INT);

-- -----------------------------------------------------
-- View `smartplug`.`group_plugs`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `smartplug`.`group_plugs` ;
DROP TABLE IF EXISTS `smartplug`.`group_plugs`;
USE `smartplug`;
CREATE  OR REPLACE VIEW `smartplug`.`group_plugs` AS
SELECT DISTINCT 
	G.name as group_name,
	S.name as plug_name,
	S.ip_address
FROM (`group` G 
	 LEFT JOIN `plan` P ON (G.id_group = P.id_group))
	 LEFT JOIN `plug` S ON (P.id_plug = S.id_plug)

;

-- -----------------------------------------------------
-- View `smartplug`.`plug_plan`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `smartplug`.`plug_plan` ;
DROP TABLE IF EXISTS `smartplug`.`plug_plan`;
USE `smartplug`;
CREATE  OR REPLACE VIEW `smartplug`.`plug_plan` AS
SELECT
	S.name as plug_name,
	S.ip_address,
	P.time_slot,
	A.name as action_name,
-- 	P.days_of_week,
	P.start_time,
	P.end_time
FROM `plug` S
	LEFT JOIN `plan` P ON (S.id_plug = P.id_plug)
	LEFT JOIN `action` A ON (P.id_action = A.id_action)
;

-- -----------------------------------------------------
-- View `smartplug`.`action_type`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `smartplug`.`action_type` ;
DROP TABLE IF EXISTS `smartplug`.`action_type`;
USE `smartplug`;
CREATE  OR REPLACE VIEW `smartplug`.`action_type` AS
SELECT "immediate"
UNION
SELECT "planned"
;

-- -----------------------------------------------------
-- View `smartplug`.`time_slot`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `smartplug`.`time_slot` ;
DROP TABLE IF EXISTS `smartplug`.`time_slot`;
USE `smartplug`;
CREATE  OR REPLACE VIEW `smartplug`.`time_slot` AS
SELECT 1 AS `slot`
UNION
SELECT 2
UNION
SELECT 3
UNION
SELECT 4
;

-- -----------------------------------------------------
-- View `smartplug`.`exec_plan`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `smartplug`.`exec_plan` ;
DROP TABLE IF EXISTS `smartplug`.`exec_plan`;
USE `smartplug`;
CREATE OR REPLACE VIEW `smartplug`.`exec_plan` AS
SELECT
	S.name as plug_name,
	S.ip_address,
	A.command as command,
	A.parameters,
	P.time_slot,
	CONCAT(
	P.dow_sunday, 
	P.dow_monday, 
	P.dow_tuesday, 
	P.dow_wednesday, 
	P.dow_thursday, 
	P.dow_friday, 
	P.dow_saturday
	) as days_of_week,
	P.start_time,
	DATE_FORMAT(P.start_time, "%H") AS start_time_hr,
	DATE_FORMAT(P.start_time, "%i") AS start_time_mn,
	P.end_time,
	DATE_FORMAT(P.end_time, "%H") AS end_time_hr,
	DATE_FORMAT(P.end_time, "%i") AS end_time_mn,
	G.f_enabled,
	G.f_follow_sun,
	G.f_fixed_hour,
	G.max_offset
FROM `plug` S
	LEFT JOIN `plan` P ON (S.id_plug = P.id_plug)
	LEFT JOIN `action` A ON (P.id_action = A.id_action)
	INNER JOIN `group` G ON (P.id_group = G.id_group)
;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
