


/********************************************************************************************************************************************
Global variables
********************************************************************************************************************************************/

// Panel object. This will get the result of "document.createElement" method
var panel_bg = null;
var panel = null;

// Objects that will be use a voucher
var obj_cus_email = null;
var obj_spo_code = null;



/********************************************************************************************************************************************
Format the parameter to be used on Requests
********************************************************************************************************************************************/

function FormatParamVoucher()
{
    var params = '';
    params += POSTinitP;
    for (var i = 0; i < arguments.length; i++) {
        params += jsP[i] + arguments[i];
    }
    return params;
}

function FormatParamCommandVoucher(command, email, code) {
    return FormatParamVoucher(
        command, // command
        '', // cus_id
        '', // spo_id
        email,
        code
    );
}


/********************************************************************************************************************************************
Customer panel used for Insert, Edit or Delete command
********************************************************************************************************************************************/

function CreateUseVoucherPanel(email, code)
{
    /**
    Create a panel for insert/editing customers and add on the body

    :param email String:
        The emmail of the customer

    :param code String:
        The code of the special offer
    */

    // Create the background panel element object
    panel_bg = document.createElement('div');
    panel_bg.className = 'panel_bg';
    panel_bg.id = 'panel_bg';

    // Create the panel element object
    panel = document.createElement('div');
    panel.className = 'panel';
    panel.id = 'panel';

    // Create the content of the panel
    var panel_content = '';
    panel_content += '    <div class="tit">Using a Voucher</div>';
    panel_content += '    <div class="lin">';
    panel_content += '        <label>Customer Email</label>';
    panel_content += '        <input type="text" id="cus_email" value="' + email + '" placeholder="user@domain.com" />';
    panel_content += '    </div>';
    panel_content += '    <div class="lin">';
    panel_content += '        <label>Voucher Code</label>';
    panel_content += '        <input type="text" id="spo_code" value="' + code + '" placeholder="Special Offer Code" />';
    panel_content += '    </div>';
    panel_content += '    <div class="buttons">';
    panel_content += '        <div class="btn" id="panel_save" onclick="DoUseVoucher();">Save</div>';
    panel_content += '        <div class="btn" id="panel_cancel" onclick="DoCancelUseVoucher();">Cancel</div>';
    panel_content += '        <div style="clear:both;"></div>';
    panel_content += '    </div>';

    panel.innerHTML = panel_content;

    // Insert the panel and background panel to the body
    document.body.appendChild(panel_bg);
    document.body.appendChild(panel);

    // Associate the objects of the panel into variables
    obj_cus_email = document.getElementById('cus_email');
    obj_spo_code = document.getElementById('spo_code');
}


function DestroyUseVoucherPanel()
{
    /**
    Remove the customer panel from the body
    */
    document.body.removeChild(panel_bg);
    document.body.removeChild(panel);
}


/********************************************************************************************************************************************
Insert new customer
********************************************************************************************************************************************/

function UseVoucher()
{
    /**
    Open the panel NewCustomer
    */
    CreateUseVoucherPanel('', '');
}


function DoCancelUseVoucher()
{
    /**
    Destroy the panel NewCustomer
    */
    DestroyUseVoucherPanel();
}


function DoUseVoucher()
{
    /**
    Save the new customer
    */

    var email = obj_cus_email.value;
    var code = obj_spo_code.value;
    try {

        //Populate the parameter
        var params = FormatParamCommandVoucher(COMMAND_QUERY, email, code);

        http_exec_customer.open('POST', 'cmd/voucher.php', true);
        http_exec_customer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_customer.onreadystatechange = UseVoucherResponse;
        http_exec_customer.send(params);

    } catch (error) {
        addErrorMessage(error);
        //addErrorMessage(error.message);
    }
}


function UseVoucherResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var json_string = String( this.responseText );
        var resp = JSON.parse(json_string);

        if (resp.error == 'no') {
            alert('Voucher accept, dicount: ' + resp.percentage);
        }
        else {
            console.log(resp.error);
            alert(resp.error);
        }

        QueryCustomer();
        QuerySpecialOffer();
        DestroyUseVoucherPanel();
    }
}

