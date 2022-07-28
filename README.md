# kanban-board
A modded Kanban interface that'll allow you to track tasks at different stages, move them from stage-to-stage, as well as modify existing tasks in each stage.

Original design idea based off of [Kanban-Board-App](https://www.jqueryscript.net/other/kanban-board-app.html). That version is no longer updated or supported, and built strictly for Bootstrap V4 on a standalone static site.

This new Kanban Board is built to support Bootstrap V3 and/or V4, as well as MySQLDB/MariaDB backend driven, jquery ajax execution, and completely dynamic and supported.
<br><br>

## Pre-requisites
Web server which executes PHP v5.x or newer, and MySQLDB/MariaDB.

And because I know some people are going to say that this can be done within a container, well of course it can, and if you'd like to run it that way go for it, I won't stop you ;)
<br><br>

## Setup
### Web Files
Make sure the entire scrum/ directory contents are present and available from a running web server.

This was originally built on top of a linux system/folder structure, so to limit the number of issues, please use a linux web server for servicing the Kanban Board.

### DB Schema Setup
The DB Setup SQL is available in each of the includes/Kanban*php files, however for ease of use, they are also below. Copy-and-paste the following SQL code within your DB server (after selecting which database you wish to use):
<br>

#### Kanban SQL class object:
``` sql
		CREATE TABLE Kanban
		(
			`primaryid` int primary key auto_increment,
			`id` int NOT NULL,
			`title` varchar(100) NOT NULL,
			`description` varchar(255) default NULL,
			`position` varchar(30) default 'yellow',
			`priority` boolean default false
		);
```
#### Kanban Settings SQL class object:
The following are *DEFAULT* options for a standard Kanban board. Feel free to change the title, color, and ordernumber as you please.
``` sql
		CREATE TABLE KanbanSettings
		(
            `id` int auto_increment primary key,
            `title` varchar(30) NOT NULL,
            `color` varchar(30) NOT NULL default 'white',
            `ordernumber` int default 1
		);
        INSERT INTO KanbanSettings SET `title`='backlog', `color`='yellow', `ordernumber`=1;
        INSERT INTO KanbanSettings SET `title`='in progress', `color`='coral', `ordernumber`=2;
        INSERT INTO KanbanSettings SET `title`='blocked', `color`='red',`oOrdernumber`=3;
        INSERT INTO KanbanSettings SET `title`='testing', `color`='orange', `ordernumber`=4;
        INSERT INTO KanbanSettings SET `title`='completed', `color`='green', `ordernumber`=5;
```
#### KanbanTheme SQL class object
``` sql
		CREATE TABLE KanbanTheme
		(
			`theme` enum('light','darkmode') default 'light'
		);
		INSERT INTO KanbanTheme SET `theme` = 'light';
```
<br>

### Configuration Settings
Edit the following configuration settings file to implement the Kanban Board integration: includes/define.php

Within this file, you will need to set the following variables:
1. **CONN_DATABASE_HOST** - URL/IP address/hostname for where the MySQLDB/MariaDB server resides
2. **CONN_DATABASE_USERNAME** - database connection username *(this user account must have access to the database where the Kanban* tables reside)*
3. **CONN_DATABASE_PASSWORD** - password for database connection username
4. **CONN_DATABASE_NAME** - database name where the Kanban* tables reside


## Support
Feel free to post any issues you have, I'd be happy to support/assist to get your system up-and-running.


### DISCLOSURE
Again this was modded from another Kanboard Board that is no longer supported, but feel free to reach out with any questions, etc.

Enjoy!
