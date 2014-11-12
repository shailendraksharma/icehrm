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


REPLACE INTO `Reports` (`id`, `name`, `details`, `parameters`, `query`, `paramOrder`, `type`) VALUES
(1, 'Employee Details Report', 'This report list all employee details and you can filter employees by department, employment status or job title', '[\r\n[ "department", {"label":"Department","type":"select2","remote-source":["CompanyStructure","id","title"],"allow-null":true}],\r\n[ "employment_status", {"label":"Employment Status","type":"select2","remote-source":["EmploymentStatus","id","name"],"allow-null":true}],\r\n[ "job_title", {"label":"Job Title","type":"select2","remote-source":["JobTitle","id","name"],"allow-null":true}]\r\n]', 'Select id, employee_id as ''Employee ID'',\r\nconcat(`first_name`,'' '',`middle_name`,'' '', `last_name`) as ''Name'',\r\n(SELECT name from Nationality where id = nationality) as ''Nationality'',\r\nbirthday as ''Birthday'',\r\ngender as ''Gender'',\r\nmarital_status as ''Marital Status'',\r\nssn_num as ''SSN Number'',\r\nnic_num as ''NIC Number'',\r\nother_id as ''Other IDs'',\r\ndriving_license as ''Driving License Number'',\r\n(SELECT name from EmploymentStatus where id = employment_status) as ''Employment Status'',\r\n(SELECT name from JobTitles where id = job_title) as ''Job Title'',\r\n(SELECT name from PayGrades where id = pay_grade) as ''Pay Grade'',\r\nwork_station_id as ''Work Station ID'',\r\naddress1 as ''Address 1'',\r\naddress2 as ''Address 2'',\r\ncity as ''City'',\r\n(SELECT name from Country where code = country) as ''Country'',\r\n(SELECT name from Province where id = province) as ''Province'',\r\npostal_code as ''Postal Code'',\r\nhome_phone as ''Home Phone'',\r\nmobile_phone as ''Mobile Phone'',\r\nwork_phone as ''Work Phone'',\r\nwork_email as ''Work Email'',\r\nprivate_email as ''Private Email'',\r\njoined_date as ''Joined Date'',\r\nconfirmation_date as ''Confirmation Date'',\r\n(SELECT title from CompanyStructures where id = department) as ''Department'',\r\n(SELECT concat(`first_name`,'' '',`middle_name`,'' '', `last_name`,'' [Employee ID:'',`employee_id`,'']'') from Employees e1 where e1.id = e.supervisor) as ''Supervisor'' \r\nFROM Employees e _where_', '["department","employment_status","job_title"]', 'Query'),
(2, 'Employee Leaves Report', 'This report list all employee leaves by employee, date range and leave status', '[\r\n[ "employee", {"label":"Employee","type":"select2","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],\r\n[ "date_start", {"label":"Start Date","type":"date"}],\r\n[ "date_end", {"label":"End Date","type":"date"}],\r\n[ "status", {"label":"Leave Status","type":"select","source":[["NULL","All Statuses"],["Approved","Approved"],["Pending","Pending"],["Rejected","Rejected"]]}]\r\n]', 'EmployeeLeavesReport', '["employee","date_start","date_end","status"]', 'Class'),
(3, 'Employee Time Entry Report', 'This report list all employee time entries by employee, date range and project', '[\r\n[ "employee", {"label":"Employee","type":"select2","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],\r\n[ "project", {"label":"Project","type":"select","allow-null":true,"remote-source":["Project","id","name"]}],\r\n[ "date_start", {"label":"Start Date","type":"date"}],\r\n[ "date_end", {"label":"End Date","type":"date"}]\r\n]', 'EmployeeTimesheetReport', '["employee","date_start","date_end","status"]', 'Class'),
(4, 'Employee Attendance Report', 'This report list all employee attendance entries by employee and date range', '[\r\n[ "employee", {"label":"Employee","type":"select2","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],\r\n[ "date_start", {"label":"Start Date","type":"date"}],\r\n[ "date_end", {"label":"End Date","type":"date"}]\r\n]', 'EmployeeAttendanceReport', '["employee","date_start","date_end"]', 'Class');

ALTER TABLE  `Settings` DROP INDEX  `name` ,
ADD UNIQUE  `name` (`name`);

REPLACE INTO `Settings` (`name`, `value`, `description`, `meta`) VALUES
('System: Add New Permissions', '0', 'Select this to add new permission changes done to meta.json file of any module','["value", {"label":"Value","type":"select","source":[["1","Yes"],["0","No"]]}]'),
('Email: Mode', 'SMTP', 'SMTP, PHP Mailer or Amazon SES. SMTP = send emails using local or a remote smtp server. PHP Mailer = send emails using mail function provided by php. Amazon SES = send emails trough amazon Simple Email Service.','["value", {"label":"Value","type":"select","source":[["SMTP","SMTP"],["PHP Mailer","PHP Mailer"],["SNS","Amazon SES"]]}]');