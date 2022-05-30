<?php
namespace common\components\helpers;

class HtmlHelper{
	/**
     * Creates an array for the dropdown list
     * @param object $id
     * @param string $key
     * @param string $value
     * @return Array
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function createDropdownListArray($model, $key, $value)
    {
        if($model){
            $array = [];
            foreach($model as $var){
                $array[$var->$key] = $var->$value;
            }
            return $array;
        }
    }

    /*
    Creates an array to help in the checkboxes
    */
    public function createListArray($model, $value)
    {
        if($model){
            $array = [];
            foreach($model as $var){
                array_push($array, $var->$value);
            }
            return $array;
        }
    }


    /*
    Creates an array to help in the queries
    */
    public function createQueryArray($model)
    {
        if($model){
            $array = "(";
            foreach($model as $var){
                $array.=$var.",";

            }
            return trim($array,',').")";
        }
    }


    public function createDropdownListOptions($model, $key, $value, $selected=""){
        if($model){
            $options = "";
            foreach($model as $var){
                $options.="<option value='".$var->$key."' ".($selected==$var->$key ? "selected" : " ")." >".$var->$value."</option>";
            }
            return $options;
        }
    }

    public function createDropdownListOptions2($model, $key, $value, $withnull=false){
        if($model){
            $options = $withnull ? "<option></option>" : "";
            foreach($model as $var){
                $options.="<option value='".$var->$key."'  >".$var->$value."</option>";
            }
            return $options;
        }
    }

    public function time_elapsed_string($datetime, $full = false) {
        $now = new \DateTime;
        $ago = new \DateTime($datetime.' 23:59:59');
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) : 'today';
    }
}


