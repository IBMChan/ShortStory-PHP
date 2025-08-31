**Setup Instructions**

**Step 1: Place Project Files and Start Services**

***1.1 - Move the Project:***

Place the entire shortstory/ folder inside your XAMPP installation's htdocs directory.
The path is typically C:/xampp/htdocs/ on WindowsStart XAMPP: 
Open the XAMPP Control Panel and start the Apache and MySQL services.

**Step 2: Create and Set Up the Database**

***2.1 - Open phpMyAdmin:***

In your web browser, navigate to the following URL:
__http://localhost/phpmyadmin/__

***2.2 - Create the Database:***

Click on the Databases tab.Under "Create database", enter the name shortstory.Click Create.

***2.3 - Import the Database Schema:***

Select the newly created shortstory database from the left-hand menu.Click on the Import tab.
Click "Choose File" and select the schema file from the project folder: shortstory/database/table.sql


***2.4 - Import Sample Data:***

After the schema import is successful, click the Import tab again.
Click "Choose File" and select the data file: shortstory/database/rows.sql

**Step 3: Configure the Database Connection**

***3.1 - Open the Configuration File:***

Using a text editor, open the following file: shortstory/includes/db.php
Verify Credentials: The file contains database credentials. For a standard XAMPP installation, the password should be empty. 
Ensure the DB_PASSWORD line looks like this: define('DB_PASSWORD', ''); 

**Step 4: Accessing the Website**

Open your web browser and navigate to: 
__http://localhost/shortstory/src/__

***You should now see the ShortStory homepage with the sample books displayed.***
