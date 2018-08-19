<?php
require('functions.php');
require('../conn/conpdo.class.php');

// CSRF protection
if (!$csrf->check_valid('post') && !$csrf->check_valid('get')) {
    MyShortEcho('Token is unkowned!');
    exit;
}


$error = array();


function AddVoucher($cus_id, $spo_id)
{
    /**
    Add a new Voucher, making a rekationship between a customer and a special offer

    :param :cus_id Integer:
        Id of the customer

    :param :spo_id Integer:
        Id of the Special Offer

    :return Boolean:
        True if succefully
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        // Execute the command
        $result = DbExecutePrepare('ADD_VOUCHER', array('cus_id' => $cus_id, 'spo_id' => $spo_id), $connection);

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


function UseVoucher($code, $email)
{
    /**
    Try to find the relationship between the customer and the special offer.
    If found, check if the voucher is available.
    If yes, use it.

    :param :email Integer:
        Email of the customer

    :param :code String:
        The code of the special offer

    :return Boolean:
        True if succefully
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);


        // Check if the customer already exists
        $sql = 'SELECT CUS_ID, CUS_EMAIL FROM CUSTOMER WHERE CUS_EMAIL = :email;';
        $qry = $connection->prepare($sql);
        $qry->bindParam(':email', $email);
        $qry->execute();
        $count = $qry->rowCount();
        $r = $qry->fetch(PDO::FETCH_ASSOC);
        $cus_id = $r['CUS_ID'];
        $cus_email = $r['CUS_EMAIL'];

        // Assert if the customer could be found
        if ($count == 0 || $cus_email != $email) {
            return json_encode(array('error' => 'The Customer could not be found by the given Email'));
        }


        // Get the ID of the special offer from the special offer code
        $sql = 'SELECT SPO_ID, SPO_CODE, SPO_PERCENTAGE, SPO_DATETIME_EXPIRATION FROM SPECIAL_OFFER WHERE SPO_CODE = :code;';
        $qry = $connection->prepare($sql);
        $qry->bindParam(':code', $code);
        $qry->execute();
        $count = $qry->rowCount();
        $r = $qry->fetch(PDO::FETCH_ASSOC);
        $spo_id = $r['SPO_ID'];
        $spo_code = $r['SPO_CODE'];
        $spo_percentage = $r['SPO_PERCENTAGE'];
        $spo_datetime_expiration = $r['SPO_DATETIME_EXPIRATION'];

        // Assert if the special offer could be found
        if ($count == 0 || $spo_code != $code) {
            return json_encode(array('error' => 'The Special Offer could not be found by the given Code'));
        }

        // Assert if the special offer is valid
        if ($spo_datetime_expiration < date()) {
            return json_encode(array('error' => 'The Special Offer has already been used'));
        }


        // Get the information from the Voucher related by the Customer and the Special Offer
        $sql = 'SELECT VOU_ID, VOU_DATETIME_START, VOU_DATETIME_EXPIRATION, VOU_STATUS FROM VOUCHER WHERE CUS_ID = :cus_id AND SPO_ID = :spo_id AND VOU_STATUS = 1;';
        $qry = $connection->prepare($sql);
        $qry->bindParam(':cus_id', $cus_id);
        $qry->bindParam(':spo_id', $spo_id);
        $qry->execute();
        $count = $qry->rowCount();
        $r = $qry->fetch(PDO::FETCH_ASSOC);
        $vou_id = $r['VOU_ID'];
        $vou_datetime_start = $r['VOU_DATETIME_START'];
        $vou_datetime_expiration = $r['VOU_DATETIME_EXPIRATION'];
        $vou_status = $r['VOU_STATUS'];

        // Assert if the voucher is not used
        if ($vou_status == 2) {
            return json_encode(array('error' => 'The Voucher has already been used'));
        }

        // Assert if the voucher is valid
        if ($vou_datetime_start > date()) {
            return json_encode(array('error' => 'The Voucher isn\'t available yet'));
        }

        // Assert if the voucher is valid
        if ($vou_datetime_expiration < date()) {
            return json_encode(array('error' => 'The Voucher is already expired'));
        }


        // Execute the command
        $result = DbExecutePrepare('USE_VOUCHER', array('vou_id' => $vou_id), $connection);


        // Commit the transaction and close the connection
        CommitTransaction($connection, $transaction);

        // Return that the command was succefully executed
        return json_encode(array('error' => 'no', 'percentage' => $spo_percentage));
    } catch (Exception $e) {
        // Rollback the transaction, close the connection and display an error message
        RollbackTransaction($connection, $transaction, $e->getMessage());

        // Return that the command was not succefully executed
        return false;
    }
}
