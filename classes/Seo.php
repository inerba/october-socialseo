<?php namespace Inerba\SocialSeo\Classes;

use Request;
use Inerba\SocialSeo\Models\Settings;
//use Arcanedev\SeoHelper\Entities\Title;
/*
      <title>5 Tactics to Earn Links Without Having to Directly Ask - Whiteboard Friday - Moz</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />  
      <meta name="description" content="Stop bumming out journalists, bloggers, and content creators" />

      <link rel="canonical" href="https://moz.com/blog/earn-links-directly" />

      <meta name="twitter:site" content="@Moz" />
      <meta property="twitter:account_id" content="15651700" />
      <meta property="fb:page_id" content="8489236245" />
      <meta property="og:image" content="https://d1avok0lzls2w.cloudfront.net/uploads/og_image/597ab34f8042c1.11997587.jpg" />
      <meta property="og:title" content="5 Tactics to Earn Links Without Having to Directly Ask - Whiteboard Friday" />
      <meta property="og:description" content="Stop bumming out journalists, bloggers, and content creators by begging for links. Try out some of Rand&#039;s tactics in this Whiteboard Friday to earn those links without the painful ask (and low success rate)." />
      <meta property="og:type" content="article" />
      <meta property="og:site_name" content="Moz" />
      <meta property="fb:admins" content="22408537" />
      <meta property="og:url" content="https://moz.com/blog/earn-links-directly" />
      <meta property="twitter:title" content="5 Tactics to Earn Links Without Having to Directly Ask - Whiteboard Friday" />
      <meta property="twitter:description" content="Stop bumming out journalists, bloggers, and content creators by begging for links. Try out some of Rand&#039;s tactics in this Whiteboard Friday to earn those links without the painful ask (and low success rate)." />
      <meta property="twitter:card" content="summary_large_image" />
      <meta property="twitter:image:src" content="https://d1avok0lzls2w.cloudfront.net/uploads/twitter_image/597ab34e8fde50.38855560.jpg" />
      <meta property="twitter:creator" content="@randfish" />
      <meta name="referrer" content="unsafe-url">

      <meta name="twitter:site" content="@Moz" />
      <meta property="twitter:card" content="summary_large_image" />
      <meta property="twitter:creator" content="@randfish" />

    */
class Seo
{
	const DESCRIPTION_LENGTH = 155;

	public static function title($title)
	{
		$settings = Settings::instance();

		if($settings->enable_title)
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

	public static function description($string,$limit=self::DESCRIPTION_LENGTH)
	{
		return self::meta('name', 'description', $string, $limit);
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
    }

    public static function twitter_card($title,$description,$social_image=null,$creator=null,$type = 'summary_large_image')
    {
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