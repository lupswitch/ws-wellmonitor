<?php
class RESPONSE {
    public static $STATUS = array(0 => 'There are empty variables', 1 => 'Ok', 2 => 'Database error', 3 => 'Error lógico', 4 => 'Token failed');
    public static $RESPONSE = array(
        0=> 'Unregistered user',
        1=> 'Incorrect password',
        2=> 'The variable is not of type number',
        3=> 'Passwords do not match',
        4=> 'Email is not registered',
        5=> 'inactive user'
    ); 
}
?>