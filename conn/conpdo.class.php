<?php


function criptografar_whirlpool__doubleSalt($toHash, $str)
{
    /**
    Generate a hash to be used to store passwords using Whirlpool method

    :param $toHash Array:

    :param $str String:
    */
    $v_toHash = str_split($toHash, (strlen($toHash) / 2) + 1);
    $hash = hash('whirlpool', $str.$v_toHash[ 0 ].'@#$.angelo'.$v_toHash[ 1 ]);
    return $hash;
}


function generate_salt($length = 16)
{
    /**
    Gerenare a random String, including letters, numbers and some special characters.
    Mainly used for create Salt Hash

    :param $length integer:
        The length of the string that will be returned

    :return String
        Randon string with passed length
    */

    // Possible characters
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.'0123456789-=~!@#$%^&*()_+,./<>?;:[]{}\|';

    $str = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[ rand(0, $max) ];
    }

    return $str;
}


function generate_str_random($length)
{
    /**
    Generate a randon String, including only letters and numbes

    :param $length integer:
        The length of the string that will be returned

    :return String
        Randon string with passed length
    */

    // Possible characters
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.'0123456789';
    $str = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[ rand(0, $max) ];
    }

    return $str;
}


// A Database Instance global variable, to be used from Transaction functions below
$_db_transaction_started = false;

function StartTransaction(&$connection, &$transaction)
{
    /**
    :param &$connection:
        The instance
        Parameter by reference

    :param &$transaction:
        The variable representing the transaction
        Parameter by reference
    */
    global $_db_transaction_started;
    if ($_db_transaction_started) {
        return false;
    }
    $connection = ConnDB::getInstance();
    $transaction = new Transaction($connection);
    $_db_transaction_started = true;
    return true;
}


// faz um comit em uma transação e fecha a conexão ao banco de dados

function CommitTransaction(&$connection, &$transaction)
{
    /**
    Commit the transaction and close the database connection

    :param &$connection:
        The instance
        Parameter by reference

    :param &$transaction:
        The variable representing the transaction
        Parameter by reference
    */
    global $_db_transaction_started;
    $transaction->commit();
    $connection->close();
    $_db_transaction_started = false;
}


// faz rollback em uma transação e fecha a conexão ao banco de dados

function RollbackTransaction(&$connection, &$transaction, $erro)
{
    /**
    Rollback the transaction and close the connection

    :param &$connection:
        The instance
        Parameter by reference

    :param &$transaction:
        The variable representing the transaction
        Parameter by reference

    :param $erro:
        The error message
    */
    global $_db_transaction_started;
    $transaction->rollback();
    $connection->close();
    $_db_transaction_started = false;
    ShowException($erro);
    exit;
}


// tratamento sobre exibir ou não mensagens de excessão/erros

function ShowException($str)
{
    /**
    Receives an error message from an Exception and stop the PHP processing, showing a message alert

    :param $str String:
        The message from an Exception
    */
    echo 'Estamos fazendo manutenção em alguns de nossos servidores. Retornaremos dentro de alguns minutos.';
    //echo $str; ONLY FOR DEBUG
    exit;
}


/*********************************************************************************************************************************
Database auxiliare function
*********************************************************************************************************************************/

function SaveDBQuery($sp, $sql, &$connection, &$qry_param)
{
    /**
    Execute a Prepare statement with an Stored Procedure call

    :param $sp String:
        The name of the stored procedure

    :param $sql:
        The complete SQL code that was executed

    :param &$connection:
        The instance of the Database Connection
        Parameter by reference

    :param &$qry_param:
        The intance of where we can get the parameters
        Parameter by reference
    */
    global $_LOGIN__usu_id;
    $description = $sql."\n\n".$qry_param->GetParameters();
    $qry = $connection->prepare('CALL '.$sp.' (:usu_id, :description);');
    $qry->bindParam(':usu_id', $_LOGIN__usu_id);
    $qry->bindParam(':description', $description);
    $qry->execute();
}


/*********************************************************************************************************************************
Transaction class
*********************************************************************************************************************************/

class Transaction {

    private $db = NULL;
    private $finished = FALSE;

    function __construct($db) {
        $this->db = $db;
        $this->db->beginTransaction();
    }

    function __destruct() {
        if (!$this->finished) {
            $this->db->rollback();
        }
    }

    function commit() {
        $this->finished = TRUE;
        $this->db->commit();
    }

    function rollback() {
        $this->finished = TRUE;
        $this->db->rollback();
    }
}


/*********************************************************************************************************************************
Prepare statement class
*********************************************************************************************************************************/

class PrepareReturn extends PDOStatement {

    private $parameters = array();

    private function AddParameter($nome, $valor, $tipo) {
        global $parameters;
        $parameters[ count($parameters) ] = array('nome' => $nome, 'valor' => $valor, 'tipo' => $tipo);
    }

    public function GetParameters() {
        global $parameters;
        $txt = 'Total de parametros: '.count($parameters)."\n";
        for ($i = 0; $i < count($parameters); $i++) {
            $txt .= 'parametro: "'.$parameters[ $i ][ 'nome' ].'"'."\n".'valor: "'.$parameters[ $i ][ 'valor' ].'"'."\n".'tipo: "'.$parameters[ $i ][ 'tipo' ].'"'."\n\n";
        }
        return $txt;
    }

    function getPDOConstantType($var)
    {
        /**
        See the type of the variable and return the PDO Type Constant
        */
        if (is_int($var)) return PDO::PARAM_INT;
        if (is_bool($var)) return PDO::PARAM_BOOL;
        if (is_null($var)) return PDO::PARAM_NULL;
        return PDO::PARAM_STR;
    }

    public function bindParam($paramno, $param, $type=null, $maxlen=null, $driverdata=null) {
        $type = $this->getPDOConstantType($param);
        $this->AddParameter($paramno, $param, $type);
        return parent::bindParam($paramno, $param, $type, $maxlen, $driverdata);
    }

    public function bindColumn($column, &$param, $type = NULL, $maxlen = NULL, $driverdata = NULL) {
        return parent::bindColumn($column, $param, $type, $maxlen, $driverdata);
    }

    public function bindValue($column, $param, $type = NULL) {
        return parent::bindValue($column, $param, $type);
    }

    public function fetchSingle() {
        return $this->fetchColumn(0);
    }

    public function fetchAssoc() {
        $this->setFetchMode(PDO::FETCH_ASSOC);
        $data = $this->fetch();
        return $data;
    }

    public function fetch($how = NULL, $orientation = NULL, $offset = NULL) {
        $vr = parent::fetch($how, $orientation, $offset);
        return $vr;
    }
}


/*********************************************************************************************************************************
Database class, inherited from PDO
*********************************************************************************************************************************/

class ConnDB extends pdo {
    /**
    Database connection class
    */

    // Status of the connection
    private static $connected = false;

    // Instance of this same object
    private static $instance = null;


    public function __construct($dns, $username, $password)
    {
        /**
        Constructor method. Call the Constructor on the parent too
        */
        parent::__construct($dns, $username, $password);
    }


    public function  __destruct()
    {
        /**
        When the object is destructed, we close the connection first
        */
        self::$instance->close();
        self::$instance = null;
    }


    public function close()
    {
        /**
        Override Close method, closing the instance for singleton method
        */
        if (self::$connected) {
            parent::close();
            self::$connected = false;
        }
    }


    public static function getInstance()
    {
        /**
        Get/Create the instante in Singleton mode. If the instante already have been created, just return it. Otherwise, create it

        :return:
            An instance of this same object
        */

        $DB_CONNECTION = array(
          'Cloud platform' => 'AWS Amazon',
          'Server Location' => 'South America (São Paulo)',
          'DB Instance Identifier' => 'DB Instace',
          'URL' => 'DB URL',
          'DB Name' => 'DB NAME',
          'Username' => 'DB USERNAME',
          'Password' => 'DB PASSWORD'
        );

        if (!isset(self::$instance)) {
            try {
                $dns = 'mysql:host='.$DB_CONNECTION['URL'].';dbname='.$DB_CONNECTION['DB Name'].';charset=utf8';
                self::$instance = new self($dns, $DB_CONNECTION['Username'], $DB_CONNECTION['Password']);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
            catch (PDOException $e) {
                die(ShowException($e->getMessage()));
            }
        }
        return self::$instance; // Se já existe instancia na memória eu a retorno
    }


    public function query($sql, $params = array())
    {
        /**
        Override the Query statement of the PDO class

        :param $sql String:
            The SQL statement

        :param $params array:
            Default value = array()

        :return:
            Prepare statement
        */
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }


    public function prepare($sql, $options = NULL)
    {
        /**
        Override the Prepare statement of the PDO class

        :param $sql String:
            The SQL statement

        :param $options Array:
            This overrided function does not use it, but it needed to make a perfect overrided call
            Default Value = NULL

        :return:
            Prepare statement
        */
        $stmt = parent::prepare($sql, array(PDO::ATTR_STATEMENT_CLASS => array('PrepareReturn')));
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($stmt) {
            return $stmt;
        }
        else {
            throw new Exception('Query Exception: '.parent::errorInfo().' numero:'.parent::errorCode());
        }
    }

}

