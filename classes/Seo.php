<?php namespace Inerba\SocialSeo\Classes;

use Request;
use Inerba\SocialSeo\Models\Settings;

class Seo
{
	public static function title($title, $prefix_suffix = true)
	{
		$settings = Settings::instance();

		if($settings->enable_title && $prefix_suffix)
        {
            $position = $settings->title_position;
            $site_title = $settings->title;

            if($position == 'prefix')
            {
                $new_title = $site_title . " " . $title;
            }
            else
            {
                $new_title = $title . " " . $site_title;
            }
        }
        else
        {
        	$new_title = $title;
        }

		$output = self::render($new_title);
		return "<title>{$output}</title>";
	}

	public static function description($string)
	{
    try {
		  return self::meta('name', 'description', $string, Settings::get('seo_meta_description_maxlength'));
    } catch (\Exception $e) {}
	}

	public static function meta($type='property', $name, $content, $limit=false)
	{	
		$content = self::clean($content);

		if($limit) $content = self::tokenTruncate($content,$limit);
		
		$output = self::render($content);
		
		return "<meta {$type}=\"{$name}\" content=\"{$output}\" />";
	}

	public static function otherMeta()
    {
        $settings = Settings::instance();
        if($settings->other_tags)
        {
            return $settings->other_tags;
        }
        return "";
    }

  public static function referrerMeta()
    {
        $settings = Settings::instance();

        if($settings->seo_meta_referrer)
        {
            return self::meta('name', 'referrer', 'unsafe-url');
        }
        return "";
    }

	public static function canonical($url=false)
	{	
		$settings = Settings::instance();
        
        if($url){
			return "<link rel=\"canonical\" href=\"{$url}\" />";
        } else {
	        if($settings->enable_canonical_url)
	        {
	            return '<link rel="canonical" href="'. Request::url().'"/>';
	        }
	    }

	    return "";

	}

	public static function og_meta($title,$description,$canonical=null,$social_image=null)
    {
        try {
          $settings = Settings::instance();
          if($settings->enable_og_tags)
          {
              $ogTags = "";
              
              if($settings->og_fb_appid)
                  $ogTags  .= '<meta property="fb:app_id" content="'.$settings->og_fb_appid.'" />' ."\n" ;
              
              if($settings->og_sitename)
                  $ogTags  .= '<meta property="og:site_name" content="'.$settings->og_sitename .'" />'."\n" ;
              
              $ogUrl = empty($post->canonical_url) ? Request::url() : $this->page->canonical_url ;

              $ogTags .= '<meta property="og:description" content="'.$description.'" />'."\n" ;
              $ogTags .= '<meta property="og:title" content="'. $title .'" />'."\n" ;
              $ogTags .= '<meta property="og:url" content="'. $ogUrl .'" />'."\n" ;
              if(!empty($social_image)){
              	$ogTags .= '<meta property="og:image" content="'. $social_image .'" />'."\n" ;
              	$ogTags .= '<meta property="og:image:width" content="1200" />'."\n" ;
              	$ogTags .= '<meta property="og:image:height" content="630" />'."\n" ;
              }

              return $ogTags;
          }
        } catch (\Exception $e) {}
    }

    public static function twitter_card($title,$description,$social_image=null,$creator=null,$type = 'summary_large_image')
    {
        try {
          $settings = Settings::instance();
          if($settings->enable_twitter_card)
          {
              $cardTags = "";
              
              $cardTags .= self::meta('name', 'twitter:card', $type)."\n" ;

              if($settings->twitter_card_site)
                  $cardTags  .= self::meta('name', 'twitter:site', $settings->twitter_card_site)."\n" ;
              
              if(!empty($creator))
                  $cardTags  .= self::meta('name', 'twitter:creator', $creator)."\n" ;
              
              $cardTags .= self::meta('name', 'twitter:title', $title)."\n" ;
              $cardTags .= self::meta('name', 'twitter:description', $description)."\n" ;

              if(!empty($social_image))
              	$cardTags .= self::meta('name', 'twitter:image', $social_image)."\n" ;

              return $cardTags;
          }
        } catch (\Exception $e) {}
    }

	public static function og_image($image)
	{	
		return self::meta('property', 'og:image', $image);
	}

	/*
	 * Accorcia la stringa mantenendo le parole intere
	 * https://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara
	 */
	public static function tokenTruncate($string, $your_desired_width) {
		$parts = preg_split('/([\s\n\r]+)/u', $string, null, PREG_SPLIT_DELIM_CAPTURE);
		$parts_count = count($parts);

		$length = 0;
		$last_part = 0;
		for (; $last_part < $parts_count; ++$last_part) {
			$length += strlen($parts[$last_part]);
			if ($length > $your_desired_width) { break; }
		}

		$text = trim(implode(array_slice($parts, 0, $last_part)));
		$text[strlen($text)-1] = (!ctype_alnum($text[strlen($text)-1])?"":$text[strlen($text)-1]);

		return preg_replace('/\s+/', ' ', trim($text));
	}

	public static function short($string, $length)
	{
		$short = self::tokenTruncate($string, $length);
		return self::render($short);
	}

	private static function clean($string)
	{
		return trim(strip_tags($string));
	}

	public static function render($string)
	{
		return htmlspecialchars(strip_tags($string), ENT_QUOTES, 'UTF-8', false);
	}
}