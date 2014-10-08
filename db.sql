CREATE TABLE `zwazo_account` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`login` VARCHAR(200) NOT NULL,
	`password` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;
-- pwd = 7conDios1A
INSERT INTO `zwazo_account`(id,login,password) VALUES(79,'aogara','ca0ba7612d2980603569bca01713226d'); 

CREATE TABLE `zwazo_tag` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`label` VARCHAR(30) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `zwazo_memo` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`cdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`mdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`title` VARCHAR(50) NOT NULL DEFAULT '0',
	`description` BLOB NULL,
	`article` BLOB NULL,
	`publicated` TINYINT(2) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `zwazo_tag_memo` (
	`id_tag` INT(11) UNSIGNED NOT NULL,
	`id_memo` INT(11) UNSIGNED NOT NULL,
	UNIQUE INDEX `Index 1` (`id_tag`, `id_memo`)
)
ENGINE=InnoDB;