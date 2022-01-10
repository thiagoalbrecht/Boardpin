<?php
function checkDuplicates($trimmed_json)
{
    $json = "[" . $trimmed_json . "]";
    $array = json_decode($json);
    $ids = array();

    foreach ($array as $key => $value) {
        array_push($ids, $array[$key]->Id);
    }
    $id_counts = array_count_values($ids);

    do { // Loop this while there are duplicate values in the array.
        $incremented_ids = array();
        foreach ($id_counts as $key => $value) {
            $count = $value;
            $this_id = $key + $count - 1;

            while ($count > 0) {
                array_push($incremented_ids, $this_id);
                $count -= 1;
                $this_id -= 1;
            }
        }
        $id_counts = (array_count_values($incremented_ids));
    } while (count($incremented_ids) !== count(array_unique($incremented_ids))); 

    $count = 0;
    foreach ($array as $key => $value) {
        $array[$key]->Id = $incremented_ids[$count];
        $count += 1;
    }
    $final_trimmed_json = trim(json_encode($array), "[]");


    return ($final_trimmed_json);
}
