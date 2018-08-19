<?php

function MyShortEcho($msg, $content_type = 'text/plain')
{
    header('Content-Type: '.$content_type);
    //ob_clean();
    //ob_start();
    echo $msg;
    ob_end_flush();
}


function DbExecutePrepare($sql_stored_procedure, $values, &$connection)
{
    /**
    :param $sql_stored_procedure String:
        The name of the stored procedure

    :param $values Array:
        Array with a String as Key and a String/Number/Boolean Value

    :param $connection Database Connection:
        The Database instance connection
    */

    global $usu_id;
    global $usu_auth;

    // Create the SQL statement
    $sql = 'CALL '.$sql_stored_procedure.'(:usu_id, :usu_auth';
    foreach ($values as $param => $value) {
        $sql .= ', :p'.$param;
    }
    $sql .= ');';

    try {

        // Execute the statement
        $qry = $connection->prepare($sql);
        $qry->bindParam(':usu_id', $usu_id);
        $qry->bindParam(':usu_auth', $usu_auth);
        foreach ($values as $param => $value) {
            $qry->bindParam(':p'.$param, $value);
        }
        //print_r($qry->GetParameters());
        $qry->execute();

        // Return that the command was succefully executed
        return true;
    } catch (Exception $e) {
        // Return that the command was not succefully executed
        throw new Exception($e);
        return false;
    }

}

