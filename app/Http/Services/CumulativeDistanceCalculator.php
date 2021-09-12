<?php

namespace App\Http\Services;

class CumulativeDistanceCalculator

{
    private $starting_value;

    public function __construct($value = 0){
        $this->starting_value = $value;
    }

    public function generate() {
        $this->starting_value += 5;
        return $this->starting_value;
    }
}