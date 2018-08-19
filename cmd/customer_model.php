<?php
require('functions.php');
require('../conn/conpdo.class.php');

// CSRF protection
if (!$csrf->check_valid('post') && !$csrf->check_valid('get')) {
    MyShortEcho('Token is unkowned!');
    exit;
}


$error = array();


function AddCustomer($name, $email)
{
    /**
    Add a new Customer

    :param :name String:
        Name of the customer

    :param :email String::
        Email of the customer

    :return Boolean:
        True if succefully
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        // Execute the command
        $result = DbExecutePrepare('ADD_CUSTOMER', array('name' => $name, 'email' => $email), $connection);
        
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


function EditCustomer($id, $name, $email)
{
    /**
    Edit a Customer

    :param :id Integer:
        Id of the customer

    :param :name String:
        Name of the customer

    :param :email String::
        Email of the customer

    :return Boolean:
        True if succefully
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        // Execute the command
        $result = DbExecutePrepare('EDIT_CUSTOMER', array('id' => $id, 'name' => $name, 'email' => $email), $connection);
        
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


function DelCustomer($id)
{
    /**
    Edit a Customer

    :param :id Integer:
        Id of the customer

    :return Boolean:
        True if succefully
    */

    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        // Execute the command
        $result = DbExecutePrepare('DEL_CUSTOMER', array('id' => $id), $connection);
        
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


function GetCustomerIdFromEmail($email)
{
    /**
    Get the customer Id looking for the customer Email

    :param :email String:
        Email of the customer

    :return Integer/Boolean:
        If succefully, return the Customer Id number
        If not, return False
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
        if ($cus_email != $email) {
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


function GetAllCustomers($search, $order, $offset, $quantity)
{
    try {
        // Start transaction, create a database instance if needed
        StartTransaction($connection, $transaction);

        $result = array();

        // Check if the customer already exists
        $sql = 'SELECT CUS_ID, CUS_NAME, CUS_EMAIL, ';
        $sql .= '(SELECT COUNT(*) FROM VOUCHER WHERE CUS_ID = CUSTOMER.CUS_ID AND VOU_STATUS = 1) AS QT_OPENED_SPECIAL_OFFERS, ';
        $sql .= '(SELECT COUNT(*) FROM VOUCHER WHERE CUS_ID = CUSTOMER.CUS_ID AND VOU_STATUS = 2) AS QT_USED_SPECIAL_OFFERS ';
        $sql .= 'FROM CUSTOMER WHERE CUS_NAME LIKE :search OR CUS_EMAIL LIKE :search ORDER BY '.$order.' LIMIT '.$offset.', '.$quantity.';';
        $qry = $connection->prepare($sql);
        $qry->bindParam(':search', $search);
        $qry->execute();
        $count = $qry->rowCount();
        while ($r = $qry->fetch(PDO::FETCH_ASSOC)) {

            // Get the code of all opened vouchers
            $opened_vouchers = '';
            $sql2 = 'SELECT SPO_CODE FROM SPECIAL_OFFER INNER JOIN VOUCHER ON (SPECIAL_OFFER.SPO_ID = VOUCHER.SPO_ID) WHERE VOUCHER.CUS_ID = :cus_id AND VOU_STATUS = 1;';
            $qry2 = $connection->prepare($sql2);
            $qry2->bindParam(':cus_id', $r['CUS_ID']);
            $qry2->execute();
            while ($r2 = $qry2->fetch(PDO::FETCH_ASSOC)) {
                $opened_vouchers .= ($opened_vouchers != '' ? '<br>' : '').$r2['SPO_CODE'];
            }

            $result[] = array(
                'id' => $r['CUS_ID'],
                'name' => $r['CUS_NAME'],
                'email' => $r['CUS_EMAIL'],
                'qt_opened_offers' => $r['QT_OPENED_SPECIAL_OFFERS'],
                'qt_used_offers' => $r['QT_USED_SPECIAL_OFFERS'],
                'opened_vouchers' => $opened_vouchers
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
