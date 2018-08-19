


/********************************************************************************************************************************************
Global variables
********************************************************************************************************************************************/

// Panel object. This will get the result of "document.createElement" method
var panel_bg = null;
var panel = null;

// Objects that will be populated on "CreateSpecialOfferPanel" function and used by "DoInsertSpecialOffer"
var current_id = null;
var obj_spo_name = null;
var obj_spo_percentage = null;

// Buttons on the panel
var obj_panel_save;
var obj_panel_cancel;

// Message panel object
var error_panel = null;
var message_panel = null;

// JSon object containing the list of the special offers loaded on the page
var list_of_special_offer = null;


/********************************************************************************************************************************************
Enable/Disable elements on the page
********************************************************************************************************************************************/

function SpecialOfferFields(enabled_value)
{
    /**
    Set a value to the ENABLED parameter of each object related to the special_offer card

    :param enabled_value Boolean:
        The value to be set on the ENABLED parameter of the object
    */
    obj_spo_name.enabled = enabled_value;
    obj_spo_percentage.enabled = enabled_value;
    //obj_panel_save.enabled = enabled_value;
    //obj_panel_cancel.enabled = enabled_value;
}


function ActivateSpecialOfferFields()
{
    /**
    Turn all object related to the special_= offer card able to be interact by the user
    */
    SpecialOfferFields(true);
}


function DeactivateSpecialOfferFields()
{
    /**
    Turn all object related to the special offer card disable to be interact by the user
    */
    SpecialOfferFields(false);
}


/********************************************************************************************************************************************
Query the list of special_offers
********************************************************************************************************************************************/

function QuerySpecialOffer()
{
    /**
    Query the list of special offers
    */

    var word_to_search = obj_special_offer_to_search.value;

    try {

        //Populate the parameter
        var params = FormatParamQuerySpecialOffer(word_to_search);

        http_exec_special_offer.open('POST', 'cmd/special_offer.php', true);
        http_exec_special_offer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_special_offer.onreadystatechange = QuerySpecialOfferResponse;
        http_exec_special_offer.send(params);

    } catch (error) {
        addErrorMessage(error);
        //addErrorMessage(error.message);
    }
}


function QuerySpecialOfferResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        PopulateSpecialOfferList(resp);
    }
}


function PopulateSpecialOfferList(json_string)
{
    list_of_special_offer = JSON.parse(json_string);

    var total_open_voucher = 0;
    var total_used_voucher = 0;

    var lines = '';
    for (i = 0; i < list_of_special_offer.length; i++) {
        var id = list_of_special_offer[i].id;
        var name = list_of_special_offer[i].name;
        var percentage = list_of_special_offer[i].percentage;
        var qt_open_voucher = list_of_special_offer[i].qt_open_voucher;
        var qt_used_voucher = list_of_special_offer[i].qt_used_voucher;
        var spo_code = list_of_special_offer[i].spo_code;

        total_open_voucher += parseInt(qt_open_voucher);
        total_used_voucher += parseInt(qt_used_voucher);

        var lin = '';
        lin += '<tr>';
        lin += '    <td id="special_offer' + id + '_name">' + name + '</td>';
        lin += '    <td id="special_offer' + id + '_percentage">' + percentage + '</td>';
        lin += '    <td>' + qt_open_voucher + '</td>';
        lin += '    <td>' + qt_used_voucher + '</td>';
        lin += '    <td>' + spo_code + '</td>';
        lin += '    <td>';
        lin += '        <div class="btn btn-edit" onClick="EditSpecialOffer(' + id + ');">Edit</div>';
        lin += '        <div class="btn btn-del" onclick="DeleteSpecialOffer(' + id + ');">Delete</div>';
        lin += '        <div style="clear:both;"></div>';
        lin += '    </td>';
        lin += '</tr>';

        lines += lin;
    }

    obj_available_vouchers.innerHTML = total_open_voucher;
    obj_used_vouchers.innerHTML = total_used_voucher;

    obj_list_of_special_offers.innerHTML = lines;
}


/********************************************************************************************************************************************
Format the parameter to be used on Requests
********************************************************************************************************************************************/

function FormatParamSpecialOffer()
{
    var params = '';
    params += POSTinitP;
    for (var i = 0; i < arguments.length; i++) {
        params += jsP[i] + arguments[i];
    }
    return params;
}

function FormatParamCommandSpecialOffer(command, id, name, percentage) {
    return FormatParamSpecialOffer(
        command, // command
        '', // search
        '', // order by
        '', // offset
        '', // quantity
        id, // special_offer id
        name, // special offer name
        percentage // special offer percentage
    );
}

function FormatParamQuerySpecialOffer(word_to_search) {
    return FormatParamSpecialOffer(
        COMMAND_QUERY, // command
        word_to_search, // search
        '', // order by
        '', // offset
        '', // quantity
        '', // special_offer id
        '', // special offer name
        '' // special offer percentage
    );
}


/********************************************************************************************************************************************
SpecialOffer panel used for Insert, Edit or Delete command
********************************************************************************************************************************************/

function CreateSpecialOfferPanel(name, percentage, command)
{
    /**
    Create a panel for insert/editing special_offers and add on the body

    :param name String:
        The name of the special_offer that will be showing on the input name

    :param percentage String:
        The percentage of the special_offer that will be showing on the input percentage

    :param command String:
        'Insert'
        'Edit'
        'Delete'
    */

    // Format the title according the Command parameter
    var title = '';
    switch (command) {
        case COMMAND_INSERT : title = PANEL_TITLE_ADDING_SPECIAL_ORDER; break;
        case COMMAND_EDIT : title = PANEL_TITLE_EDITING_SPECIAL_ORDER; break;
        case COMMAND_DELETE : title = PANEL_TITLE_DELETING_SPECIAL_ORDER; break;
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
    panel_content += '        <input type="text" id="spo_name" value="' + name + '" placeholder="SpecialOffer name" />';
    panel_content += '    </div>';
    panel_content += '    <div class="lin">';
    panel_content += '        <label>Percentage</label>';
    panel_content += '        <input type="text" id="spo_percentage" value="' + percentage + '" placeholder="only the number" />';
    panel_content += '    </div>';
    panel_content += '    <div class="buttons">';
    panel_content += '        <div class="btn" id="panel_save" onclick="Do' + command + 'SpecialOffer();">Save</div>';
    panel_content += '        <div class="btn" id="panel_cancel" onclick="DoCancel' + command + 'SpecialOffer();">Cancel</div>';
    panel_content += '        <div style="clear:both;"></div>';
    panel_content += '    </div>';

    panel.innerHTML = panel_content;

    // Insert the panel and background panel to the body
    document.body.appendChild(panel_bg);
    document.body.appendChild(panel);

    // Associate the objects of the panel into variables
    obj_spo_name = document.getElementById('spo_name');
    obj_spo_percentage = document.getElementById('spo_percentage');
}


function DestroySpecialOfferPanel()
{
    /**
    Remove the special offer panel from the body
    */
    document.body.removeChild(panel_bg);
    document.body.removeChild(panel);
}


/********************************************************************************************************************************************
Insert new special offer
********************************************************************************************************************************************/

function InsertSpecialOffer()
{
    /**
    Open the panel NewSpecialOffer
    */
    CreateSpecialOfferPanel('', '', COMMAND_INSERT);
}


function DoCancelInsertSpecialOffer()
{
    /**
    Destroy the panel NewSpecialOffer
    */
    DestroySpecialOfferPanel();
}


function DoInsertSpecialOffer()
{
    /**
    Save the new special offer
    */

    var name = obj_spo_name.value;
    var percentage = obj_spo_percentage.value;

    DeactivateSpecialOfferFields();
    try {

        //Populate the parameter
        var params = FormatParamCommandSpecialOffer(COMMAND_INSERT, '', name, percentage);

        http_exec_special_offer.open('POST', 'cmd/special_offer.php', true);
        http_exec_special_offer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_special_offer.onreadystatechange = InsertSpecialOfferResponse;
        http_exec_special_offer.send(params);

    } catch (error) {
        ActivateSpecialOfferFields();
        addErrorMessage(error);
        //addErrorMessage(error.message);
    }
}


function InsertSpecialOfferResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        PopulateSpecialOfferList(resp);
        DestroySpecialOfferPanel();
    }
}


/********************************************************************************************************************************************
Edit a special offer
********************************************************************************************************************************************/

function EditSpecialOffer(id)
{
    /**
    Open the panel EditSpecialOffer
    */
    current_id = id;
    var name = document.getElementById('special_offer' + current_id + '_name').innerHTML;
    var percentage = document.getElementById('special_offer' + current_id + '_percentage').innerHTML;
    CreateSpecialOfferPanel(name, percentage, COMMAND_EDIT);
}


function DoCancelEditSpecialOffer()
{
    /**
    Destroy the panel NewSpecialOffer
    */
    current_id = null;
    DestroySpecialOfferPanel();
}


function DoEditSpecialOffer()
{
    /**
    Save the new special offer
    */

    var name = obj_spo_name.value;
    var percentage = obj_spo_percentage.value;

    DeactivateSpecialOfferFields();
    try {

        //Populate the parameter
        var params = FormatParamCommandSpecialOffer(COMMAND_EDIT, current_id, name, percentage);
        console.log(params);

        http_exec_special_offer.open('POST', 'cmd/special_offer.php', true);
        http_exec_special_offer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_special_offer.onreadystatechange = EditSpecialOfferResponse;
        http_exec_special_offer.send(params);

        current_id = null;

    } catch (error) {
        ActivateSpecialOfferFields();
        addErrorMessage(error.message);
    }
}


function EditSpecialOfferResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        PopulateSpecialOfferList(resp);
        DestroySpecialOfferPanel();
    }
}


/********************************************************************************************************************************************
Delete a special offer
********************************************************************************************************************************************/

function DeleteSpecialOffer(id)
{
    /**
    Open the panel EditSpecialOffer
    */
    current_id = id;
    if (confirm('Are you sure?')) {
        DoDeleteSpecialOffer();
    }
    else {
        DoCancelDeleteSpecialOffer();
    }
}


function DoCancelDeleteSpecialOffer()
{
    /**
    Destroy the panel NewSpecialOffer
    */
    current_id = null;
}


function DoDeleteSpecialOffer()
{
    /**
    Save the new special offer
    */

    try {

        //Populate the parameter
        var params = FormatParamCommandSpecialOffer(COMMAND_DELETE, current_id, '', '');

        http_exec_special_offer.open('POST', 'cmd/special_offer.php', true);
        http_exec_special_offer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_exec_special_offer.onreadystatechange = EditSpecialOfferResponse;
        http_exec_special_offer.send(params);

        current_id = null;

    } catch (error) {
        addErrorMessage(error.message);
    }
}


function DeleteSpecialOfferResponse() {
    if (this.readyState == 4 && this.status == 200) {
        var resp = String( this.responseText );
        PopulateSpecialOfferList(resp);
    }
}


/********************************************************************************************************************************************
Manage offers from a special offer
********************************************************************************************************************************************/

function ManageOffersFromSpecialOffer(id)
{

}


