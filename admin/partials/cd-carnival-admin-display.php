<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://sayedakhtar.github.io
 * @since      1.0.0
 *
 * @package    Cd_Carnival
 * @subpackage Cd_Carnival/admin/partials
 */

$args = array(
    'post_type'      => 'page',
    'posts_per_page' => -1, // Retrieve all posts
);

$elementor_pages = get_posts($args);
?>
<div class="wrap">
    <h2>CD Carnival <?php esc_attr_e('Options', 'plugin_name'); ?></h2>

    <form method="post" name="<?php echo $this->plugin_name; ?>" action="options.php">
        <?php
        //Grab all options
        $options = get_option($this->plugin_name);
        $lead_form_name = (isset($options['lead_form_name']) && !empty($options['lead_form_name'])) ? esc_attr($options['lead_form_name']) : '';
        $capture_update = (isset($options['capture_update']) && !empty($options['capture_update'])) ? esc_attr($options['capture_update']) : '';
        $update_visitor_form_name = (isset($options['update_visitor_form_name']) && !empty($options['update_visitor_form_name'])) ? esc_attr($options['update_visitor_form_name']) : '';

        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);

        ?>

        <!-- Text -->
        <fieldset>
            <p><?php esc_attr_e('Please enter the Lead Capture Form Name', 'plugin_name'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Please enter the Lead Capture Form Name', 'plugin_name'); ?></span>
            </legend>
            <input type="text" class="lead_form_name" id="<?php echo $this->plugin_name; ?>-lead_form_name" name="<?php echo $this->plugin_name; ?>[lead_form_name]" value="<?php if (!empty($lead_form_name)) echo $lead_form_name;
                                                                                                                                                                            else echo ''; ?>" />
        </fieldset>

        <fieldset>
            <p><?php esc_attr_e('Please select the update capture redirection Page', 'plugin_name'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Please select the update capture redirection Page', 'plugin_name'); ?></span>
            </legend>
            <label for="capture_update">
                <select name="<?php echo $this->plugin_name; ?>[capture_update]" id="<?php echo $this->plugin_name; ?>-capture_update">
                    <option> Select Elementor page</option>
                    <?php
                    if ($elementor_pages) {
                        foreach ($elementor_pages as $page) {
                    ?>
                            <option <?php if ($capture_update == $page->ID) echo 'selected="selected"'; ?> value="<?php echo $page->ID ?>"><?php echo $page->post_title; ?></option>
                    <?php }
                    } else {
                        echo '<option> No Pages to select from</option>';
                    } ?>
                </select>
            </label>
            <?php
            if(!empty($capture_update)){
                echo '<small> Selected page: <a href="'.get_edit_post_link($capture_update).'">'. get_post_field( 'post_name', $capture_update ) .' </a>';
            }
            ?>
        </fieldset>

        <fieldset>
            <p><?php esc_attr_e('Please enter the Visitor Capture Form Name', 'plugin_name'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Please enter the Visitor Capture Form Name', 'plugin_name'); ?></span>
            </legend>
            <input type="text" class="update_visitor_form_name" id="<?php echo $this->plugin_name; ?>-update_visitor_form_name" name="<?php echo $this->plugin_name; ?>[update_visitor_form_name]" value="<?php if (!empty($update_visitor_form_name)) echo $update_visitor_form_name;
                                                                                                                                                                            else echo ''; ?>" />
        </fieldset>

        <?php submit_button(__('Save all changes', 'plugin_name'), 'primary', 'submit', TRUE); ?>
    </form>
</div>