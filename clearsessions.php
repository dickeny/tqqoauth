<?php
/**
 * @file
 * Clears PHP sessions and redirects to the connect page.
 */
 
/* Load and clear sessions */
session_start();
session_destroy();
 
/* Redirect to page with the connect to Tqq option. */
header('Location: ./connect.php');
