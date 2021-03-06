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

  	public static function otherMeta()
    {
        $settings = Settings::instance();
        if($settings->other_tags)
        {
            return $settings->other_tags;
        }
        return "";
    }

    public static function geoTag($is_home=true,$components=false,$geo_lat=false,$geo_lng=false)
    {
        $settings = Settings::instance();

        $geoTag = "";
        if( ($settings->seo_geotag && $is_home) || ($settings->seo_geotag && $components && $geo_lat && $geo_lng) )
        {
            if($is_home){
                $region = $settings->country."-".$settings->province;
                $position = round($settings->lat,6).";".round($settings->lng,6);
                $icbm = round($settings->lat,6).", ".round($settings->lng,6);
                $city = $settings->city;
            } else {
                $region = $components['country']."-".$components['province'];
                $position = round($geo_lat,6).";".round($geo_lng,6);
                $icbm = round($geo_lat,6).", ".round($geo_lng,6);
                $city = $components['city'];
            }

            $geoTag .= self::meta('name', 'geo.region', $region)."\n\t";
            $geoTag .= self::meta('name', 'geo.placename', $city)."\n\t";
            $geoTag .= self::meta('name', 'geo.position', $position)."\n\t";
            $geoTag .= self::meta('name', 'ICBM', $position)."\n";

            return $geoTag;
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

                $ogTags .= self::meta('property', 'og:type', 'article')."\n\t" ;

                if($settings->og_fb_appid)
                  $ogTags .= self::meta('property', 'fb:app_id', $settings->og_fb_appid)."\n\t" ;
                
                if($settings->og_sitename)
                  $ogTags .= self::meta('property', 'og:site_name', $settings->og_sitename)."\n\t" ;
                
                $ogUrl = empty($post->canonical_url) ? Request::url() : $this->page->canonical_url ;

                if($description)
                  $ogTags .= self::meta('property', 'og:description', $description, $settings->seo_facebook_maxlength)."\n\t" ;

                $ogTags .= self::meta('property', 'og:title', $title)."\n\t" ;
                $ogTags .= self::meta('property', 'og:url', $ogUrl)."\n\t" ;

                if(!empty($social_image)){
                  $ogTags .= self::meta('property', 'og:image', $social_image)."\n\t" ;
                  $ogTags .= self::meta('property', 'og:image:width', 1200)."\n\t" ;
                  $ogTags .= self::meta('property', 'og:image:height', 630)."\n\t" ;
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
              
              $cardTags .= self::meta('name', 'twitter:card', $type)."\n\t" ;

              if($settings->twitter_card_site)
                  $cardTags  .= self::meta('name', 'twitter:site', $settings->twitter_card_site)."\n\t" ;
              
              if(!empty($creator))
                  $cardTags  .= self::meta('name', 'twitter:creator', $creator)."\n\t" ;
              
              $cardTags .= self::meta('name', 'twitter:title', $title)."\n\t" ;

              if($description)
                $cardTags .= self::meta('name', 'twitter:description', $description)."\n\t" ;

              if(!empty($social_image))
              	$cardTags .= self::meta('name', 'twitter:image', $social_image)."\n\t" ;

              return $cardTags;
          }
        } catch (\Exception $e) {}
    }

  	public static function og_image($image)
  	{	
  		  return self::meta('property', 'og:image', $image);
  	}

    public static function meta($type='property', $name, $content, $limit=false)
    { 
        $content = self::clean($content);

        if($limit) $content = self::tokenTruncate($content,$limit);
        
        $output = self::render($content);
        
        return "<meta {$type}=\"{$name}\" content=\"{$output}\" />";
    }

  	/*
  	 * Accorcia la stringa mantenendo le parole intere
  	 * https://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara
  	 */
  	public static function tokenTruncate($string, $your_desired_width) {
        if(strlen($string) < 1) return;
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