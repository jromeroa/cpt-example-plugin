<?php
/*
Plugin Name: CPT Example Plugin
Description: Register a CPT with a custom meta field and expose it in the REST API.
Version: 1.0
Author: Eng. Jhonny Romero
*/

// Register the CPT (Custom Post Type)
function register_example_cpt() {
    $labels = array(
        'name'               => 'CPT Example',
        'singular_name'      => 'CPT Example',
        'menu_name'          => 'CPT Example',
        'add_new_item'       => 'Add New CPT Example',
        'edit_item'          => 'Edit CPT Example',
        'view_item'          => 'View CPT Example',
        'view_items'         => 'View CPTs Example',
        'search_items'       => 'Search CPTs Example',
        'not_found'          => 'No CPTs Example found',
        'not_found_in_trash' => 'No CPTs Example found in Trash'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'capability_type'    => 'post', //type post
        'supports'           => array( 'title', 'editor', 'custom-fields' ),
        'show_in_rest'       => true, //Visibility
    );

    register_post_type( 'example_cpt', $args );
}
add_action( 'init', 'register_example_cpt' );

//Custom meta field
function register_example_meta_field() {
    register_post_meta( 'example_cpt', 'example_meta', array(
        'type'         => 'string',
        'single'       => true,
        'show_in_rest' => true, // Visibility
    ) );
}
add_action( 'init', 'register_example_meta_field' );

//Add the text box to manage the meta field in the edit interface
function add_example_meta_box() {
    add_meta_box(
        'example_meta_box',
        'Example Meta',
        'show_example_meta_box',
        'example_cpt',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_example_meta_box' );

//Show the text box to manage the meta field
function show_example_meta_box( $post ) {
    // Get the current value of the field
    $example_meta_value = get_post_meta( $post->ID, 'example_meta', true );
    ?>
    <label for="example_meta">Example Meta:</label>
    <input type="text" id="example_meta" name="example_meta" value="<?php echo esc_attr( $example_meta_value ); ?>" style="width: 100%;">
    <?php
}
//Expose the meta field in the REST API
function expose_meta_field_in_api( $response, $post, $request ) {
    $example_meta_value = get_post_meta( $post->ID, 'example_meta', true );
    $response->data['example_meta'] = $example_meta_value;
    return $response;
}

//Save the field value when the post is saved or updated
function save_example_meta_field( $post_id ) {
    if ( isset( $_POST['example_meta'] ) ) {
        update_post_meta( $post_id, 'example_meta', sanitize_text_field( $_POST['example_meta'] ) );
    }
}

add_action( 'save_post_example_cpt', 'save_example_meta_field' );
add_filter( 'rest_prepare_example_cpt', 'expose_meta_field_in_api', 10, 3 );
