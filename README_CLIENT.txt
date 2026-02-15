
HOW TO RUN THE PROJECT
======================

Prerequisites:
-------------
1. XAMPP installed and running (Apache and MySQL modules started).
2. Node.js installed.
3. Composer installed.

Steps:
------
1. Double-click the "run_project.bat" file in this folder.
2. A terminal window will open and automatically set up the project:
   - It will install all necessary libraries.
   - It will prepare the database.
   - It will build the website assets.
3. Once finished, the website will open automatically in your browser.

Troubleshooting:
----------------
- If the database step fails, open XAMPP -> phpMyAdmin and ensure a database exists with the name specified in the .env file (default is usually 'laravel' or 'cause_society').
- If the window closes immediately with an error, try right-clicking "run_project.bat" and run as Administrator, or check if Composer/Node are correctly installed.
