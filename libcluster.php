<?php
## Copyright (C) 2011 Leonardo Yamasaki Maza
## 
## This program is free software; you can redistribute it and/or modify
## it under the terms of the GNU General Public License as published by
## the Free Software Foundation; either version 2 of the License, or
## (at your option) any later version.
## 
## This program is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.
## 
## You should have received a copy of the GNU General Public License
## If not, see <http://www.gnu.org/licenses/>.

/*
**  Este código es un ejemplo de implementación del algoritmo k-means 
**  (http://en.wikipedia.org/wiki/K-means_clustering)  
**  Necesita tener las bibliotecas Math_Vector y Math_Matrix de http://pear.php.net/
**
*/


require_once('Math/Vector.php');
require_once('Math/Matrix.php');

function lstest_Kmeans($data, $k,$measure = 'cartesian',$maxiterations = 20)
{
	$dim = $data->getSize();
	$m = $dim[0];
	$n = $dim[1];
	$clusters = Math_Matrix::makeMatrix($m,1,0);
	$centroids = Math_Matrix::makeMatrix($m,$n,0);
	$distmatrix = Math_Matrix::makeMatrix($m,$k,0);
	lstest_AssignInitialPositions($data, $k, $clusters);
	lstest_CalculateCentroids($data,$k,$clusters,$centroids);
	lstest_MakeDistMatrix($data,$k,$centroids,$distmatrix,$measure);
	$change = true;
	$minindex = array();
	$rowtest = Math_Matrix::makeMatrix(1,$k,0);
	while($change)
	{
		$iternum++;
		if($iternum > $maxiterations){
			break;
		}
		$change = false;
		for($i=0;$i<$m;$i++){
			$rowtest->setData(array($distmatrix->getRow($i)));
			$minindex = $rowtest->getMinIndex();           //Get minimum distance of any centroid
			if($minindex[1]!=$clusters->getElement($i,0)){ //If not assigned to the minimun
				$clusters->setElement($i,0,$minindex[1]);  //then change classe assign
				$change = true;
			}
		}
		if($change){ //then recalculate centroids
			lstest_CalculateCentroids($data,$k,$clusters,$centroids);
			lstest_MakeDistMatrix($data,$k,$centroids,$distmatrix,$measure);
		}
	}
	return $clusters;
}

function lstest_MakeDistMatrix($data,$k,$centroids,$distances,$measure = 'cartesian'){
	$dim = $data->getSize();
	$m = $dim[0];
	$n = $dim[1];
	$x = new Math_Vector();
	$y = new Math_Vector();
	for($i=0;$i<$m;$i++){
		for($kn=0;$kn<$k;$kn++){
			$x->setData($data->getRow($i));
			$y->setData($centroids->getRow($kn));
			$s = $x->distance($y,$measure); //Can choose 'cartesian'=euclidean or 'chessboard'=manhattan
			$distances->setElement($i,$kn,round($s,3));
		}
	}
	unset($x);
	unset($y);
}

function lstest_CalculateCentroids($data,$k,$klass,$centroids){
	$decimales = 3;
	$dim = $data->getSize();
	$m = $dim[0];
	$n = $dim[1];
	if($k==0){
		die("Need one class minimum");
	}
	$sumklass = Math_Matrix::makeMatrix($k,1,0);
	$sumcent  = Math_Matrix::makeMatrix($k,$n,0);
	for($i=0;$i<$m;$i++){
		$nclases = $sumklass->getElement($klass->getElement($i,0),0);
		$sumklass->setElement($klass->getElement($i,0),0,$nclases + 1);
		for($j=0;$j<$n;$j++){
			$acumulado = $sumcent->getElement($klass->getElement($i,0),$j) + $data->getElement($i,$j);
			$sumcent->setElement($klass->getElement($i,0),$j,$acumulado);
		}
	}
	for($i=0;$i<$k;$i++){
		for($j=0;$j<$n;$j++){
			$acumulado = $sumcent->getElement($i,$j);
			$nclases = $sumklass->getElement($i,0);
			$centroids->setElement($i,$j,round($acumulado/$nclases,$decimales));
		}
	}
	unset($sumklass);
	unset($sumcent);
}

function lstest_AssignInitialPositions($data,$k,$class){
	$dim = $data->getSize();
	$m = $dim[0];
	$n = $dim[1];
	for($i=0;$i<$m;$i++){
		$class->setRow($i,array(mt_rand(0,$k-1)));
	}
}

function lstest_plot($imgfilename,$data,$k,$clases,$centroids){
	// Size of the image 
	$width  = 500;
	$height = 500;
	// data
	$dim = $data->getSize();
	$m = $dim[0];
	$n = $dim[1];
	// Create image 
	$image = @imagecreate( $width , $height ) 
	    or die( "Cannot Initialize new GD image stream" ); 
	
	// Create basic color map 
	$colormap = array(); 
	$colormap[10] = imagecolorallocate($image, 255, 255, 255);//Background
	// Create a standard color palette 
	$colormap[0] = imagecolorallocate( $image,   0,   0,   0);//Black
	$colormap[1] = imagecolorallocate( $image, 192,   0,   0);//Red
	$colormap[2] = imagecolorallocate( $image,   0, 192,   0);//Green
	$colormap[3] = imagecolorallocate( $image,   0,   0, 192);//Blue
	$colormap[4] = imagecolorallocate( $image,  48,  48,   0);//Brown
	$colormap[5] = imagecolorallocate( $image,   0, 192, 192);//Cyan
	$colormap[6] = imagecolorallocate( $image, 192,   0, 192);//Purple
	$colormap[7] = imagecolorallocate( $image, 255, 255,   0);//Yellow
	$colormap[8] = imagecolorallocate( $image, 192, 192, 192);//LightGray
	$colormap[9] = imagecolorallocate( $image, 255,   0,   0);//LightRed
	//DrawBorder
	$colorborder = $colormap[0];
	imageline($image,0,0,$width-1,0,$colorborder);//top
	imageline($image,0,0,0,$height-1,$colorborder);//left
	imageline($image,$width-1,0,$width-1,$height-1,$colorborder);//right
	imageline($image,0,$height-1,$width-1,$height-1,$colorborder);//bottom
	// Set scale items
	$max = 100;
	$min = 1;
	$scale = $max - $min;
	$sizepoint = 7; // size of circle
	// draw points of data
	for($i=0;$i<$m;$i++){
		$x = $data->getElement($i,0)/$scale * $width;
		$y = $data->getElement($i,1)/$scale * $height;
		$c = $clases->getElement($i,0);
		imagefilledellipse($image,$x,$y,$sizepoint,$sizepoint,$colormap[$c]);
	}
	//draw points of centroids
	for($i=0;$i<$k;$i++){
		$x = $centroids->getElement($i,0);
		$y = $centroids->getElement($i,1);
		$x = $centroids->getElement($i,0)/$scale * $width;
		$y = $centroids->getElement($i,1)/$scale * $height;
		imageellipse($image,$x,$y,$sizepoint*1.5,$sizepoint*1.5,$colormap[$i]);
	}
	// Write file of image
	imagepng($image,$imgfilename,5);
}
