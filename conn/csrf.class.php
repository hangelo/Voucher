<?php

/*
A classe CSRF serve para proteção sobre CSRF (Cross Site Request Forgery).
A idéia é criar identificadores aleatórios que serão os parâmetros usados em POST/GET,
 gerando a chave com um TOKEN criado no início da sessão do usuário.
Isso garante que cada sessão gere um Token diferente, consequentemente os parâmetros também serão diferentes.


Modo de uso:

<?php
session_start();
include 'csrf.class.php';

$csrf = new csrf();


// Generate Token Id and Valid
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

// Generate Random Form Names
$form_names = $csrf->form_names(array('user', 'password'), false);


if(isset($_POST[$form_names['user']], $_POST[$form_names['password']])) {
        // Check if token id and token value are valid.
        if($csrf->check_valid('post')) {
                // Get the Form Variables.
                $user = $_POST[$form_names['user']];
                $password = $_POST[$form_names['password']];

                // Form Function Goes Here
        }
        // Regenerate a new random value for the form.
        $form_names = $csrf->form_names(array('user', 'password'), true);
}

?>

<form action="index.php" method="post">
    <input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
    <input type="text" name="<?= $form_names['user']; ?>" /><br/>
    <input type="text" name="<?= $form_names['password']; ?>" />
    <input type="submit" value="Login"/>
</form>

*/

class csrf {

    public function get_token_id() {
        if ( isset( $_SESSION[ 'token_id' ] ) ) return $_SESSION[ 'token_id' ];
        else {
            $token_id = $this->random( 10 );
            $_SESSION[ 'token_id' ] = $token_id;
            return $token_id;
        }
    }

    public function get_token() {
        if ( isset( $_SESSION[ 'token_value' ] ) ) return $_SESSION[ 'token_value' ];
        else {
            $token = hash( 'sha256', $this->random( 500 ) );
            $_SESSION[ 'token_value' ] = $token;
            return $token;
        }

    }

    public function check_valid( $method ) {
        if ( $method == 'post' || $method == 'get' ) {
            $post = $_POST;
            $get = $_GET;
            if ( isset( ${ $method }[ $this->get_token_id() ] ) && ( ${ $method }[ $this->get_token_id() ] == $this->get_token() ) ) return true;
            else return false;
        }
        else return false;
    }

    public function form_names( $names, $regenerate ) {

        $values = array();
        foreach ( $names as $n ) {
            if ( $regenerate == true ) unset( $_SESSION[ $n ] );
            $s = isset( $_SESSION[ $n ] ) ? $_SESSION[ $n ] : $this->random( 10 );
            $_SESSION[ $n ] = $s;
            $values[ $n ] = $s;
        }
        return $values;
    }

    private function random( $len ) {
        if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
            $byteLen = intval( ( $len / 2 ) + 1 );
            $return = substr( bin2hex( openssl_random_pseudo_bytes( $byteLen ) ), 0, $len );
        } else if ( @is_readable( '/dev/urandom' ) ) {
            $f=fopen( '/dev/urandom', 'r' );
            $urandom = fread( $f, $len );
            fclose( $f );
            $return = '';
        }

        if ( empty( $return ) ) {
            for ( $i = 0; $i < $len; ++$i ) {
                if ( !isset( $urandom ) ) {
                    if ( $i % 2 == 0 ) mt_srand( time() % 2147 * 1000000 + ( double )microtime() * 1000000 );
                    $rand=48+mt_rand()%64;
                }
                else $rand = 48 + ord( $urandom[ $i ] ) % 64;
                if ( $rand > 57 ) $rand += 7;
                if ( $rand > 90 ) $rand += 6;
                if ( $rand == 123 ) $rand = 52;
                if ( $rand == 124 ) $rand = 53;
                $return .= chr( $rand );
            }
        }

        return $return;
    }

}

