
function InitializeDashboard()
{
    // number of available vouchers
    obj_available_vouchers.innerHTML = '0';

    // number of used vouchers in total
    obj_used_vouchers.innerHTML = '0';
}


function InitializeEvents()
{
    // Button to create new special order
    obj_btn_new_special_offer.onclick = InsertSpecialOffer;

    // Button to create new customer
    obj_btn_new_customer.onclick = InsertCustomer;

    // Button to search special orders
    obj_search_special_offer.onclick = QuerySpecialOffer;

    // Button to search customers
    obj_search_customer.onclick = QueryCustomer;
}


function Initialize()
{
    // Initialize the dashboard counters
    InitializeDashboard();

    // Set a event functions to some HTML elements
    InitializeEvents();

    // Query customers
    QueryCustomer();

    // Query customers
    QuerySpecialOffer();
}