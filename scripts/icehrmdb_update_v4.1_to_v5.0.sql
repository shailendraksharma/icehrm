ALTER TABLE  `Settings` ADD  `meta` text default '';



UPDATE `Settings` set meta = '' where name = 'Company: Name';
UPDATE `Settings` set meta = '["value", {"label":"Value","type":"select","source":[["1","Yes"],["0","No"]]}]' where name = 'Email: Enable';
UPDATE `Settings` set meta = '["value", {"label":"Value","type":"select","source":[["SMTP","SMTP"],["SNS","SNS"]]}]' where name = 'Email: Mode';
UPDATE `Settings` set meta = '' where name = 'Email: SMTP Host';
UPDATE `Settings` set meta = '["value", {"label":"Value","type":"select","source":[["1","Yes"],["0","No"]]}]' where name = 'Email: SMTP Authentication Required';
UPDATE `Settings` set meta = '' where name = 'Email: SMTP User';
UPDATE `Settings` set meta = '' where name = 'Email: SMTP Password';
UPDATE `Settings` set meta = '' where name = 'Email: SMTP Port';
UPDATE `Settings` set meta = '' where name = 'Email: Amazon SNS Key';
UPDATE `Settings` set meta = '' where name = 'Email: Amazone SNS Secret';
UPDATE `Settings` set meta = '' where name = 'Email: Email From';
UPDATE `Settings` set meta = '' where name = 'Instance : ID';

INSERT INTO `Settings` (`name`, `value`, `description`, `meta`) VALUES
('System: Do not pass JSON in request', '0', 'Select Yes if you are having trouble loading data for some tables','["value", {"label":"Value","type":"select","source":[["1","Yes"],["0","No"]]}]'),
('System: Reset Modules and Permissions', '1', 'Select this to reset module and permission information in Database (If you have done any changes to meta files)','["value", {"label":"Value","type":"select","source":[["1","Yes"],["0","No"]]}]');

Alter table `Users` change `user_level` `user_level` enum('Admin','Employee','Manager') default NULL;

Alter table `Modules` add column `user_levels` varchar(500) NOT NULL;

create table `Permissions` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`user_level` enum('Admin','Employee','Manager') default NULL,
	`module_id` bigint(20) NOT NULL,
	`permission` varchar(200) default null,
	`meta` varchar(500) default null,
	`value` varchar(200) default null,
	UNIQUE KEY `Module_Permission` (`user_level`,`module_id`,`permission`),
	primary key  (`id`)
) engine=innodb default charset=utf8;