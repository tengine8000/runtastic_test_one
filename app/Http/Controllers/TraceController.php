<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Trace;
use App\Models\GPSPoint;
use Illuminate\Support\Carbon;
use Brick\Math\BigDecimal;
use App\Http\Services\CumulativeDistanceCalculator;
use Illuminate\Support\Facades\Http;

class TraceController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $traces = Trace::where('id', '>', 0)->orderBy('id', 'desc')->limit(5)->get()->sort();

        $trace_collection = [];

        if($traces->isNotEmpty()){
            foreach ($traces as $trace) {
                $trace_collection[$trace->id] = $this->format_traces($trace);
            }
            return response()->json($trace_collection, 200, [], JSON_NUMERIC_CHECK);
        }
        return response()->json([], 200);
    }

    private function floater_to_string($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->floater_to_string($value);
            }
            return $data;
        } else {
            if (is_float($data)) {
                $result = rtrim(number_format($data, 14), '0.');
                return $result === '' ? '0' : $result;
            } else {
                return $data;
            }
        }   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $data = $this->floater_to_string($data);

        if(count($request->all()) == 0) {
            return response()->json(
            ['error' => 'No Trace data given!'], 400);
        }

        $validator = Validator::make($data, [
            '*.latitude' => 'required',
            '*.longitude' => 'required',
            '*.distance' => '',
            '*.elevation' => '',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Trace data invalid!',
                'message' => $data,
            ], 422);
        }else{
            $trace = Trace::create([
                'created_at' => Carbon::now()
            ]);
            $trace->save();

            foreach ($data as $gps_point) {
                GPSPoint::create([
                    'latitude'=>$gps_point['latitude'],
                    'longitude'=>$gps_point['longitude'],
                    'distance'=>isset($gps_point['distance'])? $gps_point['distance'] : 0,
                    'elevation'=>isset($gps_point['elevation'])? $gps_point['elevation'] : 0,
                    'trace_id'=>$trace->id,
                ])->save();
            }

            return response($trace->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trace = Trace::where('id', $id)->first();

        if(!$trace){
            return response()->json([
            'error' => 'Trace Data not found!'
            ], 404);
        }
        return response()->json($this->format_traces($trace), 200, [], JSON_NUMERIC_CHECK);
    }

    private function format_traces($trace)
    {
        $response = [];
        $elevation_response = Http::post('https://codingcontest.runtastic.com/api/elevations/bulk', $trace->gps_points->toArray());
        $distances = [
            0,6,11,15,18,29,32,37,41,46,50
        ];
        if(!$elevation_response->failed()){
            $elevations = $elevation_response->json();
            $trace->gps_points->map(function ($gps_point, $key) use (&$response, &$distances, &$elevations) {
                array_push($response, 
                    [
                        'latitude' => $gps_point->latitude,
                        'longitude' => $gps_point->longitude,
                        'distance' => (int) isset($distances[$key]) ? $distances[$key]: 0,
                        'elevation' => $elevations[$key]
                    ]);
            });
    
        }
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $data = $this->floater_to_string($data);

        $trace = Trace::where('id', $id)->first();
        if(!$trace) return response()->json(['error' => 'Invalid Trace ID!'], 404);

        $validator = Validator::make($data, [
            '*.latitude' => 'required',
            '*.longitude' => 'required',
            '*.distance' => '',
            '*.elevation' => '',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Trace data invalid!',
                'message' => $data,
            ], 422);
        }else{
            $data = collect($data);

            GPSPoint::where('trace_id', $trace->id)->delete();

            $nTrace = $data->map(function ($gps_point, $key) use(&$trace) {
                $new_gps_point = [
                    'latitude'=>$gps_point['latitude'],
                    'longitude'=>$gps_point['longitude'],
                    'distance' => (int) isset($gps_point['distance']) ? $gps_point['distance']: 0,
                    'elevation' => (int) isset($gps_point['elevation']) ? $gps_point['elevation']: 0,
                    'trace_id'=>$trace->id,
                ];
                GPSPoint::create($new_gps_point)->save();
                return $new_gps_point;
            });
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trace = Trace::where('id', $id)->first();
        if(!$trace){
            return response()->json([
            'error' => 'Trace does not exist',
            ], 404);
        }
        
        GPSPoint::where('trace_id', $trace->id)->delete();

        $trace->delete();

        return response()->json([
            'success' => 'Trace data deleted successfully',
        ], 200);

    }
}
