create table `EmployeeLeaveLog` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`employee_leave` bigint(20) NOT NULL,
	`user_id` bigint(20) NULL,
	`data` varchar(500) NOT NULL,
	`status_from` enum('Approved','Pending','Rejected') default 'Pending',
	`status_to` enum('Approved','Pending','Rejected') default 'Pending',
	`created` timestamp default '0000-00-00 00:00:00',
	CONSTRAINT `Fk_EmployeeLeaveLog_EmployeeLeaves` FOREIGN KEY (`employee_leave`) REFERENCES `EmployeeLeaves` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `Fk_EmployeeLeaveLog_Users` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	primary key  (`id`)
) engine=innodb default charset=utf8;

REPLACE INTO `Settings` (`name`, `value`, `description`, `meta`) VALUES
('Leave: Share Calendar to Whole Company', '0', '','["value", {"label":"Value","type":"select","source":[["1","Yes"],["0","No"]]}]');

UPDATE `Settings` set value = '1' where name = 'System: Reset Modules and Permissions';

ALTER TABLE  `Country` CHANGE  `namecap`  `namecap` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT  '';

ALTER TABLE  `EmployeeProjects` CHANGE  `date_start`  `date_start` DATE NULL;
ALTER TABLE  `EmployeeProjects` CHANGE  `date_end`  `date_end` DATE NULL;

ALTER TABLE  `EmployeeCompanyLoans` ADD  `currency` BIGINT( 20 ) NULL DEFAULT NULL AFTER  `period_months`;