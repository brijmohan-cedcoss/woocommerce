>Function register_endpoint_custom_tab().
    - Registers an endpoint with a name "last-order-endpoint".
    -Hooked with register_activation_hook, so when the plugin activates, it flushes the permalink rewrite rules.
    -After plugin deactivation also it flushes permalink rewrite rules.

>Function add_custom_endpoint_query_var( $vars ).
    -registers a query variable to the egistered endpoint.

>Function custom_last_order_tab( $items ).
    -Here we insert the registred endpoint as a tab in the My account page menu.
    -Firstly, logout tab was unset , and at that place custom tab was inserted, then logout tab was inserted back so the custom tab appears just before logout tab.

>Function check_customers_order().
    -Here we check if the customer has made any earlier purchase or not.
    -For this, we are check for only one order, as it is enought to check if any earlier purachse has been made or not.
    -if yes, the function returns boolean value true, and false if not.


>Function populate_last_order_endpoint().
    -Now, here is the time when we show the content in the custom tab.
    -First we check if the customer has any earlier purachse or not, if yeas than we show the data of last purachse, else display the message that no purchase has been made yet.
    -Then, we get the user id using get_current_user_id() function.
    -Then we get the last order associated with the current user, using wc_get_customer_last_order().
    -To show the all the order items, we frist fetch the items using get_items(), then using a foreach loop we show the different details of each of order items.
    -And all the dynamic values wil be shown as per data fetched from database.
    -Then hook the function to "woocommerce_account_{endpoint}_endpoint"

>Function last_order_endpoint_title( $title ).
    -Here we use the query variable that we registered earlier, using global variable $wp_query
    -First we check whether the registered endpoint is set or not.
    -Then in a if statement we check whether
            -the current request is not for an administravtive interface page,
            -the query is the main query or not,
            -the caller is in the loop or not,
            -the page is my account page or not.
    -If all the conditions return true, then we change the the title of the endpoint and return the value.
    -And hook the function with 'the_title' hook.