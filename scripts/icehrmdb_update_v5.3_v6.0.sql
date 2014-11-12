ALTER TABLE `EmployeeTimeSheets` ADD KEY `EmployeeTimeSheets_date_end` (`date_end`);

REPLACE INTO `Settings` (`name`, `value`, `description`, `meta`) VALUES
('System: Debug Mode', '0', '','["value", {"label":"Value","type":"select","source":[["1","Yes"],["0","No"]]}]'),
('Projects: Make All Projects Available to Employees', '1', '','["value", {"label":"Value","type":"select","source":[["1","Yes"],["0","No"]]}]');

ALTER TABLE `WorkDays` ADD COLUMN `country` bigint(20) DEFAULT NULL;
ALTER TABLE `HoliDays` ADD COLUMN `country` bigint(20) DEFAULT NULL;

ALTER TABLE `HoliDays` DROP INDEX holidays_dateh;
ALTER TABLE `HoliDays` ADD unique key `holidays_dateh_country` (`dateh`,`country`);
ALTER TABLE `WorkDays` ADD unique key `workdays_name_country` (`name`,`country`);

Delete from `Reports`;

INSERT INTO `Reports` (`id`, `name`, `details`, `parameters`, `query`, `paramOrder`, `type`) VALUES
(1, 'Employee Details Report', 'This report list all employee details and you can filter employees by department, employment status or job title', '[\r\n[ "department", {"label":"Department","type":"select","remote-source":["CompanyStructure","id","title"],"allow-null":true}],\r\n[ "employment_status", {"label":"Employment Status","type":"select","remote-source":["EmploymentStatus","id","name"],"allow-null":true}],\r\n[ "job_title", {"label":"Job Title","type":"select","remote-source":["JobTitle","id","name"],"allow-null":true}]\r\n]', 'Select id, employee_id as ''Employee ID'',\r\nconcat(`first_name`,'' '',`middle_name`,'' '', `last_name`) as ''Name'',\r\n(SELECT name from Nationality where id = nationality) as ''Nationality'',\r\nbirthday as ''Birthday'',\r\ngender as ''Gender'',\r\nmarital_status as ''Marital Status'',\r\nssn_num as ''SSN Number'',\r\nnic_num as ''NIC Number'',\r\nother_id as ''Other IDs'',\r\ndriving_license as ''Driving License Number'',\r\n(SELECT name from EmploymentStatus where id = employment_status) as ''Employment Status'',\r\n(SELECT name from JobTitles where id = job_title) as ''Job Title'',\r\n(SELECT name from PayGrades where id = pay_grade) as ''Pay Grade'',\r\nwork_station_id as ''Work Station ID'',\r\naddress1 as ''Address 1'',\r\naddress2 as ''Address 2'',\r\ncity as ''City'',\r\n(SELECT name from Country where code = country) as ''Country'',\r\n(SELECT name from Province where id = province) as ''Province'',\r\npostal_code as ''Postal Code'',\r\nhome_phone as ''Home Phone'',\r\nmobile_phone as ''Mobile Phone'',\r\nwork_phone as ''Work Phone'',\r\nwork_email as ''Work Email'',\r\nprivate_email as ''Private Email'',\r\njoined_date as ''Joined Date'',\r\nconfirmation_date as ''Confirmation Date'',\r\n(SELECT title from CompanyStructures where id = department) as ''Department'',\r\n(SELECT concat(`first_name`,'' '',`middle_name`,'' '', `last_name`,'' [Employee ID:'',`employee_id`,'']'') from Employees e1 where e1.id = e.supervisor) as ''Supervisor'' \r\nFROM Employees e _where_', '["department","employment_status","job_title"]', 'Query'),
(2, 'Employee Leaves Report', 'This report list all employee leaves by employee, date range and leave status', '[\r\n[ "employee", {"label":"Employee","type":"select","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],\r\n[ "date_start", {"label":"Start Date","type":"date"}],\r\n[ "date_end", {"label":"End Date","type":"date"}],\r\n[ "status", {"label":"Leave Status","type":"select","source":[["NULL","All Statuses"],["Approved","Approved"],["Pending","Pending"],["Rejected","Rejected"]]}]\r\n]', 'EmployeeLeavesReport', '["employee","date_start","date_end","status"]', 'Class'),
(3, 'Employee Time Entry Report', 'This report list all employee time entries by employee, date range and project', '[\r\n[ "employee", {"label":"Employee","type":"select","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],\r\n[ "project", {"label":"Project","type":"select","allow-null":true,"remote-source":["Project","id","name"]}],\r\n[ "date_start", {"label":"Start Date","type":"date"}],\r\n[ "date_end", {"label":"End Date","type":"date"}]\r\n]', 'EmployeeTimesheetReport', '["employee","date_start","date_end","status"]', 'Class'),
(4, 'Employee Attendance Report', 'This report list all employee attendance entries by employee and date range', '[\r\n[ "employee", {"label":"Employee","type":"select","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],\r\n[ "date_start", {"label":"Start Date","type":"date"}],\r\n[ "date_end", {"label":"End Date","type":"date"}]\r\n]', 'EmployeeAttendanceReport', '["employee","date_start","date_end"]', 'Class');

create table `DataEntryBackups` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`tableType` varchar(200) default null,
	`data` longtext default null,
	primary key  (`id`)
) engine=innodb default charset=utf8;

create table `AuditLog` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`time` datetime default '0000-00-00 00:00:00',
	`user` bigint(20) NOT NULL,
	`ip` varchar(100) NULL,
	`type` varchar(100) NOT NULL,
	`employee` varchar(300) NULL,
	`details` text default null,
	CONSTRAINT `Fk_AuditLog_Users` FOREIGN KEY (`user`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	primary key  (`id`)
) engine=innodb default charset=utf8;


create table `Notifications` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`time` datetime default '0000-00-00 00:00:00',
	`fromUser` bigint(20) NULL,
	`fromEmployee` bigint(20) NULL,
	`toUser` bigint(20) NOT NULL,
	`image` varchar(500) default null,
	`message` text default null,
	`action` text default null,
	`type` varchar(100) NULL,
	`status` enum('Unread','Read') default 'Unread',
	CONSTRAINT `Fk_Notifications_Users` FOREIGN KEY (`touser`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	primary key  (`id`),
	KEY `toUser_time` (`toUser`,`time`),
	KEY `toUser_status_time` (`toUser`,`status`,`time`)
) engine=innodb default charset=utf8;