<?php
require('../conn/constants.php' );
session_start();
require('../conn/csrf.class.php' );
require('../conn/csrf_init.php' );
session_write_close();
require('voucher_model.php');

// CSRF protection
if (!$csrf->check_valid('post') && !$csrf->check_valid('get')) {
    MyShortEcho('Token is unkowned!');
    exit;
}


// Get parameters
$command = isset($_POST[$POST_params['a0']]) ? $_POST[$POST_params['a0']] : NULL;
$cus_id = isset($_POST[$POST_params['b0']]) ? $_POST[$POST_params['b0']] : NULL;
$spo_id = isset($_POST[$POST_params['c0']]) ? $_POST[$POST_params['c0']] : NULL;
$email = isset($_POST[$POST_params['d0']]) ? $_POST[$POST_params['d0']] : NULL;
$code = isset($_POST[$POST_params['e0']]) ? $_POST[$POST_params['e0']] : NULL;


switch ($command) {

    case $COMMAND_INSERT :
        $result = AddVoucher($cus_id, $spo_id);
        $json = json_encode(array('result' => 'ok'));
        MyShortEcho($json, 'application/json');
        break;

    case $COMMAND_QUERY :
        $result = UseVoucher($email, $code);
        $json = json_encode($result);
        MyShortEcho($json, 'application/json');
        break;

}
