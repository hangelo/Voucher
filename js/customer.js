


/********************************************************************************************************************************************
Global variables
********************************************************************************************************************************************/

// Panel object. This will get the result of "document.createElement" method
var panel_bg = null;
var panel = null;

// Objects that will be populated on "CreateCustomerPanel" function and used by "DoInsertCustomer"
var current_id = null;
var obj_cus_name = null;
var obj_cus_email = null;
var obj_adding_special_offer_to_customer = null;

// Buttons on the panel
var obj_panel_save;
var obj_panel_cancel;

// Message panel object
var error_panel = null;
var message_panel = null;

// JSon object containing the list of the customers loaded on the page
var list_of_customer = null;


/********************************************************************************************************************************************
Enable/Disable elements on the page
********************************************************************************************************************************************/

function CustomerFields(enabled_value)
{
    /**
    Set a value to the ENABLED parameter of each object related to the customer card

    :param enabled_value Boolean:
        The value to be set on the ENABLED parameter of the object
    */
    obj_cus_name.enabled = enabled_value;
    obj_cus_email.enabled = enabled_value;
    //obj_panel_save.enabled = enabled_value;
    //obj_panel_cancel.enabled = enabled_value;
}


function ActivateCustomerFields()
{
    /**
    Turn all object related to the customer card able to be interact by the user
    */
    CustomerFields(true);
}


function DeactivateCustomerFields()
{
    /**
    Turn all object related to the customer card disable to be interact by the user
    */
    CustomerFields(false);
}


/********************************************************************************************************************************************
Query the list of customers
********************************************************************************************************************************************/

function QueryCustomer()
{
    /**
    Query the list of customers
    */

    var word_to_search = obj_customer_to_search.value;

    try {

        //Populate the parameter
        var params = FormatParamQueryCustomer(word_to_search);

        http_exec_customer.open('POST', 'cmd/customer.php', true);
        http_exec_customer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_customer.onreadystatechange = QueryCustomerResponse;
        http_exec_customer.send(params);

    } catch (error) {
        addErrorMessage(error);
        //addErrorMessage(error.message);
    }
}


function QueryCustomerResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        PopulateCustomerList(resp);
    }
}


function PopulateCustomerList(json_string)
{
    list_of_customer = JSON.parse(json_string);

    var lines = '';
    for (i = 0; i < list_of_customer.length; i++) {
        var id = list_of_customer[i].id;
        var name = list_of_customer[i].name;
        var email = list_of_customer[i].email;
        var qt_opened_offers = list_of_customer[i].qt_opened_offers;
        var qt_used_offers = list_of_customer[i].qt_used_offers;
        var opened_vouchers = list_of_customer[i].opened_vouchers;

        var lin = '';
        lin += '<tr>';
        lin += '    <td id="customer' + id + '_name">' + name + '</td>';
        lin += '    <td id="customer' + id + '_email">' + email + '</td>';
        lin += '    <td>' + qt_opened_offers + '</td>';
        lin += '    <td>' + qt_used_offers + '</td>';
        lin += '    <td>' + opened_vouchers + '</td>';
        lin += '    <td>';
        lin += '        <div class="btn btn-edit" onClick="EditCustomer(' + id + ');">Edit</div>';
        lin += '        <div class="btn btn-del" onclick="DeleteCustomer(' + id + ');">Delete</div>';
        lin += '        <div class="btn btn-manage" onclick="ManageSpecialOffersFromCustomer(' + id + ');">Manage Special Offers</div>';
        lin += '        <div style="clear:both;"></div>';
        lin += '    </td>';
        lin += '</tr>';

        lines += lin;
    }

    obj_list_of_customers.innerHTML = lines;
}


/********************************************************************************************************************************************
Format the parameter to be used on Requests
********************************************************************************************************************************************/

function FormatParamCustomer()
{
    var params = '';
    params += POSTinitP;
    for (var i = 0; i < arguments.length; i++) {
        params += jsP[i] + arguments[i];
    }
    return params;
}


function FormatParamCommandCustomer(command, id, name, email)
{
    return FormatParamCustomer(
        command, // command
        '', // search
        '', // order by
        '', // offset
        '', // quantity
        id, // customer id
        name, // customer name
        email // customer email
    );
}


function FormatParamQueryCustomer(word_to_search)
{
    return FormatParamCustomer(
        COMMAND_QUERY, // command
        word_to_search, // search
        '', // order by
        '', // offset
        '', // quantity
        '', // customer id
        '', // customer name
        '' // customer email
    );
}

function FormatParamSpoToCus(command, cus_id, spo_id)
{
    return FormatParamCustomer(
        command, // command
        cus_id,
        spo_id
    );
}


/********************************************************************************************************************************************
Customer panel used for Insert, Edit or Delete command
********************************************************************************************************************************************/

function CreateCustomerPanel(name, email, command)
{
    /**
    Create a panel for insert/editing customers and add on the body

    :param name String:
        The name of the customer that will be showing on the input name

    :param email String:
        The email of the customer that will be showing on the input email

    :param command String:
        'Insert'
        'Edit'
        'Delete'
    */

    // Format the title according the Command parameter
    var title = '';
    switch (command) {
        case COMMAND_INSERT : title = PANEL_TITLE_ADDING_CUSTOMER; break;
        case COMMAND_EDIT : title = PANEL_TITLE_EDITING_CUSTOMER; break;
        case COMMAND_DELETE : title = PANEL_TITLE_DELETING_CUSTOMER; break;
    }

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
    panel_content += '    <div class="tit">' + title + '</div>';
    panel_content += '    <div class="lin">';
    panel_content += '        <label>Name</label>';
    panel_content += '        <input type="text" id="cus_name" value="' + name + '" placeholder="Customer name" />';
    panel_content += '    </div>';
    panel_content += '    <div class="lin">';
    panel_content += '        <label>Email</label>';
    panel_content += '        <input type="text" id="cus_email" value="' + email + '" placeholder="user@domain.com" />';
    panel_content += '    </div>';
    panel_content += '    <div class="buttons">';
    panel_content += '        <div class="btn" id="panel_save" onclick="Do' + command + 'Customer();">Save</div>';
    panel_content += '        <div class="btn" id="panel_cancel" onclick="DoCancel' + command + 'Customer();">Cancel</div>';
    panel_content += '        <div style="clear:both;"></div>';
    panel_content += '    </div>';

    panel.innerHTML = panel_content;

    // Insert the panel and background panel to the body
    document.body.appendChild(panel_bg);
    document.body.appendChild(panel);

    // Associate the objects of the panel into variables
    obj_cus_name = document.getElementById('cus_name');
    obj_cus_email = document.getElementById('cus_email');
}


function DestroyCustomerPanel()
{
    /**
    Remove the customer panel from the body
    */
    document.body.removeChild(panel_bg);
    document.body.removeChild(panel);
}


/********************************************************************************************************************************************
Customer panel to manage vouchers
********************************************************************************************************************************************/

function CreateCustomerPanelManageVouchers()
{
    /**
    */

    // Format the title according the Command parameter
    var title = 'Manage vouchers';


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
    panel_content += '    <div class="tit">' + title + '</div>';
    panel_content += '    <div class="lin">';
    panel_content += '        <label>Special Offer</label>';
    panel_content += '        <select id="select_special_offer">';
    for (i = 0; i < list_of_special_offer.length; i++) {
        var id = list_of_special_offer[i].id;
        var name = list_of_special_offer[i].name;
        var percentage = list_of_special_offer[i].percentage;
        var available_vouchers = list_of_special_offer[i].available_vouchers;
        var used_vouchers = list_of_special_offer[i].used_vouchers;
        panel_content += '    <option value="' + id + '">' + name + ' (' + percentage + ')</option>';
    }
    panel_content += '        </select>';
    panel_content += '    </div>';

    panel_content += '    <div class="buttons">';
    panel_content += '        <div class="btn" id="panel_save" onclick="DoAddSpecialOfferToCustomer();">Save</div>';
    panel_content += '        <div class="btn" id="panel_cancel" onclick="DoCancelAddSpecialOfferToCustomer();">Cancel</div>';
    panel_content += '        <div style="clear:both;"></div>';
    panel_content += '    </div>';

    panel.innerHTML = panel_content;

    // Insert the panel and background panel to the body
    document.body.appendChild(panel_bg);
    document.body.appendChild(panel);

    // Associate the objects of the panel into variables
    obj_adding_special_offer_to_customer = document.getElementById('select_special_offer');
}


function DoCancelAddSpecialOfferToCustomer()
{
    DestroyCustomerPanel();
}


function DoAddSpecialOfferToCustomer()
{
    var spo_id = obj_adding_special_offer_to_customer.value;

    try {

        //Populate the parameter
        var params = FormatParamSpoToCus(COMMAND_INSERT, current_id, spo_id);

        http_exec_customer.open('POST', 'cmd/voucher.php', true);
        http_exec_customer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_customer.onreadystatechange = AddSpecialOfferToCustomerResponse;
        http_exec_customer.send(params);

    } catch (error) {
        addErrorMessage(error);
        //addErrorMessage(error.message);
    }
}


function AddSpecialOfferToCustomerResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        QueryCustomer();
        QuerySpecialOffer();
        DestroyCustomerPanel();
    }
}


/********************************************************************************************************************************************
Manage offers from a customer
********************************************************************************************************************************************/

function ManageSpecialOffersFromCustomer(id)
{
    current_id = id;
    CreateCustomerPanelManageVouchers();
}


/********************************************************************************************************************************************
Insert new customer
********************************************************************************************************************************************/

function InsertCustomer()
{
    /**
    Open the panel NewCustomer
    */
    CreateCustomerPanel('', '', COMMAND_INSERT);
}


function DoCancelInsertCustomer()
{
    /**
    Destroy the panel NewCustomer
    */
    DestroyCustomerPanel();
}


function DoInsertCustomer()
{
    /**
    Save the new customer
    */

    var name = obj_cus_name.value;
    var email = obj_cus_email.value;

    DeactivateCustomerFields();
    try {

        //Populate the parameter
        var params = FormatParamCommandCustomer(COMMAND_INSERT, '', name, email);

        http_exec_customer.open('POST', 'cmd/customer.php', true);
        http_exec_customer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_customer.onreadystatechange = InsertCustomerResponse;
        http_exec_customer.send(params);

    } catch (error) {
        ActivateCustomerFields();
        addErrorMessage(error);
        //addErrorMessage(error.message);
    }
}


function InsertCustomerResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        PopulateCustomerList(resp);
        DestroyCustomerPanel();
    }
}


/********************************************************************************************************************************************
Edit a customer
********************************************************************************************************************************************/

function EditCustomer(id)
{
    /**
    Open the panel EditCustomer
    */
    current_id = id;
    var name = document.getElementById('customer' + current_id + '_name').innerHTML;
    var email = document.getElementById('customer' + current_id + '_email').innerHTML;
    CreateCustomerPanel(name, email, COMMAND_EDIT);
}


function DoCancelEditCustomer()
{
    /**
    Destroy the panel NewCustomer
    */
    current_id = null;
    DestroyCustomerPanel();
}


function DoEditCustomer()
{
    /**
    Save the new customer
    */

    var name = obj_cus_name.value;
    var email = obj_cus_email.value;

    DeactivateCustomerFields();
    try {

        //Populate the parameter
        var params = FormatParamCommandCustomer(COMMAND_EDIT, current_id, name, email);
        console.log(params);

        http_exec_customer.open('POST', 'cmd/customer.php', true);
        http_exec_customer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_customer.onreadystatechange = EditCustomerResponse;
        http_exec_customer.send(params);

        current_id = null;

    } catch (error) {
        ActivateCustomerFields();
        addErrorMessage(error.message);
    }
}


function EditCustomerResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        PopulateCustomerList(resp);
        DestroyCustomerPanel();
    }
}


/********************************************************************************************************************************************
Delete a customer
********************************************************************************************************************************************/

function DeleteCustomer(id)
{
    /**
    Open the panel EditCustomer
    */
    current_id = id;
    if (confirm('Are you sure?')) {
        DoDeleteCustomer();
    }
    else {
        DoCancelDeleteCustomer();
    }
}


function DoCancelDeleteCustomer()
{
    /**
    Destroy the panel NewCustomer
    */
    current_id = null;
}


function DoDeleteCustomer()
{
    /**
    Save the new customer
    */

    try {

        //Populate the parameter
        var params = FormatParamCommandCustomer(COMMAND_DELETE, current_id, '', '');

        http_exec_customer.open('POST', 'cmd/customer.php', true);
        http_exec_customer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_customer.onreadystatechange = EditCustomerResponse;
        http_exec_customer.send(params);

        current_id = null;

    } catch (error) {
        addErrorMessage(error.message);
    }
}


function DeleteCustomerResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        PopulateCustomerList(resp);
    }
}


