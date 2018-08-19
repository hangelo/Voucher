<?php
// abre sessão para leitura/escrita
session_start();

// carrega a classe
require('conn/csrf.class.php' );

// Gera o token e informa os parâmetros
require('conn/csrf_init.php' );

// carrega parâmetros sem renovar identificadores, pois foram renovados quando iniciou a sessão
$POST_params = $csrf->form_names( $vet_POST_params, false );

// Fecha a sesão para leitura/escrita
session_write_close();
?>
<html>
    <header>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="css/index.css" rel="stylesheet">

        <script src="js/dictionary.js"></script>
        <script src="js/string.js"></script>
        <script src="js/init.js"></script>
        <script src="js/messages.js"></script>
        <script src="js/request.js"></script>
        <script src="js/customer.js"></script>
        <script src="js/special_offer.js"></script>
        <script src="js/voucher.js"></script>

    </header>
    <body onload="Initialize();">

        <div class="box">
            <div class="pnl">
                <div class="val" id="available_vouchers">300</div>
                <div class="lbl">Available vouchers</div>
            </div>
            <div class="pnl">
                <div class="val" id="used_vouchers">200</div>
                <div class="lbl">Used vouchers</div>
            </div>
            <div style="clear:both;"></div>
        </div>

        <div class="box">
            <div class="tit">Special Offer</div>
            <div class="bar">
                <div class="btn" id="new_special_offer">New Special Offer</div>
                <div class="search-box">
                    <input type="text" id="special_offer_to_search" />
                    <div class="search-btn" id="search_special_offer">Search</div>
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
            <div class="grid">
                <table cellpadding="0" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Discount</td>
                            <td>Available Vouchers</td>
                            <td>Used Vouchers</td>
                            <td>Code</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody id="list_of_special_offers"></tbody>
                </table>
            </div>
        </div>

        <div class="box">
            <div class="tit">Customer</div>
            <div class="bar">
                <div class="btn" id="new_customer">New Customer</div>
                <div class="search-box">
                    <input type="text" id="customer_to_search" />
                    <div class="search-btn" id="search_customer">Search</div>
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
            <div class="grid">
                <table cellpadding="0" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Email</td>
                            <td>Open offers</td>
                            <td>Used offers</td>
                            <td>Vouchers</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody id="list_of_customers">
                    </tbody>
                </table>
                <div style="clear:both;"></div>
            </div>
        </div>

        <div class="UseVoucher" onclick="UseVoucher();">Use a Voucher</div>


        <script>
            // Button to create a new Special Offer
            var obj_btn_new_special_offer = document.getElementById('new_special_offer');

            // Button to create a new Customer
            var obj_btn_new_customer = document.getElementById('new_customer');

            // Number representing the total of available Vouchers
            var obj_available_vouchers = document.getElementById('available_vouchers');

            // Number representing the total of used Vouchers
            var obj_used_vouchers = document.getElementById('used_vouchers');

            // Number representing the total Vouchers that is used on the current day
            var obj_used_today = document.getElementById('used_today');

            // Number representing the date of the last Voucher usage
            var obj_last_used = document.getElementById('last_used');

            // Button to search special offers
            var obj_search_special_offer = document.getElementById('search_special_offer');

            // Text used to make a query on special offers
            var obj_special_offer_to_search = document.getElementById('special_offer_to_search');

            // Button to search customers
            var obj_search_customer = document.getElementById('search_customer');

            // Text used to make a query on customers
            var obj_customer_to_search = document.getElementById('customer_to_search');

            // Container of customer list
            var obj_list_of_customers = document.getElementById('list_of_customers');

            // Container of special offer
            var obj_list_of_special_offers = document.getElementById('list_of_special_offers');
        </script>

        <?php
        // Define as mesmas variáveis para serem usadas em Javascript no Front End
        require('conn/csrf.php' );

        // Our constants
        require('conn/constants.php' );

        // Echo the constant values
        $_const = '';
        $_const .= '<script>';
        $_const .= '    var COMMAND_QUERY = \''.$COMMAND_QUERY.'\';';
        $_const .= '    var COMMAND_INSERT = \''.$COMMAND_INSERT.'\';';
        $_const .= '    var COMMAND_EDIT = \''.$COMMAND_EDIT.'\';';
        $_const .= '    var COMMAND_DELETE = \''.$COMMAND_DELETE.'\';';
        $_const .= '</script>';
        echo $_const;
        ?>

    </body>
</html>