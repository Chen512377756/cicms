<?php
class M_captcha extends Model{
    var $code = '';
    var $fontFile;
    var $validate;
    var $image;
    var $specialAdd = 'asdDSAKJH*&@#^&*!sjkad&*675765^&%';
    var $codeExpire=86400;
    var $codeCookieName='imgcde';
    var $fontSize=14;
    
    function __construct()
    {
        $this->fontFile = dirname( __FILE__ ) . '/arial.ttf';
        //$this->fontFile = dirname( __FILE__ ) . '/type-ra.ttf';
        //$this->fontFile = dirname( __FILE__ ) . '/monaco.ttf';
    }

    /**
     * 指定字体文件所在路径，默认为当前文件夹下arial.ttf文件
     * @param $fontFile 文件路径
     * @return void
     */
    function loadFont($fontFile)
    {
        $this->fontFile = $fontFile;
    }

    /**
     * 图片输出方法，在执行本方法前程序不应该有任何形式的输出
     * @return void;
     */
    function stroke()
    {
        $this->saveCode();
        $this->sendHeader();
        imagegif( $this->validate );
        imagedestroy( $this->validate );
        imagedestroy( $this->image );
    }

    /**
     * 图片保存方法
     * @param $fileName 保存路径
     * @return void
     */
    function save($fileName)
    {
        $this->saveCode();
        imagegif( $this->validate , $fileName );
        imagedestroy( $this->validate );
        imagedestroy( $this->image );
    }
    
 	/**
     * 验证码验证方法
     * @param $input 要验证的字符串，即用户的输入内容
     * @return boolean 验证结果
     */
    function verify($input)
    {
        $input=strtolower($input);
        $targetCode=$this->authCode($input);
        $code=$this->getCookie();
        //echo "code=$code, tarc=$targetCode";exit;
        if(empty($code)||$code!=$targetCode)$result= false;
        else $result=true;
        $_COOKIE[$this->codeCookieName]='';
        setcookie ( $this->codeCookieName, '', -1 );
        return $result;
    }
    
    /**
     * 验证码加密方法
     * @param string $code 要加密的随机码
     * @return mixed 执行结果
     */
    function authCode($code)
    {
        return md5($code.$this->specialAdd);
    }
    
    /**
     * 图片创建方法
     * @return void;
     */
    function create_image()
    {
        $this->randCode();
        
        $size = 25;
        $width = 80;
        $height = 26;
        $degrees = array (
            rand( 0 , 30 ), rand( 0 , 30 ), rand( 0 , 30 ), rand( 0 , 30 )
        );
        

        for ($i = 0; $i < 4; ++$i)
        {
            if (rand() % 2);
            else $degrees[$i] = -$degrees[$i];
        }
        
        $this->image = imagecreatetruecolor( $size , $size );
        $this->validate = imagecreatetruecolor( $width , $height );
        $back = imagecolorallocate( $this->image , 255 , 255 , 255 );
        $border = imagecolorallocate( $this->image , 0 , 0 , 0 );
        imagefilledrectangle( $this->validate , 0 , 0 , $width , $height , $back );
        
        for ($i = 0; $i < 4; ++$i)
        {
            $temp = $this->RgbToHsv( rand( 0 , 250 ) , rand( 0 , 150 ) , rand( 0 , 250 ) );
            
            if ($temp[2] > 60) $temp[2] = 60;
            
            $temp = $this->HsvToRgb( $temp[0] , $temp[1] , $temp[2] );
            $textcolor[$i] = imagecolorallocate( $this->image , $temp[0] , $temp[1] , $temp[2] );
        }

        /*
		// -----------------------------------
		// Determine angle and position
		// -----------------------------------
		$length = 4;
		$angle =($length >= 6)? rand(-($length - 6),($length - 6)): 0;
		$x_axis = rand(6,(360 / $length)- 16);
		$y_axis =($angle >= 0)? rand($height, $width): rand(6, $height);
        
		// -----------------------------------
		//  Create the spiral pattern
		// -----------------------------------
		$theta = 1;
		$thetac = 7;
		$radius = 16;
		$circles = 200;
		$points = 32;

		for($i = 0; $i <($circles * $points)- 1; $i ++)
		{
			$grid_color = imagecolorallocate($this->validate, mt_rand(10, 160), mt_rand(10, 160), mt_rand(10, 160));
			$theta = $theta + $thetac;
			$rad = $radius *($i / $points);
			$x =($rad * cos($theta))+ $x_axis;
			$y =($rad * sin($theta))+ $y_axis;
			$theta = $theta + $thetac;
			$rad1 = $radius *(($i + 1)/ $points);
			$x1 =($rad1 * cos($theta))+ $x_axis;
			$y1 =($rad1 * sin($theta))+ $y_axis;
			imageline($this->validate, $x, $y, $x1, $y1, $grid_color);
			$theta = $theta - $thetac;
		}
		*/
        
        for ($i = 0; $i < 200; ++$i)
        {
            $randpixelcolor = ImageColorallocate( $this->validate , rand( 0 , 255 ) , rand( 0 , 255 ) , rand( 0 , 255 ) );
            imagesetpixel( $this->validate , rand( 1 , 87 ) , rand( 1 , 35 ) , $randpixelcolor );
        }
        
        $temp = $this->RgbToHsv( rand( 220 , 255 ) , rand( 220 , 255 ) , rand( 220 , 255 ) );
        
        if ($temp[2] < 200) $temp[2] = 255;
        
        $temp = $this->HsvToRgb( $temp[0] , $temp[1] , $temp[2] );
        $randlinecolor = imagecolorallocate( $this->image , $temp[0] , $temp[1] , $temp[2] );
        
        $this->imagelinethick( $this->validate , $textcolor[rand( 0 , 3 )] );
        
        imagefilledrectangle( $this->image , 0 , 0 , $size , $size , $back );
        //putenv( 'GDFONTPATH=' . realpath( '.' ) );
        
        // Name the font to be used (note the lack of the .ttf extension
        imagettftext( $this->image , $this->fontSize , 0 , 8 , 20 , $textcolor[0] , $this->fontFile , $this->code[0] );
        
        $this->image = imagerotate( $this->image , $degrees[0] , $back );
        imagecolortransparent( $this->image , $back );
        imagecopymerge( $this->validate , $this->image , 1 , 4 , 4 , 5 , imagesx( $this->image ) - 10 , imagesy( $this->image ) - 10 , 100 );
        
        $this->image = imagecreatetruecolor( $size , $size );
        imagefilledrectangle( $this->image , 0 , 0 , $size , $size , $back );
        imagettftext( $this->image , $this->fontSize , 0 , 8 , 20 , $textcolor[1] , $this->fontFile , $this->code[1] );
        $this->image = imagerotate( $this->image , $degrees[1] , $back );
        imagecolortransparent( $this->image , $back );
        imagecopymerge( $this->validate , $this->image , 21 , 4 , 4 , 5 , imagesx( $this->image ) - 10 , imagesy( $this->image ) - 10 , 100 );
        
        $this->image = imagecreatetruecolor( $size , $size );
        imagefilledrectangle( $this->image , 0 , 0 , $size - 1 , $size - 1 , $back );
        imagettftext( $this->image , $this->fontSize , 0 , 8 , 20 , $textcolor[2] , $this->fontFile , $this->code[2] );
        $this->image = imagerotate( $this->image , $degrees[2] , $back );
        imagecolortransparent( $this->image , $back );
        imagecopymerge( $this->validate , $this->image , 41 , 4 , 4 , 5 , imagesx( $this->image ) - 10 , imagesy( $this->image ) - 10 , 100 );
        
        $this->image = imagecreatetruecolor( $size , $size );
        imagefilledrectangle( $this->image , 0 , 0 , $size - 1 , $size - 1 , $back );
        imagettftext( $this->image , $this->fontSize , 0 , 8 , 20 , $textcolor[3] , $this->fontFile , $this->code[3] );
        $this->image = imagerotate( $this->image , $degrees[3] , $back );
        imagecolortransparent( $this->image , $back );
        imagecopymerge( $this->validate , $this->image , 61 , 4 , 4 , 5 , imagesx( $this->image ) - 10 , imagesy( $this->image ) - 10 , 100 );
        //imagerectangle( $this->validate , 0 , 0 , $width - 1 , $height - 1 , $border );
    }
    
    /**
     * 获取随机生成的验证码
     * @return string 随机验证码，返回的验证码不进行任何处理
     */
    function getCode()
    {
        return $this->code;
    }

    /**
     * 生成随机码方法
     * @return void;
     */
    function randCode()
    {
        $alphastr = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $randStr = array (
            $alphastr{rand( 0 , 31 )}, $alphastr{rand( 0 , 31 )}, $alphastr{rand( 0 , 31 )}, $alphastr{rand( 0 , 31 )}
        );
        $this->code = strtolower( $randStr[0] . $randStr[1] . $randStr[2] . $randStr[3] );
    }

    /**
     * RGB色到HSV色转变方法
     * @param $R
     * @param $G
     * @param $B
     * @return array HSV数组
     */
    function RgbToHsv($R, $G, $B)
    {
        $tmp = min( $R , $G );
        $min = min( $tmp , $B );
        $tmp = max( $R , $G );
        $max = max( $tmp , $B );
        $V = $max;
        $delta = $max - $min;
        
        if ($max != 0) $S = $delta / $max; // s
        else
        {
            $S = 0;
            //$H = UNDEFINEDCOLOR;
            return;
        }
        if ($R == $max) $H = ($G - $B) / $delta; // between yellow & magenta
        else if ($G == $max) $H = 2 + ($B - $R) / $delta; // between cyan & yellow
        else $H = 4 + ($R - $G) / $delta; // between magenta & cyan
        


        $H *= 60; // degrees
        if ($H < 0) $H += 360;
        return array (
            $H, $S, $V
        );
    }

    /**
     * 同上一方法功能相反
     * @param $H
     * @param $S
     * @param $V
     * @return array RGB数组
     */
    function HsvToRgb($H, $S, $V)
    {
        if ($S == 0)
        {
            // achromatic (grey)
            $R = $G = $B = $V;
            return;
        }
        
        $H /= 60; // sector 0 to 5
        $i = floor( $H );
        $f = $H - $i; // factorial part of h
        $p = $V * (1 - $S);
        $q = $V * (1 - $S * $f);
        $t = $V * (1 - $S * (1 - $f));
        
        switch ($i)
        {
            case 0 :
                $R = $V;
                $G = $t;
                $B = $p;
                break;
            case 1 :
                $R = $q;
                $G = $V;
                $B = $p;
                break;
            case 2 :
                $R = $p;
                $G = $V;
                $B = $t;
                break;
            case 3 :
                $R = $p;
                $G = $q;
                $B = $V;
                break;
            case 4 :
                $R = $t;
                $G = $p;
                $B = $V;
                break;
            default : // case 5:
                $R = $V;
                $G = $p;
                $B = $q;
                break;
        }
        return array (
            $R, $G, $B
        );
    }

    /**
     * 使用COOKIE保存验证码的方法
     * @return void
     */
    function saveCode()
    {
        $code = $this->authCode($this->code);
        $this->setCookie($code);
    }

    /**
     * 验证码COOKIE值获取方法
     * @return string COOKIE值
     */
    function getCookie()
    {
        if (empty( $_COOKIE[$this->codeCookieName] ))
        {
            return '';
        }
        else
        {
            return addslashes($_COOKIE[$this->codeCookieName]);
        }
    }
    
    /**
     * 验证码cookie创建方法
     * @param string $code 要保存的验证码
     * @return void
     */
    function setCookie($code)
    {
        $expire = $this->codeExpire > 0 ? $this->codeExpire + time() : 0;
        setcookie( $this->codeCookieName , $code, $expire, "/", "" );
    }
    
    /**
     * 干扰线生成方法
     * @param resource $image 图片资源句柄
     * @param string $color 干扰线颜色
     */
    function imagelinethick($image, $color)
    {
        $k = rand( 5 , 20 );
        for ($px = 0; $px < 400; $px = $px + 1)
        {
            $y = $k * sin( 0.1 * ($px) ); //$y=200+10*sin(0.1*($px-200));
            for ($i = 0; $i < 2; $i++)
            {
                imagesetpixel( $image , $px , $y + 10 + $i , $color );
            }
        
        }
    }

    /**
     * HTTP标头设置方法
     * @return void
     */
    function sendHeader()
    {
        header( "Pragma: no-cache" );
        header( "Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate" );
        header( 'Content-type: image/gif' );
    }
}