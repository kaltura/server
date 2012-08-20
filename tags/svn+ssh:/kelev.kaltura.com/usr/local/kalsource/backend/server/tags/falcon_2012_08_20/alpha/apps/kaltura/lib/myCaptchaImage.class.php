<?php
//session_start();

//require_once('random-back-image4.jpg');

class myCaptchaImage {

	protected $backgroundimg;
	protected $sizex;
	protected $sizey;
	protected $yofs;
	protected $random;
	protected $length;
	protected $font;
	protected $size;
	protected $bold;
	protected $red;
	protected $green;
	protected $blue;
// set config paramaters
private function initialize()
	{
		$this->backgroundimg = "images/captchabk.jpg"; // this is the image used for random background creation
		
		$this->sizex = 188; // captcha image width pixels
		$this->sizey = 60; // captcha image height pixels
		
		$this->yofs = 3; // VERTICAL text offset for font (varies with font) to get it 'just so'
		$this->random = 0; // 1 is random rotation, 0 is no rotation
		
		$this->length = 5; // number of characters in security code (must fit on your image!)
		
		$this->font = "images/1942.ttf";
		$this->size = 36; // pixel size of the font used may need adjusting depending on your chosen font
		$this->bold = 1; // 0=OFF. Some fonts/backgrounds will need bold text, so change $bold=0 to $bold=1
		
		$this->red = 51; // RGB red channel 0-255 for font color
		$this->green = 106; // RGB green channel 0-255 for font color
		$this->blue = 162; // RGB blue channel 0-255 for font color
	}
	
	
	
	
	public  function getCaptcha() 
	{
		return $this->captcha;
	}
	
private	function imagettftext_cr(&$img, $size, $angle, $x, $y, $content_color, $font, $text) {
	    // retrieve boundingbox
	    $bbox = imagettfbbox($size, $angle, $font, $text);
	    // calculate deviation
	    $dx = ($bbox[2]-$bbox[0])/2.0 - ($bbox[2]-$bbox[4])/2.0; // deviation left-right
	    $dy = ($bbox[3]-$bbox[1])/2.0 + ($bbox[7]-$bbox[1])/2.0; // deviation top-bottom
	    // new pivotpoint
	    $px = $x-$dx;
	    $py = $y-$dy;
	    return imagettftext($img, $size, $angle, $px, $py, $content_color, $font, $text);
	}
	
	
	public  function getImage() 
	{
		
	$this->initialize();
		
	// create random character code for the captcha image
	$text = "";
	$key_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // 0 1 O I removed to avoid confusion
	$rand_max  = strlen($key_chars) - 1;
	for ($i = 0; $i < $this->length; $i++) {
	    $rand_pos  = rand(0, $rand_max);
	    $text.= $key_chars{$rand_pos};
	}
	$this->captcha = $text; // save what we create
	
	// center text in the 'box' regardless of rotation
	
	// get background image dimensions
	$imgsize = getimagesize($this->backgroundimg); 
	$height = $imgsize[1];
	$width = $imgsize[0];
	$xmax = $width - $this->sizex;
	$ymax = $height - $this->sizey;
	
	// create the background in memory so we can grab chunks for each random image
	$copy = imagecreatefromjpeg($this->backgroundimg);
	
	// create the image
	$img = imagecreatetruecolor($this->sizex,$this->sizey);
	$content_color = imagecolorallocate($img, $this->red, $this->green, $this->blue); 
		
	// choose a random block (right size) of the background image
	$x0 = rand(0,$xmax); $x1 = $x0 + $this->sizex;
	$y0 = rand(0,$ymax); $y1 = $y0 + $this->sizey;
	
	imagecopy($img,$copy, 0, 0, $x0, $y0, $x1, $y1);
	$angle = $this->random * (5*rand(0,8) - 20); // random rotation -20 to +20 degrees
	
	// add text to image once or twice (offset one pixel to emulate BOLD text if needed)
	$this->imagettftext_cr($img, $this->size, $angle, $this->sizex/2, $this->sizey/2-$this->yofs, $content_color, $this->font, $text);
	if ($this->bold==1) {
	    $this->imagettftext_cr($img, $this->size, $angle, $this->sizex/2+1, $this->sizey/2-$this->yofs, $content_color, $this->font, $text);
	}
	
	return $img;
	}
		
}
	
?>