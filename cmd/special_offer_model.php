<?php
require('functions.php');
require('../conn/conpdo.class.php');

// CSRF protection
if (!$csrf->check_valid('post') && !$csrf->check_valid('get')) {
    MyShortEcho('Token is unkowned!');
    exit;
}


$error = array();


function AddSpecialOffer($name, $percentage)
{
    /**
    Add a new SpecialOffer

    :param :name String:
        Name of the customer

    :param :percentage String::
        Percentage of the customer

    :return Boolean:
        True if succefully
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        // Execute the command
        $result = DbExecutePrepare('ADD_SPECIAL_OFFER', array('name' => $name, 'percentage' => $percentage, 'url' => '', 'code' => '', 'expiration' => ''), $connection);

        // Commit the transaction and close the connection
        CommitTransaction($connection, $transaction);
    } catch (Exception $e) {
        // Rollback the transaction, close the connection and display an error message
        RollbackTransaction($connection, $transaction, $e->getMessage());

        // Return that the command was not succefully executed
        return false;
    }

    return true;
}


function EditSpecialOffer($id, $name, $percentage)
{
    /**
    Edit a SpecialOffer

    :param :id Integer:
        Id of the customer

    :param :name String:
        Name of the customer

    :param :percentage String::
        Percentage of the customer

    :return Boolean:
        True if succefully
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        // Execute the command
        $result = DbExecutePrepare('EDIT_SPECIAL_OFFER', array('id' => $id, 'name' => $name, 'percentage' => $percentage, 'url' => '', 'code' => '', 'expiration' => ''), $connection);

        // Commit the transaction and close the connection
        CommitTransaction($connection, $transaction);
    } catch (Exception $e) {
        // Rollback the transaction, close the connection and display an error message
        RollbackTransaction($connection, $transaction, $e->getMessage());

        // Return that the command was not succefully executed
        return false;
    }

    return $result;
}


function DelSpecialOffer($id)
{
    /**
    Edit a SpecialOffer

    :param :id Integer:
        Id of the customer

    :return Boolean:
        True if succefully
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        // Execute the command
        $result = DbExecutePrepare('DEL_SPECIAL_OFFER', array('id' => $id), $connection);

        // Commit the transaction and close the connection
        CommitTransaction($connection, $transaction);
    } catch (Exception $e) {
        // Rollback the transaction, close the connection and display an error message
        RollbackTransaction($connection, $transaction, $e->getMessage());

        // Return that the command was not succefully executed
        return false;
    }

    return $result;
}


function GetSpecialOfferIdFromPercentage($percentage)
{
    /**
    Get the customer Id looking for the customer Percentage

    :param :percentage String:
        Percentage of the customer

    :return Integer/Boolean:
        If succefully, return the SpecialOffer Id number
        If not, return False
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);


        // Check if the customer already exists
        $sql = 'SELECT SPO_ID, SPO_PERCENTAGE FROM SPECIAL_OFFER WHERE SPO_PERCENTAGE = :percentage;';
        $qry = $connection->prepare($sql);
        $qry->bindParam(':percentage', $percentage);
        $qry->execute();
        $count = $qry->rowCount();
        $r = $qry->fetch(PDO::FETCH_ASSOC);
        $cus_id = $r['SPO_ID'];
        $cus_percentage = $r['SPO_PERCENTAGE'];

        // Assert if the customer could be found
        if ($cus_percentage != $percentage) {
            return false;
        }

        // Commit the transaction and close the connection
        CommitTransaction($connection, $transaction);

        // Return with the CUstomer Id
        return $cus_id;

    } catch (Exception $e) {
        // Rollback the transaction, close the connection and display an error message
        RollbackTransaction($connection, $transaction, $e->getMessage());

        // Return that the command was not succefully executed
        return false;
    }
}


function GetAllSpecialOffers($search, $order, $offset, $quantity)
{
    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        $result = array();

        // Check if the customer already exists
        $sql = 'SELECT SPO_ID, SPO_NAME, SPO_PERCENTAGE, SPO_CODE, ';
        $sql .= '(SELECT COUNT(*) FROM VOUCHER WHERE SPO_ID = SPECIAL_OFFER.SPO_ID AND VOU_STATUS = 1) AS QT_OPEN_VOUCHER, ';
        $sql .= '(SELECT COUNT(*) FROM VOUCHER WHERE SPO_ID = SPECIAL_OFFER.SPO_ID AND VOU_STATUS = 2) AS QT_USED_VOUCHER ';
        $sql .= 'FROM SPECIAL_OFFER WHERE SPO_NAME LIKE :search ORDER BY '.$order.' LIMIT '.$offset.', '.$quantity.';';
        $qry = $connection->prepare($sql);
        $qry->bindParam(':search', $search);
        $qry->execute();
        $count = $qry->rowCount();
        while ($r = $qry->fetch(PDO::FETCH_ASSOC)) {
            $result[] = array(
                'id' => $r['SPO_ID'],
                'name' => $r['SPO_NAME'],
                'percentage' => $r['SPO_PERCENTAGE'],
                'qt_open_voucher' =>  $r['QT_OPEN_VOUCHER'],
                'qt_used_voucher' =>  $r['QT_USED_VOUCHER'],
                'spo_code' =>  $r['SPO_CODE']
            );
        }

        // Commit the transaction and close the connection
        CommitTransaction($connection, $transaction);

    } catch (Exception $e) {
        // Rollback the transaction, close the connection and display an error message
        RollbackTransaction($connection, $transaction, $e->getMessage());

        // Return that the command was not succefully executed
        return false;
    }

    // Return the JSon in a String
    return json_encode($result);
}
