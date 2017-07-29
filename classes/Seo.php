<?php namespace Inerba\SocialSeo\Classes;

//use Arcanedev\SeoHelper\Entities\Title;
/*
      <title>5 Tactics to Earn Links Without Having to Directly Ask - Whiteboard Friday - Moz</title>
      <meta name="viewport" content="width=device-width, initial-scale=1" />  <meta name="description" content="Stop bumming out journalists, bloggers, and content creators by begging for links. Try out some of Rand's tactics in this Whiteboard Friday to earn those links without the painful ask (and low success rate)." />

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

    */
class Seo
{
	const DESCRIPTION_LENGTH = 155;

	public static function title($string)
	{
		$output = self::render($string);
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

	public static function canonical($url)
	{	
		return "<link rel=\"canonical\" href=\"{$url}\" />";
	}

	/*
	 * Accorcia la stringa mantenendo le parole intere
	 * https://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara
	 */
	private static function tokenTruncate($string, $your_desired_width) {
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

		return trim($text);
	}

	private static function clean($string)
	{
		return trim(strip_tags($string));
	}

	private static function render($string)
	{
		return htmlspecialchars(strip_tags($string), ENT_QUOTES, 'UTF-8', false);
	}
}