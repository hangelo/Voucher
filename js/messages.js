

/********************************************************************************************************************************************
Error message panel
********************************************************************************************************************************************/

function addErrorMessage(message)
{
    /**
    Insert an error message at the right bottom corner of the page

    :param message string:
        The message the will be display
    */

    if (!error_panel) {
        error_panel = document.createElement('div');
        error_panel.className = 'error-panel';
        error_panel.innerHTML = '<div class="close" onclick="CloseErrorMessage();">x</div><div class="msg" id="error-msg"></div>';
        document.body.appendChild(error_panel);
    }
    document.getElementById('error-msg').innerHTML = message;
}


function CloseErrorMessage()
{
    /**
    Destroy the error panel
    */
    document.body.removeChild(error_panel);
}


/********************************************************************************************************************************************
Informative message panel
********************************************************************************************************************************************/

function addInFoMessage(message)
{
    /**
    Insert an error message at the right bottom corner of the page

    :param message string:
        The message the will be display
    */

    if (!message_panel) {
        message_panel = document.createElement('div');
        message_panel.className = 'message-panel';
        message_panel.innerHTML = '<div class="close" onclick="CloseInfoMessage();">x</div><div class="msg" id="message-msg"></div>';
        document.body.appendChild(message_panel);
    }
    document.getElementById('message-msg').innerHTML = message;
}


function CloseInfoMessage()
{
    /**
    Destroy the error panel
    */
    document.body.removeChild(message_panel);
}