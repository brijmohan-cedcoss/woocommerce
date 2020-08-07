> Checking if Woocommerce plugin is active or not, if active then only Woo-extension plugin will work.
    -Checking if ABSPATH constant is define dor not, if 'not' exit.
    -Creating my own path constant( WOO_PLUGIN_DIR_PATH) , if we need to include/require any files in this plugin in future.

> Function add_custom_rating_field()
    -adds a Ratings field in general section of product data.
    -id of the field is 'custom_rating,
    -type is of number format,
    -description tip enabled, with a custom message,
    -Retrives the previous value from database at each visit, if value was assigned earlier else will be empty.

>Function save_custom_rating_field( $id )
    -$id specifies the product id of each product.
    -saves and updates the field data to the database for each product.

>Function display_custom_rating_field()
    -displays ratings as stars on archive page above add to cart button.
    -$rating retrives the rating data from database.
    -$count is used to increment the value after every iteration.

>Function custom_print_name_on_back_tab( $tabs )
    -adds a custom tab ( Print Name on back) in product data section.

>Function populate_print_name_on_back_tab()
    -using this function we populate the fields of cusotm tab
    -Here, I just shown a checkbox to enable/disable the the option of Print Name On Back option.

>Function save_print_name_on_back_tab_fields( $id )
    -saves the fields values to database
    -firstly it checks whether the field is set or not, if set assigns a 'yes' value to it, if not then assigns 'no'.

>Function add_name_to_print_cart_item( $cart_item_data )
    -Here we grab the input field text and associate it with the cart item using woocommerce_add_cart_item_data filter.
    -first we get the value using $_POST
    -Then check if the value is empty, if 'yes' return the cart item data, if 'no' assign it to the array 'cart_item_data' with a key 'name_to_print' and then return it.

>Function display_name_to_print_incart( $item_data, $cart_item )
    -this displays the custom field data into the cart along with te item it is associated.
    -firstly we check if the custom field data exists or not, if 'not' return the cart item as normal.
    -else, add data to item with the help of array using key and value, key is used as title to display.
    



