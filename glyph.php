<?php
/**
 * Glyph image
 * 
 * Copyright 2007-2010 Stephen E Slevinski Jr
 * Steve (Slevin@signpuddle.net)
 * 
 * This file is part of SWIS: the SignWriting Image Server.
 * 
 * SWIS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * SWIS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with SWIS.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * END Copyright
 *  
 * @copyright 2007-2010 Stephen E Slevinski Jr 
 * @author Steve (slevin@signpuddle.net)  
 * @license http://www.opensource.org/licenses/gpl-3.0.html GPL
 * @access public
 * @package SWIS
 * @version 1.2.0
 * @filesource
 *   
 */

/**
 * include general iswa library
 */
include 'bsw.php';
include 'spl.php';
include 'image.php';

/**
 * attributes
 */ 
$key = @$_REQUEST['key'];
$code = @$_REQUEST['code'];
$bsw = @$_REQUEST['bsw'];
$bsw2 = @$_REQUEST['bsw2'];
$sym = @$_REQUEST['sym'];
if($sym){
  $key = bsw2key(utf2bsw($sym));
//  echo urlencode($sym) . ' and ' . $key;
}
$style = @$_REQUEST['style'];
if (!$style) $style='png1';

$size = @$_REQUEST['size'];
$line = @$_REQUEST['line'];
$fill = @$_REQUEST['fill'];
$back = @$_REQUEST['back'];
$break = @$_REQUEST['break'];
$colorize = @$_REQUEST['colorize'];
$name= @$_REQUEST['name'];
if(!$name){$name='glyph';}

//testing and setting
if ($code){
  //determine BaseSymbol
  $base = intval(($code-1)/96)*96 + 1;
  $offset = $code-$base;
  $drot = $offset % 16;
  $dfill = ($offset-$drot)/16;
  $base = dechex(intval(($code-1)/96) + 256);
  $key = $base . dechex($dfill) . dechex($drot);
} else if ($key){
  $base = substr($key,0,3);
} else if ($bsw){
  $base = substr($bsw,0,3);
  if (strlen($bsw)>3){
    $hfill = char2fill(substr($bsw,3,3));
    $hrot = char2rot(substr($bsw,6,3));
    $key = $base . $hfill . $hrot;
  } else {
    $key = base2view($base);
  }
} else if ($bsw2){
  $bsw = bsw2bsw3($bsw2);
  $base = substr($bsw,0,3);
  if (strlen($bsw)>3){
    $hfill = char2fill(substr($bsw,3,3));
    $hrot = char2rot(substr($bsw,6,3));
    $key = $base . $hfill . $hrot;
  } else {
    $key = base2view($base);
  }
} else {
  die();
}
//display
$fmt = substr($style,0,3);
$ver = substr($style,3,1);
switch ($fmt){
  case "txt":
    header("Content-type: text/plain");
    header('Content-Disposition: filename=' . $name . '.txt');
    echo glyph_txt($key, $ver, $line, $fill, $back, $break);
    break;
  case "svg":
    header("Content-type: image/svg+xml");
    header('Content-Disposition: filename=' . $name . '.svg');
    echo glyph_svg($key, $ver, $size, $line, $fill, $back, $colorize);
    break;
  default://png
    header("Content-type: image/png");
    header('Content-Disposition: filename=' . $name . '.png');
    ImagePNG(glyph_png($key,$ver,$size,$line, $fill, $back, $colorize));
}

?>

