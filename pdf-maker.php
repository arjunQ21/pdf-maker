<?php 
require_once "vendor/autoload.php" ;
require_once "vendor/setasign/fpdf/fpdf.php" ;
ini_set("memory_limit", '256M') ;

use PHPImageWorkshop\ImageWorkshop as Editor ;

define("image_types", ['jpg', 'png', 'jpeg']);

$imageDir = null ;
while(1){
	echo "\nEnter path to images: ";
	$imageDir = readline() ;
	if(file_exists($imageDir)){
		$imageDir = realpath($imageDir) ;
		break ;
	}
	echo "\nThat folder could not be found. Enter a valid folder." ;
}

$images = array_filter(scandir($imageDir), function($file){
	return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), image_types) ;
}) ;
$noOfImages = count($images) ;
if(!($noOfImages)) die("There are no images in this folder.") ;
echo "\nDetected $noOfImages images." ;
// print_r($images) ;
$i = 1 ;
$pdf = new fpdf() ;
foreach($images as $img){
	echo "\nProcessing image (".$i++."/". $noOfImages.") => ". $img ;
	$layer = Editor::initFromPath($imageDir."/".$img);
	$layer->resizeInPixel(1500, null, true) ;
	echo "\tresized" ;
	$layer->rotate(90) ;
	echo "\trotated" ;
	$layer->save("edited", "edited_".$img, true, null, 95 ) ;
	$pdf->addPage() ;
	$pdf->image("edited/edited_".$img,0,0, 210, 297) ;
	echo "\tadded to pdf" ;
}
echo "\nCompleted." ;
echo "\n\nEnter name to give to that pdf file: ";
while(1){
	$name = readline() ;
	if(!(trim($name))){
		echo "\nEnter name again: " ;
		continue ;
	} 
	$path = pathinfo($name, PATHINFO_DIRNAME) ;
	if(!realpath($path)){
		echo "\nThat folder doesnot exist. Enter again: " ;
		continue ;		
	}
	break ; 
}

$pdf->Output("F", $name.".pdf") ;
echo "\nFile saved at '".__DIR__."/$name".".pdf'" ;