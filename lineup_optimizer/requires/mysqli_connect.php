<?php 

    //-----------------------------------------------------------------------------//
    // This creates a connection to the 'nba_lineup_optimizer' database and to MySQL. //
    // It also sets the encoding.                                                  //
    //-----------------------------------------------------------------------------//
    
    // Set the access details as constants.
    define ('DB_USER', 'webadmin');
    define ('DB_PASSWORD', 'webadmin');
    define ('DB_HOST', 'localhost');
    define ('DB_NAME', 'nba_lineup_optimizer');

    // Make the connection.
    if (!isset($dbcon))
    {
        $dbcon = @mysqli_connect (DB_HOST, 
                                  DB_USER, 
                                  DB_PASSWORD, 
                                  DB_NAME) OR 
                  die ('Could not connect to MySQL: ' . mysqli_connect_error() );

        // Set the encoding.
        @mysqli_set_charset($dbcon, 'utf8');
    }
?>