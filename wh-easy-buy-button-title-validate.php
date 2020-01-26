<?php
// this checks the title on post_save and if Duplicate deletes the coupon.
function wh_ebb_duplicate_title_check( $post_id, $post )
{
	
	if($post->post_type !== 'wh_ebb_coupons' || !isset($_POST['post_title'])):
		return;
	endif;

	global $wpdb ;
	
	$title = $_POST['post_title'];
 
	$wtitles = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'wh_ebb_coupons' 
						AND post_title = '{$title}' AND ID != {$post_id} " ;
 
	$wresults = $wpdb->get_results( $wtitles ) ;
 
	if ( $wresults )
	{
		$wpdb->delete( $wpdb->posts,array( 'ID' => $post_id ) ) ;
        $arr_params = array( 'message' => '10', 'wh_ebb_coupon_error' => '1' )  ;      
		$location = add_query_arg( $arr_params , admin_url('post-new.php?post_type=wh_ebb_coupons') ) ;
		wp_redirect( $location  ) ;
        exit ; 
	}
}
 
add_action( 'save_post', 'wh_ebb_duplicate_title_check', 10, 2 ) ;
 
/// handel error for back end 
function not_published_error_notice() {
    if(isset($_GET['wh_ebb_coupon_error']) == 1 ){
		?>
		<div class="notice notice-warning">
		<p style='color:red' ><?php _e('This Coupon ID is already being used. Please create a new coupon with a unique name.' , 'dublicate-title-validate') ?></p>
		</div>
		<?php
    }
}
add_action( 'admin_notices', 'not_published_error_notice' );        
 