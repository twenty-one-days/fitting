<?php  if ( ! defined('AVIA_FW')) exit('No direct script access allowed');

 
if( ! class_exists( 'av_google_maps' ) )
{
	add_action('wp_footer', 'av_google_maps::gmap_js_globals', 10);
	add_action('admin_footer', 'av_google_maps::gmap_js_globals', 10);
			
	class av_google_maps
	{
			//	maintain URL and version number here for all objects using this element
		//const API_URL		=	'https://maps.googleapis.com/maps/api/js';
		
		const API_URL		=	'https://maps.googleapis.com/maps/api/js';
		const API_VERSION	=	'3.30';					
		
		public function __construct($key)
		{
			$this->key = $key;
		}
		
		/**
		 * Returns the script source of GoogleMapsApi with the correct version 
		 * Use this function to allow easy maintanance of URL and version number
		 * User may filter the parameters - a fallback to the set values if user removes needed values by default settings
		 * 
		 * @param string $api_key			API key generated by Google
		 * @param string $callback			callback function when API is loaded
		 * @return string
		 */
		static public function api_url( $api_key = '', $callback = '' )
		{
			$args = array();
			$api_src = array(
							'source'	=>	av_google_maps::API_URL,
							'version'	=>	av_google_maps::API_VERSION
						);
			
			$api_src = apply_filters( 'avf_google_maps_source', $api_src );
			
			$api_url = ! empty( $api_src['source'] ) ? $api_src['source'] : av_google_maps::API_URL;
			$args['v'] = ! empty( $api_src['version'] ) ? $api_src['version'] : av_google_maps::API_VERSION;
			
			if( $api_key != '' )
			{
				$args['key'] = $api_key;
			}
			
			if( $callback != '' )
			{
				$args['callback'] = $callback;
			}
			
			if( ! empty( $args ) )
			{
				$api_url = add_query_arg( $args, $api_url );
			}
				
			return $api_url;
		}
		
		/**
		 * Output global variables needed by elements to access google maps API
		 */
		static public function gmap_js_globals()
		{
			$api_key = avia_get_option('gmap_api');
		
			$api_source = av_google_maps::api_url( $api_key );
			$api_builder = av_google_maps::api_url( $api_key, 'av_builder_maps_loaded' );
			$api_builder_backend = av_google_maps::api_url( '', 'av_backend_maps_loaded' );
			$api_maps_loaded = av_google_maps::api_url( $api_key, 'aviaOnGoogleMapsLoaded' );
			
			
			if( ! empty( $api_key ) )
			{
				echo "
<script type='text/javascript'>
 /* <![CDATA[ */  
var avia_framework_globals = avia_framework_globals || {};
	avia_framework_globals.gmap_api = '".$api_key."';
	avia_framework_globals.gmap_maps_loaded = '".$api_maps_loaded."';
	avia_framework_globals.gmap_builder_maps_loaded = '".$api_builder."';
	avia_framework_globals.gmap_backend_maps_loaded = '".$api_builder_backend."';
	avia_framework_globals.gmap_source = '".$api_source."';
/* ]]> */ 
</script>	
";
			}
		}
				
		
		function check_api_key()
		{
			$valid = false;
			//function that checks if the value of $this->key is a valid api key
		
		
			return $valid;
		}
		
		function store_key()
		{
			update_option('av_gmaps_api_key', $this->key);
		}
		
		function delete_key()
		{
			delete_option('av_gmaps_api_key');
		}
		
		
		static function backend_html($value = "", $ajax = true, $valid_key = false)
		{
			$valid_key  = $valid_key == "true" ? true : false;
			$gmaps 		= false;
			$response_text  = __("Could not connect to Google Maps with this API Key.",'avia_framework');
			$response_class = "av-notice-error";
			$content_default  =			'<h4>' . esc_html__( 'Troubleshooting:', 'avia_framework' ) . '</h4>';
			$content_default .=			'<ol>';
			$content_default .=				'<li>';
			$content_default .=					esc_html__( 'Check if you typed the key correctly.', 'avia_framework' );
			$content_default .=				'</li>';
			$content_default .=				'<li>';
			$content_default .=					esc_html__( 'If you use the restriction setting on Google try to remove that, wait a few minutes for google to apply your changes and then check again if the key works here. If it does, you probably have a syntax error in your referrer url', 'avia_framework' );
			$content_default .=				'</li>';
			$content_default .=				'<li>';
			$content_default .=					esc_html__( 'If none of this helps: deactivate all plugins and then check if the API works by using the button above. If thats the case then one of your plugins is interfering. ', 'avia_framework' );
			$content_default .=				'</li>';
			$content_default .=			'</ol>';
			
			
		
			//if called by user pressing the ajax check button
			if($ajax)
			{	
				$api = new av_google_maps($value);
				
				if($valid_key)
				{	
					$api->store_key();
					
					$response_class = "";
					$response_text  = __("We were able to properly connect to google maps with your API key",'avia_framework');
					
					
					//will be stripped from the final output but tells the ajax script to save the page after the check was performed
					$response_text .= " avia_trigger_save"; 				
				}
				else
				{
					$api->delete_key();
				}
			}
			else // is called on a normal page load. in this case we either show the stored result or if we got no stored result we show nothing
			{
				$valid_key = get_option('av_gmaps_api_key');
				
				if($valid_key)
				{
					$response_class = "";
					$response_text  = __("Last time we checked we were able to connected to google maps with your API key",'avia_framework');
				}
			}
			
			
			if($valid_key)
			{
				$content_default  = __("If you ever change your API key or the URL restrictions of the key please verify the key here again, to test if it works properly",'avia_framework');
			}
			
			

			$output  = "<div class='av-verification-response-wrapper'>";
			$output .= "<div class='av-text-notice {$response_class}'>";
			$output .= $response_text;
			$output .= "</div>";
			$output .= "<div class='av-verification-cell'>".$content_default."</div>";
			$output .= "</div>";
			
			
			return $output;
		}
		
	}
}

if (!function_exists('av_maps_api_check')){
	
	function av_maps_api_check($value, $ajax = true, $js_value = NULL)
	{
		return av_google_maps::backend_html($value, $ajax, $js_value);
	}

}