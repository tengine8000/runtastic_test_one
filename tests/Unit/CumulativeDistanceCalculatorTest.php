<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Services\CumulativeDistanceCalculator;

class CumulativeDistanceCalculatorTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_given_a_distance_returns_new_distance_greater_than_given_value()
    {
        $old_value = 5;
        $cdc = new CumulativeDistanceCalculator($old_value);
        $this->assertTrue($cdc->generate() > $old_value);
    }
}
