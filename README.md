


Database Seeder for Vanilla PHP
This project provides a PHP class for quickly populating a database with sample data, particularly useful when you cannot use tools like Tinker.

Overview
The DatabaseSeeder class allows users to insert multiple records into a MySQL database with ease. This is particularly beneficial for developers working with Vanilla PHP who need to populate their databases for testing or development purposes.

Features
Bulk Insertion: Insert up to 100 sample records in one go.
Single Record Insertion: Add individual records to the database with a web form.
File Upload: Support for uploading images associated with the records.
Requirements
PHP 7.0 or higher
MySQL database
Proper configuration of database credentials in the script
Usage
Set Up Database Connection: Update the database connection parameters in the constructor of the DatabaseSeeder class to match your environment.

php
Copy code
$this->db = @mysqli_connect("localhost", "root", "", "your_database_name");
Form Submission:

Use the provided HTML form to add a single product or to bulk insert products.
The form requires inputs for category, price, name, description, image, featured status, and deletion status.
Handling Image Uploads: Ensure the specified upload directory exists and is writable. Adjust the path in the script as necessary.

Running the Script: Access the script through a web server that supports PHP (e.g., XAMPP) and use the web interface to insert data.

Example Code
Here’s an example of how to initiate the seeder and insert data:

php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seeder = new DatabaseSeeder();

    if (isset($_POST['bulk_insert'])) {
        $data = []; // Prepare your bulk data array
        $seeder->seedData($data);
    } else {
        $item = []; // Prepare your single item array
        $seeder->insertSingle($item);
    }
    $seeder->close();
}
Conclusion
This seeder script provides a straightforward solution for populating a database in a Vanilla PHP environment. Ensure to follow the instructions carefully to set it up and use it effectively.

