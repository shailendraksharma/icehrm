IceHrm
===========

Installation
------------
Download the latest release https://sourceforge.net/projects/icehrm/

Copy the downloaded file to the path you want to install iCE Hrm in your server and extract.

Create a mysql DB for and user. Grant all on iCE Hrm DB to new DB user.

Visit iCE Hrm installation path in your browser.

During the installation form, fill in details appropriately.

Once the application is installed use the username = admin and password = admin to login to your system.

Note: Please rename or delete the install folder (<ice hrm root>/app/install) since it could pose a security threat to your iCE Hrm instance.


Update icehrm v6.1 to v7.1
--------------------------
Delete all folders except <icehrm>/app directory

Copy contents of icehrm_v7.1.zip to existing icehrm directory except app directory

Execute 'icehrmdb_os_update_v6.1_to_v7.1.sql' on your icehrm database

Release note v7.1
-----------------
*Features
*Improved company structure graph
*Leave notes implementation – Supervisor can add a note when approving or rejecting leaves
*Filtering support
*Select boxes with long lists are now searchable
*Add/Edit/Delete company structure permissions added for managers
*Add ability to disable employee information editing

*Fixes
*Make loans editable only by admin
*Fix: permissions not getting applied to employee documents
*Fix error adding employee documents when no user assigned to the admin

*Code Quality
*Moving all module related code and data into module folders

Release note v6.1
-----------------
Leave carry forwared related isue fixed

Release note v6.0
-----------------
* Features
* Notifications for leaves and timesheets
* Leave module accrue and leave carry forward
* Employee leave entitlement sub module
* Ability to put system on debug mode
* Allow admins to see documents of all the employees at one place
* Backup data when deleting an employee
* Employee attendance report added
* Changes to time entry form in timesheet module to make time entry process faster
* Admin can make all projects available to employees or just the set of prjects assigned to them using Setting "Projects: Make All Projects Available to Employees"
* Employee document, date added field can not be changed by the employee anymore
* About dialog added for admins

* Fixes
* Fix default employee delete issue (when the default employee is deleted the admin user attached to it also get deleted)
* Fix user duplicate email issue
* Fix manager can not logout from switched employee
* Remove admin guide from non admin users

Release note v5.3
-----------------
* Fixes
* Fix missing employee name in employee details report

Release note v5.2
-----------------
* Fixes
* Remove unwanted error logs
* Fix attendance module employee permission issue
* Resolve warnings
* Remove add new button from subordinates module
* Adding administrators' guide

Release note v5.1
-----------------
* Fixes
* Fixing for non updating null fields
* https://bitbucket.org/thilina/icehrm-opensource/commits/df57308b53484a2e43ef5c72967ed1cd0dc756cc

Release note v5.0
-----------------
* Features
* New user permission implementation
* Adding new user level - Manager

* Fixes
* Fixing remote table loading issue

Release note v4.2
-----------------
* Fixes
* https://bitbucket.org/thilina/icehrm-opensource/issue/23/subordinate-leaves-pagination-not-working
* https://bitbucket.org/thilina/icehrm-opensource/issue/20/error-occured-while-time-punch


Release note v4.1
-----------------
* Features
* Better email format for notifications
* Convert upload dialog to a bootstrp model

* Fixes
* Fix error sending emails with amazon SES
* Fix errors related to XAMPP and WAMPP servers
* Fix php warnings and notifications
* Fix company structure graph issues
* Allow icehrm client to work without an internet connection
* Fix installer incorrect base url issue
* Fix empty user creation issue
