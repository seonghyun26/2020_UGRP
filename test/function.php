<?php
    function angle($dim, $vector_1, $vector_2){
        $dot = 0;
        for($i = 0 ; $i < $dim ; $i++){
            $dot += $vector_1[$i] * $vector_2[$i];
            $size_1 += pow($vector_1[$i],2);
            $size_2 += pow($vector_2[$i],2);
        }
        $size_1 = sqrt($size_1);
        $size_2 = sqrt($size_2);
        $theta = acos( $dot / ( $size_1 * $size_2) );
        return $theta;
    }
    function mat_mul($matrix, $vector){
        $result = array(0,0,0);
        for($i = 0 ; $i < 3; $i++){
            for($j = 0; $j < 3; $j++){
                $result[$i] += $matrix[$i][$j] * $vector[$j]; 
            }         
        }
        return $result;
    }

    $v1 = array(
        array(1, 2, 3),
        array(4, 5, 6),
        array(7, 8, 9)
    );
    $v2 = array(0, 0, 1);
    $v3 = array(1, 1, -sqrt(2));
    $a = angle(3, $v2, $v3);
    
    $z_vec = array(0,0,1);
    $xyz_avg = array(-0.03 , -0.88 , 0.44);

    if($xyz_avg[1] < 0) $sign = 1;
    else $sign = -1;
    if($xyz_avg[0] < 0) $seta_x = asin(($xyz_avg[1]) / sqrt(pow($xyz_avg[0],2) + pow($xyz_avg[1],2))) + M_PI*$sign;    
    else $seta_x = asin((-$xyz_avg[1]) / sqrt(pow($xyz_avg[0],2) + pow($xyz_avg[1],2)));
    $seta_z = angle(3, $xyz_avg, $z_vec);
    echo $seta_x.", ".$seta_z."<br>";

    $cos_x = cos($seta_x);
    $sin_x = sin($seta_x);
    $rot_mat_z = array(
        array($cos_x, -$sin_x, 0),
        array($sin_x, $cos_x, 0),
        array(0, 0, 1)
    );
    
    $cos_z = cos(-$seta_z);
    $sin_z = sin(-$seta_z);
    $rot_mat_y = array(
        array($cos_z, 0, $sin_z),
        array(0, 1, 0),
        array(-$sin_z, 0, $cos_z)
    );
    print_r($rot_mat_y);
    echo "<br>";

    echo "<br>Original Vector : ";
    print_r($xyz_avg);
    $temp = array(0,0,0);
    $temp = mat_mul($rot_mat_z, $xyz_avg);
    $temp[0] = round($temp[0] , 4);
    $temp[1] = round($temp[1] , 4);
    $temp[2] = round($temp[2] , 4);
    print_r($temp);

    $new_xyz = array(0 , 0, 0);
    $new_xyz = mat_mul($rot_mat_y, $temp);
    $new_xyz[0] = round($new_xyz[0] , 4);
    $new_xyz[1] = round($new_xyz[1] , 4);
    $new_xyz[2] = round($new_xyz[2] , 4);

    echo "<br>New Vector : ";    
    print_r($new_xyz);
    
    //echo "<br>".$a/3.14*180;
    //echo "<br>".$b;
?>