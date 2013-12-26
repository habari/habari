<?php 
if( !defined( 'HABARI_PATH' ) ) { 
	die('No direct access');
}

class Hear extends Theme
{
	public function action_theme_activated() {}

	/**
	 * Execute on theme init to apply these filters to output
	 */
	public function action_init_theme()	{
		Format::apply( 'autop', 'comment_content_out' );
		Format::apply( 'tag_and_list', 'post_tags_out' );
		Format::apply( 'autop', 'post_content_out' );
		Format::apply_with_hook_params( 'more', 'post_content_excerpt', '', 55, 0 );
	}

	public function act_display_home( $user_filters = array() ) {
/* 		$this->featured = Posts::get( array('info' => array('place' => 'home'), 'limit' => 3) ); */
		$this->featured = Posts::get( array('limit' => 3) );
		
		$this->display('home');
	}

	/**
	 * limit function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $string
	 * @param mixed $limit
	 * @param string $break. (default: ".")
	 * @param string $pad. (default: "...")
	 * @return void
	 */
	public function limit_words($string, $limit, $break = ";", $pad = "...") {
		$string = strip_tags($string);

		if( strlen($string) <= $limit ) {
			return $string;
		}
		
		if( false !== ($breakpoint = strpos($string, $break, $limit)) ) {
    		if( $breakpoint < strlen($string) - 1 ) {
				$string = substr($string, 0, $breakpoint) . $pad;
    		}
  		}
  		
		return $string;
	}
}
?>