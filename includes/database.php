<?php
const HOST = 'localhost';
const DB_USER = 'root';
const DB_PWD = '';
const DB_NAME = 'final_db';

function ConnectDB()
{
    $db = new mysqli(HOST, DB_USER, DB_PWD, DB_NAME);

    if ($db->connect_error) {
        echo '<h1>Unable to Establish Connectopm</h1>';
        exit;
    }
    return $db;
}

function query($query)
{
    try {
        $db = ConnectDB();
        $result = $db->query($query);

        // Close the database connection
        $db->close();

        if ($result === false) {
            throw new Exception("Query execution failed: " . $db->error);
        }

        return $result;
    } catch (Exception $e) {
        // Handle any exceptions (e.g., database connection error, query error)
        echo 'Error: ' . $e->getMessage();
        return null; // or handle the error as per your application's requirements
    }
}
