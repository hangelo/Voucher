
function getXmlDoc()
{
    /**
    Return the Request object according to the browser
    */
    return ( window.XMLHttpRequest ) ? new XMLHttpRequest() : new ActiveXObject( "Microsoft.XMLHTTP" );
}


// Request object
var http_exec_customer = getXmlDoc();
var http_exec_special_offer = getXmlDoc();
var http_exec_voucher = getXmlDoc();