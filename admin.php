<?php

if ( isset( $_POST['_wpnonce'] ) && check_admin_referer( 'TIMID_admin_nonce' ) && current_user_can( 'manage_options' ) ):
  $head_extras = isset( $_POST[ 'timidpress_add_head' ] ) ? $_POST[ 'timidpress_add_head' ] : '';
  if ( empty( $head_extras ) ):
    delete_option( 'TIMIDPRESS_extra_head' );
  else:
    update_option( 'TIMIDPRESS_extra_head', htmlentities( html_entity_decode( $head_extras ) ) );
  endif;
  $delimiter = isset( $_POST[ 'timidpress_delimiter' ] ) ? $_POST[ 'timidpress_delimiter' ] : '';
  if ( empty( $delimiter ) ):
    delete_option( 'TIMIDPRESS_delimiter' );
  else:
    update_option( 'TIMIDPRESS_delimiter', htmlentities( html_entity_decode( $delimiter ) ) );
  endif;
endif;

$timidpress_setting_delimiter = get_option( 'TIMIDPRESS_delimiter' );
$timidpress_setting_head = get_option( 'TIMIDPRESS_extra_head' );

?>
<style type="text/css">
#timidpress_add_head {
  min-height: 340px;
  width: 100%;
}
</style>
<div class="wrap">
    <h1>Timid Press</h1>
	<?php settings_errors( 'Timid-notices' ); ?>

    <form method="post" action="" id="TIMID_form">
		<?php wp_nonce_field( 'TIMID_admin_nonce' ); ?>

    <p><label for="timidpress_delimiter">Delimiter</label><br>
      <input type="text" name="timidpress_delimiter" id="timidpress_delimiter" value="<?php if ( ! empty( $timidpress_setting_delimiter ) ): echo stripslashes( $timidpress_setting_delimiter ); endif; ?>"></p>
    <p><label for="timidpress_add_head">Add custom wp_head<br><small>Ex: Google Analytics, ...</small></label><br>
        <textarea name="timidpress_add_head" id="timidpress_add_head"><?php if ( !empty( $timidpress_setting_head ) ): echo stripslashes( $timidpress_setting_head ); endif; ?></textarea></p>
		<p><?php submit_button(); ?></p>
    </form>
</div>
