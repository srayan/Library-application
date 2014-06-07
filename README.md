Srayan&Guhathakurta

Instructions*for*librarians*(users)*

This library management application, known,as Book Wormis a web 
application,which has been developed using the following technologies:

Front-end: HTML, CSS and jQuery
Programming Language: PHP
Back-end: MySQL

The application is based on a virtual server. I have used XAMPP for 
configuring and running MySQL and Apache servers. While the MySQL 
server helps in hosting the database, the Apache server helps in hosting 
the web service.


Design*decisions*and*justifications*
WHY MAKE A WEB-APP

The reason to develop the application on a web platformis to address the 
fact that the application is going tobe accessed by different librarians 
(the users) from different branches, across different geographical 
locations. 

Thus the web applicationcan be hosted on a remote dedicated server and 
the librarians can access it easily and securely overthe Internet.

Add on:
1. Check the finesaccruedfor a particular card number.
2. Check the complete history for a particular branch or a card or a 
book-id.
3. Periodic back up’s of the database can be stored onto secure web 
services like Dropbox, through an optimized script.This is an 
advancedDBA feature.



The application structure is highly customizable, which makes it flexible 
towards the inclusion of new requirements in the future. The application 
user interface has a simplistic approach, assisting the user with iconsthat 
are self-explanatory.

Technical dependencies (software libraries, software versions, etc.).

The application backend is built on MySQL. However the relational 
schema that has been programmed to define and build the database, can 
be used to replicate the back end on MS SQL and other standard database 
management systems as well.

PhP is a standard programming and the code is supported by all of the 
available browsers, such as Chrome, Safari, Firefox, and Internet Explorer.
Other programming platforms that have been used for this project have 
extensive technological support.
XAMPP for OS X v 1.8.3
PHP 5.5.11

How to use it:

1. Install XAMPP from https://www.apachefriends.org/download.html
2. Start MySQL Server and Apache web server
3. Place the php files in the htdocs folder belonging to XAMPP
4. Load the given database into MySQLthrough PhP MyAdmin
5. Type into browser: localhost/bookworm

Select following options from the tile on the left panel:
Searchfor books
Check outbooks
Check inbooks
Add a new member 
Check History
Check finesand dues
About mesection (also accessible through the portal) 
