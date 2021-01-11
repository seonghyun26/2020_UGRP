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
?>