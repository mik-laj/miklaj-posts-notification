<?php
wp_register_style( 'select2css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css', false, '1.0', 'all' );
wp_register_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js', array( 'jquery' ), '1.0', true );
wp_enqueue_style( 'select2css' );
wp_enqueue_script( 'select2' );

wp_register_style( 'miklaj_notification_css', plugins_url('assets/css/style.css', M_N_FILE), false, '1.0' );
wp_enqueue_style( 'miklaj_notification_css');
?>
<form id="miklaj_notification_form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
    <div class="field-group">
        <div class="field-group-label">
            <label for="notification_form_name" class="right inline">
                <?php _e('Name and surname', 'miklaj-notification'); ?>:
            </label>
        </div>
        <div class="field-group-input">
            <input type="text" id="notification_form_name" name="name" placeholder="<?php _e('John Smith', 'miklaj-notification'); ?>" required>
        </div>
    </div>
    <div class="field-group">
        <div class="field-group-label">
            <label for="notification_form_email" class="right inline"><?php _e('E-mail', 'miklaj-notification'); ?>:</label>
        </div>
        <div class="field-group-input">
            <input type="text" id="notification_form_email" name="email" type="email" placeholder="<?php _e('email@example.com', 'miklaj-notification'); ?>" required>
        </div>
    </div>
    <div class="field-group">
        <div class="field-group-label">
            <label class="right inline"><?php _e('Categories', 'miklaj-notification'); ?>:</label>
        </div>
        <div class="field-group-input">
            <?php
            $categories = get_categories();
            foreach ($categories as $category):
                ?>
            <div class="field-group-label-cat">
                <input value="<?php echo $category->term_id; ?>" type="checkbox" name="taxonomy[category][]" id="notification_form_category_<?php echo $category->term_id; ?>">
                <label for="notification_form_category_<?php echo $category->term_id; ?>">
                    <?php echo $category->name; ?>
                </label>
            </div>
            <?php
            endforeach;
            ?>
        </div>
    </div>
    <div class="field-group">
        <div class="field-group-label">
            <?php _e('Tags', 'miklaj-notification'); ?>:
        </div>
        <div class="field-group-input">
            <select name="taxomomy[post_tag][]" id="notification_form_tags" multiple="multiple" style="width: 100%;">
                <?php
                $tags = get_tags();
                $index = 0;
                foreach ($tags as $tag):
                    ?>
                <option value="<?php echo $tag->term_id; ?>"><?php echo $tag->name; ?></option>
                <?php
                endforeach;
                ?>
            </select>
        </div>
        <script type="text/javascript">
            jQuery(function(){
                jQuery('#notification_form_tags').select2({
                    tokenSeparators: [',', ' '],
                    theme: 'classic'
                });
            });
        </script>
        <style type="text/css">
            .select2-search-field:before{
                content: initial !important;
            }
            .select2-container{
                border-radius: 0;
                background-color: #FFFFFF;
                border-style: solid;
                border-width: 1px;
                border-color: #cccccc;
                box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
                color: rgba(0, 0, 0, 0.75);
                display: block;
                font-family: inherit;
                font-size: 0.875rem;
                height: 2.3125rem;
                margin: 0 0 1rem 0;
                padding: 0.5rem;
                width: 100%;
            }
            .select2-container-multi .select2-choices .select2-search-choice{
                margin-top: 0 !important;
            }
        </style>
    </div>
    <div class="field-group">
        <div class="field-group-label">
            <label for="right-label" class="right inline">
                <?php _e('Notification frequency', 'miklaj-notification'); ?>:
            </label>
        </div>
        <div class="field-group-input">
            <?php
            foreach ($GLOBALS['mik_laj_notification']->frequencies as $id => $options):
                ?>
            <input type="radio" id="notification_form_frequency_<?php echo $id; ?>" name="frequency" value="<?php echo $id; ?>" required="">
            <label for="notification_form_frequency_<?php echo $id; ?>">
                <?php echo $options['label']; ?>
            </label>
            <?php
            endforeach;
            ?>
        </div>
    </div>
    <div class="field-group">
        <div class="field-group-input">
            <input type="checkbox" name="personal_data" id="notification_form_personal_data" nane="personal_data" required>
            <label for="notification_form_personal_data">
                <?php
                _e('I agree to the processing of personal data by you', 'miklaj-notification');
                ?>
            </label>
        </div>
    </div>
    <div class="field-group">
        <div class="field-group-input">
            <button type="submit"><?php echo _e('Save', 'miklaj-notification'); ?></button>
        </div>
    </div>
    <input type="hidden" name="action" value="miklaj_notification_save_form">
</form>
