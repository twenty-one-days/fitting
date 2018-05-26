<?php

######################################################################
# social icon builder
######################################################################

if(!function_exists('avia_social_media_icons'))
{
	function avia_social_media_icons($args = array(), $echo = true, $post_data = array())
	{
		$icons = new avia_social_media_icons($args, $post_data);
		if($echo) 
		{	
			echo $icons->html();
		}
		else
		{
			return $icons->html();
		}
	}
}



if(!class_exists('avia_social_media_icons'))
{
	class avia_social_media_icons
	{
		var $args;
		var $post_data;
		var $icons = array();
		var $html  = "";
		var $counter = 1;

		/*
		 * constructor
		 * initialize the variables necessary for all social media links
		 */

		function __construct($args = array(), $post_data = array())
		{
			$default_arguments = array('outside'=>'ul', 'inside'=>'li', 'class' => 'social_bookmarks', 'append' => '');
			$this->args = array_merge($default_arguments, $args);

			$this->post_data = $post_data;

			$this->icons = apply_filters( 'avia_filter_social_icons', avia_get_option('social_icons') );
		}

		/*
		 * function build_icon
		 * builds the html string for a single item, with a few options for special items like rss feeds
		 */

		function build_icon($icon)
		{
			global $avia_config;

			//special cases
			switch($icon['social_icon'])
			{
				case 'rss':  if(empty($icon['social_icon_link'])) $icon['social_icon_link'] = get_bloginfo('rss2_url'); break;
				case 'twitter':
				case 'dribbble':
				case 'vimeo':
				case 'behance':

				if(strpos($icon['social_icon_link'], 'http') === false && !empty($icon['social_icon_link']))
				{
					$icon['social_icon_link'] = "http://".$icon['social_icon'].".com/".$icon['social_icon_link']."/";
				}
				break;
			}

			if(empty($icon['social_icon_link'])) $icon['social_icon_link'] = "#";
			$blank = "target='_blank'";
			
			//dont add target blank to relative urls or urls to the same dmoain
			if(strpos($icon['social_icon_link'], 'http') === false || strpos($icon['social_icon_link'], home_url()) === 0) $blank = "";
			
			$html  = "";
			$html .= "<".$this->args['inside']." class='".$this->args['class']."_".$icon['social_icon']." av-social-link-".$icon['social_icon']." social_icon_".$this->counter."'>";
			$iconstr = av_icon_string($icon['social_icon']);
			$namestr = ucfirst($icon['social_icon']);
			if($icon['social_icon'] == 'qqtalk'){
				$iconstr = 'aria-hidden="true" data-av_icon="&#xe600;" data-av_iconfont="iconfont"';
				$namestr = 'QQ客服';
			}
			if($icon['social_icon'] == 'qqzone'){
				$iconstr = 'aria-hidden="true" data-av_icon="&#xe602;" data-av_iconfont="iconfont"';
				$namestr = 'QQ空间';
			}
			if($icon['social_icon'] == 'sina'){
				$iconstr = 'aria-hidden="true" data-av_icon="&#xe605;" data-av_iconfont="iconfont"';
				$namestr = '新浪微博';
			}
			if($icon['social_icon'] == 'qqweibo'){
				$iconstr = 'aria-hidden="true" data-av_icon="&#xe609;" data-av_iconfont="iconfont"';
				$namestr = '腾讯微博';
			}
			if($icon['social_icon'] == 'renren'){
				$iconstr = 'aria-hidden="true" data-av_icon="&#xe604;" data-av_iconfont="iconfont"';
				$namestr = '人人网';
			}
			if($icon['social_icon'] == 'baidu'){
				$iconstr = 'aria-hidden="true" data-av_icon="&#xe60a;" data-av_iconfont="iconfont"';
				$namestr = '百度贴吧';
			}
			if($icon['social_icon'] == 'qqfriend'){
				$iconstr = 'aria-hidden="true" data-av_icon="&#xe606;" data-av_iconfont="iconfont"';
				$namestr = '腾讯朋友';
			}
			if($icon['social_icon'] == 'wechat'){
				$iconstr = 'aria-hidden="true" data-av_icon="&#xe60e;" data-av_iconfont="iconfont"';
				$namestr = '朋友圈';
			}
			$html .= "<a {$blank} href='".$icon['social_icon_link']."' ".$iconstr." title='".$namestr."'><span class='avia_hidden_link_text'>".$namestr."</span></a>";
			$html .= "</".$this->args['inside'].">";

			return $html;
		}

		/*
		 * function html
		 * builds the html, based on the available icons
		 */

		function html()
		{
			if(!empty($this->icons))
			{
				$this->html = "<".$this->args['outside']." class='noLightbox ".$this->args['class']." icon_count_".count($this->icons)."'>";

				foreach ($this->icons as $icon)
				{
					if(!empty($icon['social_icon']))
					{
						$this->html .= $this->build_icon($icon);
						$this->counter ++;
					}
				}

				$this->html .= $this->args['append'];
				$this->html .= "</".$this->args['outside'].">";
			}


			return $this->html;
		}
	}
}




######################################################################
# share link builder
######################################################################


if(!class_exists('avia_social_share_links'))
{
	class avia_social_share_links
	{
		var $args;
		var $options;
		var $links = array();
		var $html  = "";
		var $title = "";
		var $counter = 0;
		
		/*
		 * constructor
		 * initialize the variables necessary for all social media links
		 */

		function __construct($args = array(), $options = false, $title = false)
		{
			$default_arguments = array
			(
				'QQ空间' 	=> array("encode"=>true, "encode_urls"=>true, "pattern" => "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=[permalink]&amp;title=[title]&amp;site=&amp;pics=[thumbnail]"),
				'新浪微博' 	=> array("encode"=>true, "encode_urls"=>true, "pattern" => "http://service.weibo.com/share/share.php?url=[permalink]&amp;appkey=&amp;title=[title]&amp;pic=[thumbnail]&amp;ralateUid=&amp;searchPic=false#_loginLayer_1458202819611"),
				'人人网' 	=> array("encode"=>true, "encode_urls"=>true, "pattern" => "http://widget.renren.com/dialog/share?resourceUrl=[permalink]&amp;srcUrl=[permalink]&amp;title=[title]&amp;description=" , 'label' => __("分享到人人网",'avia_framework')),
				'腾讯微博' => array("encode"=>true, "encode_urls"=>true, "pattern" => "http://share.v.t.qq.com/index.php?c=share&amp;a=index&amp;url=[permalink]&amp;title=[title]&amp;appkey=801cf76d3cfc44ada52ec13114e84a96"),
				'百度贴吧' 	=> array("encode"=>true, "encode_urls"=>true, "pattern" => "http://tieba.baidu.com/f/commit/share/openShareApi?url=[permalink]&amp;title=[title]&amp;desc=&amp;comment="),
				'QQ好友' 	=> array("encode"=>true, "encode_urls"=>true, "pattern" => "http://connect.qq.com/widget/shareqq/index.html?url=[permalink]&amp;title=[title]&amp;desc=&summary=&amp;site=baidu"),
				'腾讯朋友' 	=> array("encode"=>true, "encode_urls"=>true, "pattern" => "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?to=pengyou&url=[permalink]&amp;title=[title]&amp;desc=&amp;summary="),
				'朋友圈' 	=> array("encode"=>true, "encode_urls"=>true, "pattern" => "https://pan.baidu.com/share/qrcode?w=150&h=150&url=[permalink]"),
				'facebook' 	=> array("encode"=>true, "encode_urls"=>false, "pattern" => "http://www.facebook.com/sharer.php?u=[permalink]&amp;t=[title]"),
				'twitter' 	=> array("encode"=>true, "encode_urls"=>false, "pattern" => "https://twitter.com/share?text=[title]&url=[shortlink]"),
				'gplus' 	=> array("encode"=>true, "encode_urls"=>false, "pattern" => "https://plus.google.com/share?url=[permalink]" , 'label' => __("Share on Google+",'avia_framework')),
				'pinterest' => array("encode"=>true, "encode_urls"=>true, "pattern" => "http://pinterest.com/pin/create/button/?url=[permalink]&amp;description=[title]&amp;media=[thumbnail]"),
				'linkedin' 	=> array("encode"=>true, "encode_urls"=>false, "pattern" => "http://linkedin.com/shareArticle?mini=true&amp;title=[title]&amp;url=[permalink]"),
				'tumblr' 	=> array("encode"=>true, "encode_urls"=>true, "pattern" => "http://www.tumblr.com/share/link?url=[permalink]&amp;name=[title]&amp;description=[excerpt]"),
				'vk' 		=> array("encode"=>true, "encode_urls"=>false, "pattern" => "http://vk.com/share.php?url=[permalink]"),
				'reddit' 	=> array("encode"=>true, "encode_urls"=>false, "pattern" => "http://reddit.com/submit?url=[permalink]&amp;title=[title]"),
				'mail' 		=> array("encode"=>true, "encode_urls"=>false, "pattern" => "mailto:?subject=[title]&amp;body=[permalink]", 'label' => __("Share by Mail",'avia_framework') ),
			);
			
			$this->args = apply_filters( 'avia_social_share_link_arguments', array_merge($default_arguments, $args) );
			
			if(empty($options)) $options = avia_get_option();
			$this->options 	= $options;
			$this->title 	= $title !== false? $title : __("Share this entry",'avia_framework');
			$this->build_share_links();
		}
		
		/*
		 * filter social icons that are disabled in the backend. everything that is left will be displayed.
		 * that way the user can hook into the "avia_social_share_link_arguments" filter above and add new social icons without the need to add a new backend option
		 */
		function build_share_links()
		{
			$thumb 					= wp_get_attachment_image_src( get_post_thumbnail_id(), 'masonry' );
			$replace['permalink'] 	= !isset($this->post_data['permalink']) ? get_permalink() : $this->post_data['permalink'];
			$replace['title'] 		= !isset($this->post_data['title']) ? get_the_title() : $this->post_data['title'];
			$replace['excerpt'] 	= !isset($this->post_data['excerpt']) ? get_the_excerpt() : $this->post_data['excerpt'];
			$replace['shortlink']	= !isset($this->post_data['shortlink']) ? wp_get_shortlink() : $this->post_data['shortlink'];
			$replace['thumbnail']	= is_array($thumb) && isset($thumb[0]) ? $thumb[0] : "";
			$replace['thumbnail']	= !isset($this->post_data['thumbnail']) ? $replace['thumbnail'] : $this->post_data['thumbnail'];
			
			$replace = apply_filters('avia_social_share_link_replace_values', $replace);
			$charset = get_bloginfo('charset');
			
			foreach($this->args as $key => $share)
			{
				if ($key == 'QQ空间') {
					$key = 'qqzone';
				}
				if ($key == '新浪微博') {
					$key = 'sina';
				}
				if ($key == '人人网') {
					$key = 'renren';
				}
				if ($key == '腾讯微博') {
					$key = 'qqweibo';
				}
				if ($key == '百度贴吧') {
					$key = 'baidu';
				}
				if ($key == 'QQ好友') {
					$key = 'qq';
				}
				if ($key == '腾讯朋友') {
					$key = 'qqfriend';
				}
				if ($key == '朋友圈') {
					$key = 'wechat';
				}
				$share_key  = 'share_'.$key;
				$url 		= $share['pattern'];
				
				//if the backend option is disabled skip to the next link. in any other case generate the share link
				if(isset($this->options[$share_key]) && $this->options[$share_key] == 'disabled' ) continue;
				
				foreach($replace as $replace_key => $replace_value)
				{
					if(!empty($share['encode']) && $replace_key != 'shortlink' && $replace_key != 'permalink') $replace_value = rawurlencode(html_entity_decode($replace_value, ENT_QUOTES, $charset));
					if(!empty($share['encode_urls']) && ($replace_key == 'shortlink' || $replace_key == 'permalink')) $replace_value = rawurlencode(html_entity_decode($replace_value, ENT_QUOTES, $charset));
					
					$url = str_replace("[{$replace_key}]", $replace_value, $url);
				}
				
				$this->args[$key]['url'] = $url;
				$this->counter ++;
			}
		}
		
		
		
		/*
		 * function html
		 * builds the html, based on the available urls
		 */

		function html()
		{
			global $avia_config;
			
			if($this->counter == 0) return;
			
			$this->html .= "<div class='av-share-box'>";
			if($this->title)
			{
				$this->html .= 		"<h5 class='av-share-link-description'>";
				$this->html .= 		apply_filters('avia_social_share_title', $this->title , $this->args);
				$this->html .= 		"</h5>";
			}
			$this->html .= 		"<ul class='av-share-box-list noLightbox'>";
			
			foreach($this->args as $key => $share)
			{
				if(empty($share['url'])) continue;
			
				$icon = isset($share['icon']) ? $share['icon'] : $key;
				$name = isset($share['label'])? $share['label']: __("Share on",'avia_framework'). " " .ucfirst($key);
				
				$blank = strpos($share['url'], 'mailto') !== false ? "" : "target='_blank'";
				
				$iconstr = av_icon_string($icon);
				
				if ($key == 'qqzone') {
					$name = '分享到QQ空间';
					$iconstr = 'aria-hidden="true" data-av_icon="&#xe602;" data-av_iconfont="iconfont"';
				}
				if ($key == 'sina') {
					$name = '分享到新浪微博';
					$iconstr = 'aria-hidden="true" data-av_icon="&#xe605;" data-av_iconfont="iconfont"';
				}
				if ($key == 'renren') {
					$name = '分享到人人网';
					$iconstr = 'aria-hidden="true" data-av_icon="&#xe604;" data-av_iconfont="iconfont"';
				}
				if ($key == 'qqweibo') {
					$name = '分享到腾讯微博';
					$iconstr = 'aria-hidden="true" data-av_icon="&#xe609;" data-av_iconfont="iconfont"';
				}
				if ($key == 'baidu') {
					$name = '分享到百度贴吧';
					$iconstr = 'aria-hidden="true" data-av_icon="&#xe60a;" data-av_iconfont="iconfont"';
				}
				if ($key == 'qq') {
					$name = '分享到QQ好友';
					$iconstr = 'aria-hidden="true" data-av_icon="&#xe600;" data-av_iconfont="iconfont"';
				}
				if ($key == 'qqfriend') {
					$name = '分享到腾讯朋友';
					$iconstr = 'aria-hidden="true" data-av_icon="&#xe606;" data-av_iconfont="iconfont"';
				}
				if ($key == 'wechat') {
					$name = '<img src="'.$share['url'].'"><br/>分享到朋友圈';
					$iconstr = 'aria-hidden="true" data-av_icon="&#xe60e;" data-av_iconfont="iconfont"';
				}
				$this->html .= "<li class='av-share-link av-social-link-{$key}' >";
				$this->html .= 		"<a {$blank} href='".$share['url']."' ".$iconstr." title='' data-avia-related-tooltip='{$name}'><span class='avia_hidden_link_text'>{$name}</span></a>";
				$this->html .= "</li>";
			}
			
			$this->html .= 		"</ul>";
			$this->html .= "</div>";
			
			return $this->html;
		}
		
		
		
		
	}
}





if(!function_exists('avia_social_share_links'))
{
	function avia_social_share_links($args = array(), $options = false, $title = false, $echo = true)
	{
		$icons = new avia_social_share_links($args, $options, $title);
		
		if($echo) 
		{	
			echo $icons->html();
		}
		else
		{
			return $icons->html();
		}
	}
}


