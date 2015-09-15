<?php
/**
 * ASCII ART GENERATOR
 * 
 * I, Jonathan Ford, release this into the public domain, in the hope that someone, somewhere, may find it useful or interesting. As such, it comes WITHOUT WARRANTY, without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 * However, if you do find a use for this, please try and get in touch. An email, IM or even a link to my site would be nice :-).
 * 
 * Requirements: PHP 5 or greater, a fairly recent version of GD (with GIF support)
 * Notes: I know this class isn't very efficient, or even properly OOP. It does the job, though. If you're having problems opening images, allow_url_fopen needs to be turned on in php.ini. For details of how to do this see http://uk.php.net/manual/en/ref.filesystem.php#ini.allow-url-fopen. Any other problems, please email me.
 *
 * @file     ascii.class.php
 * @author   Jonathan Ford
 * @email    sirjavabean@gmail.com
 * @website  http://www.techhappens.com
 * @created  24/01/2007
 * @modified 26/01/2007
*/

namespace Mrchimp;

/**
 * Makes ASCII art from images.
 */
class Ascii
{

	var $headers, $image, $url;
	var $size = 2; // Default size
	var $quality = 2; // Default quality
	var $color = false; // Default color/no color (true/false)
	var $chars = array('@', '#', '+', '\'', ';', ':', ',', '.', '`', ' '); // Characters to use when generating a B/W picture, in order from darkest to lightest
	var $color_char = '#'; // The block character for color pictures
	const max_filesize = 6000000; // Maximum filesize of image in bytes. Make sure you keep this below the memory_limit specified in php.ini
	
	public function __construct($url = 'http://ascii.techhappens.com/tux.gif')
	{
		$this->url = $url;
		
		// We only want to get the headers, so do a HEAD request instead of GET (default)
		$opts = array(
			'http' => array(
				'method' => 'HEAD'
				)
			);
			
		$context = stream_context_get_default($opts);
		$this->headers = get_headers($this->url, 1);
		
		if(strstr($this->headers[0], '200') !== false) // Rather messy way of checking if the request went OK. Needs sorting sometime.
		{
			if($this->headers['Content-Length'] < self::max_filesize) // Check that the file isn't too big
			{
				if($this->is_image($this->headers['Content-Type']) !== false) // Makes sure that a content type was specified
				{
					// Pretty self-explanatory - figure out which sort of image we're going to be processing and let GD know
					switch($this->headers['Content-Type'])
					{
						case image_type_to_mime_type(IMAGETYPE_GIF):
							$this->image = imagecreatefromgif($this->url);
							break;
						case image_type_to_mime_type(IMAGETYPE_JPEG):
							$this->image = imagecreatefromjpeg($this->url);
							break;
						case image_type_to_mime_type(IMAGETYPE_PNG):
							$this->image = imagecreatefrompng($this->url);
							break;
						/*case image_type_to_mime_type(IMAGETYPE_BMP):
							$this->image = $this->imagecreatefrombmp($this->url);
							break;*/
						case image_type_to_mime_type(IMAGETYPE_WBMP):
							$this->image = imagecreatefromwbmp($this->url);
							break;
						case image_type_to_mime_type(IMAGETYPE_XBM):
							$this->image = imagecreatefromxbm($this->url);
							break;
						default:
							die('Something\'s gone horribly wrong...'); // If this happens scream very loudly and bang your head into a wall (don't forget, this program comes WITHOUT WARRANTY :-P)
							break;
					}
					
					
				}
				else
				{
					$this->error('Could not determine image type (' . $this->headers['Content-Type'] . '), please use a format compatible with the GD library.');
				}
			}
			else
			{
				$this->error('Sorry, you image is too large. Please limit filesize to ' . round(self::max_filesize / 1024) . 'KB.');
			}
		}
		else
		{
			$this->error('URL ' . $this->url . ' cannot be accessed. Please check the file exists :-).');
		}
	}
	
	// A stupid error class, I guess I'm just lazy ;-)
	public function error($message)
	{
		echo '<div class="error">' . $message . '</div>';
	}
	
	// Figure out if the file is in a (GD) supported file format or not
	public function is_image($content_type)
	{
		switch($content_type)
		{
			case image_type_to_mime_type(IMAGETYPE_GIF):
			case image_type_to_mime_type(IMAGETYPE_JPEG):
			case image_type_to_mime_type(IMAGETYPE_PNG):
			//case image_type_to_mime_type(IMAGETYPE_BMP): BMP doesn't work (yet?) :-(
			case image_type_to_mime_type(IMAGETYPE_WBMP):
			case image_type_to_mime_type(IMAGETYPE_XBM):
				return true;
				break;
			default:
				return false;
				break;
		}
	}
	
	// Draw the ASCII art. Color support is implemented really badly - I warned you!
	public function draw($img = '')
	{
		if(empty($img) === true) $img = $this->image; // Make sure there's *something* in the image
		
		$width = imagesx($img); // Work out the width
		$height = imagesy($img); // Work out the height
		
		// If we're working in colour start our <span>s
		if($this->color === true)
		{
			$pixel_color = imagecolorat($img, 1, 1);
			$rgb = imagecolorsforindex($img, $pixel_color);
			$output = '<span style="color: ' . $this->rgbtohex($rgb['red'], $rgb['green'], $rgb['blue']) . ';">';
		}
		else
		{
			$output = '';
		}
		
		// Start looping through pixels working out how bright/colorful they are. I suppose this probably should be stuck into an array before we sort out the output
		for($y = 0; $y < $height; $y = $y + $this->quality)
		{
			for($x = 0; $x < $width; $x = $x + $this->quality)
			{
				$pixel_color = imagecolorat($img, $x, $y); // Get pixel color at x,y
				$rgb = imagecolorsforindex($img, $pixel_color); // Make the color into an array we can use
				
				// Do some more color processing stuff
				if($this->color === true)
				{
					// Work out if the last pixel is the same as this one
					if($x > $this->quality && $y > $this->quality && $pixel_color == imagecolorat($img, $x - $this->quality, $y))
					{
						$char = $this->color_char;
					}
					// Or if it's not...
					else
					{
						$char = '</span><span style="color: ' . $this->rgbtohex($rgb['red'], $rgb['green'], $rgb['blue']) . ';">#';
					}
				}
				// Work out the "brighness" by adding up the RGB values and doing some division
				else
				{
					$brightness = $rgb['red'] + $rgb['green'] + $rgb['blue'];
					$brightness = round($brightness / (765 / (count($this->chars) - 1)));
					$char = $this->chars[$brightness];
				}
				$output .= $char;
			}
			$output .= "\n"; // Newline, might need adjusting on Windows systems (though *seems* to work OK, at least in a browser)
		}
		
		// Close our colorfulness
		if($this->color === true)
		{
			$output .= '</span>';
		}
		
		return $output;
	}
	
	// Converts RGB (red, green, blue) values to their hex equivalent (for HTML)
	public function rgbtohex($red, $green, $blue)
	{
		$hex = '#';
		$hex .= str_pad(dechex($red), 2, '0', STR_PAD_LEFT);
		$hex .= str_pad(dechex($green), 2, '0', STR_PAD_LEFT);
		$hex .= str_pad(dechex($blue), 2, '0', STR_PAD_LEFT);
		return($hex);
	}
	
	// Lets clean up after ourselves
	public function __destruct()
	{
		imagedestroy($this->image); // Remove the image from the memory (important with some configurations)
	}
	
}

?>