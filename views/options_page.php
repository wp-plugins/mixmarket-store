<div class="wrap">
<h2><?php _e( 'Options', MM_TEXTDOMAIN ); ?></h2>

<?php
//checking upload directory
$uploads = wp_upload_dir();
if ( !file_exists( $uploads[ 'basedir' ] ) || !is_writable( $uploads[ 'basedir' ] ) || !is_dir( $uploads[ 'basedir' ] ) ) {
?>
<div class="error">
    <p><strong><?php _e( 'File upload folder (wp-content/uploads) not found. Please, create it and make writable (chmod 777).', MM_TEXTDOMAIN ); ?></strong></p>
</div>
<?php
}
?>

<?php if ( isset( $_GET[ 'updated' ] ) && 'true' == $_GET[ 'updated' ] ) { ?>
<div class="updated settings-error" id="setting-error-settings_updated">
    <p><strong><?php _e( 'Settings updated', MM_TEXTDOMAIN ); ?></strong></p>
</div>
<?php } ?>

<div>Для настройки данного плагина необходимо зарегистрироваться в рекламной сети <a href="http://www.mixmarket.biz/doc/partners/goods/programs/?from=wordpress">"Партнерская сеть Миксмаркет"</a> и получить идентификатор рекламного блока "Где купить?" в рамках сервиса товарной рекламы <a href="http://www.mixmarket.biz/doc/partners/goods/programs/?from=wordpress">Микс-Товары</a>. <br /> <a href=
"http://blog.mixmarket.biz/wordpress-plugin-mixmarket/?from=wordpress">Подробная инструкция по настройке данного плагина >></a>.</div>

<form method="post" action="options.php">
    <?php settings_fields( 'mm-options' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Products per page', MM_TEXTDOMAIN ); ?></th>
        <td>
            <select name="mm_products_per_page">
        <?php
            $ipp = get_option( 'mm_products_per_page' );
            foreach ( array( 10, 20, 50) as $value ) {
                $selected = '';
                if ( (int)$ipp == $value ) {
                    $selected = ' selected="selected"';
                }
                echo '<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
            }
        ?>
            </select>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Products on category home page', MM_TEXTDOMAIN ); ?></th>
        <td>
            <select name="mm_products_on_category_home">
        <?php
            $ipp = get_option( 'mm_products_on_category_home' );
            foreach (array( 10, 20 ) as $value ) {
                $selected = '';
                if ( (int)$ipp == $value ) {
                    $selected = ' selected="selected"';
                }
                echo '<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
            }
        ?>
            </select>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Catalog page', MM_TEXTDOMAIN ); ?></th>
        <td>
            <?php
            $pages = get_pages();
            if ( count($pages) > 0 ) {
                echo '<select name="mm_catalog_page">';
                foreach ( $pages as $page ) {
                    $cur_page = get_option( 'mm_catalog_page' );
                    $selected = '';
                    if ( (int)$cur_page == $page->ID ) {
                        $selected = ' selected="selected"';
                    }
                    echo '<option value="'.$page->ID.'"'.$selected.'>'.$page->post_title.'</option>';
                }
                echo '</select>';
            }
            else {
                echo __( 'No Pages Found', MM_TEXTDOMAIN );
            }
            ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Blocks order', MM_TEXTDOMAIN ); ?></th>
        <td>
            <ul class="blocks_order">
            <?php
//            $blocks_ids = array( 0, 1, 2 );
            $blocks_ids = array( 0, 1 );
            if ( false !== ( $blocks_order = get_option( 'mm_blocks_order' ) ) ) {
                $blocks_ids_temp = explode( '|', $blocks_order );
                if ( count($blocks_ids_temp) == 2 ) {
                    $blocks_ids = $blocks_ids_temp;
                }
            }
            foreach ( $blocks_ids as $block_id ) {
                echo '<li id="b_' . $block_id . '">' . $blocks[ $block_id ][ 'title' ].'</li>';
            }
            ?>
            </ul>
            <input type="hidden" name="mm_blocks_order" value="<?php echo get_option( 'mm_blocks_order' ); ?>" />
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Partner Id', MM_TEXTDOMAIN ); ?></th>
        <td><input type="text" name="mm_partner_id" value="<?php echo get_option( 'mm_partner_id' ); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Use standard CSS', MM_TEXTDOMAIN ); ?></th>
        <?php
        $checked = ( 'on' == get_option( 'mm_use_standard_css' ) ) ? 'checked="checked"' : '';
        ?>
        <td><input type="checkbox" name="mm_use_standard_css" <?php echo $checked; ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Image size for item card', MM_TEXTDOMAIN ); ?></th>
        <td>
			<?php _e( 'Width :', MM_TEXTDOMAIN ); ?> <input type="text" name="mm_image_width" value="<?php echo get_option( 'mm_image_width' ); ?>" />
			<?php _e( 'Height :', MM_TEXTDOMAIN ); ?> <input type="text" name="mm_image_height" value="<?php echo get_option( 'mm_image_height' ); ?>" />
		</td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Image size for widget', MM_TEXTDOMAIN ); ?></th>
        <td>
			<?php _e( 'Width :', MM_TEXTDOMAIN ); ?> <input type="text" name="mm_image_thumb_width" value="<?php echo get_option( 'mm_image_thumb_width' ); ?>" />
			<?php _e( 'Height :', MM_TEXTDOMAIN ); ?> <input type="text" name="mm_image_thumb_height" value="<?php echo get_option( 'mm_image_thumb_height' ); ?>" />
		</td>
        </tr>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" />
    </p>
</form>

<h2><?php _e( 'Import', MM_TEXTDOMAIN ); ?> / <?php _e( 'Export', MM_TEXTDOMAIN ); ?></h2>

<form method="post" action="<?php echo MM_URL . '/libs/create_dump.php'; ?>" id="mm_import">
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e( 'Export', MM_TEXTDOMAIN ); ?>" />
    </p>
</form>

<form method="post" enctype="multipart/form-data" action="<?php echo MM_URL . '/libs/restore_dump.php'; ?>" id="mm_export">
    <?php wp_nonce_field( 'restore_dump', 'mm_nonce' ); ?>

    <?php
    if ( isset( $_GET[ 'mm_restore_mes' ] ) ) {
        $restore_mes = ( 'ok' == $_GET[ 'mm_restore_mes' ] ) ? __( 'The data was successfully imported', MM_TEXTDOMAIN ) : __( 'Data import error', MM_TEXTDOMAIN );
    ?>
    <div class="updated settings-error" id="setting-error-settings_updated">
        <p><strong><?php echo $restore_mes; ?></strong></p>
    </div>
    <?php } ?>

    <label><?php _e( 'Dump file', MM_TEXTDOMAIN ); ?> <input type="file" name="mm_db_dump" size="40" /></label>
    <p class="submit">
    <input type="submit" class="button-primary" id="import_data" value="<?php _e( 'Import', MM_TEXTDOMAIN ); ?>" />
    </p>
</form>

<form method="post" action="<?php echo MM_URL . '/libs/remove_plugin.php'; ?>" id="mm_remove_data">
    <?php wp_nonce_field( 'remove_plugin', 'mm_remove_nonce' ); ?>
    <p class="submit">
    <input type="submit" class="button-primary" id="remove_data" value="<?php _e( 'Remove plugin data', MM_TEXTDOMAIN ); ?>" />
    </p>
</form>

</div> <!-- div.wrap -->