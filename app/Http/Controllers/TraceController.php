<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Trace;
use App\Models\GPSPoint;
use Illuminate\Support\Carbon;

class TraceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->isJson()) {
            $data = ['data' => $request->input()];

            if(count($request->all()) == 0) {
                return response()->json(
                ['error' => 'No Trace data given!'], 400);
            }

            $validator = Validator::make($data, [
                'data.*.latitude' => 'required|numeric|min:-180|max:180',
                'data.*.longitude' => 'required|numeric|min:-180|max:180'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Trace data invalid!',
                    'message' => $data,
                ], 422);
            }else{
                $validated = $validator->validated();

                $trace = Trace::create([
                    'created_at' => Carbon::now()
                ]);
                $trace->save();

                foreach ($validated['data'] as $gps_point) {
                    GPSPoint::create([
                        'latitude'=>$gps_point['latitude'],
                        'longitude'=>$gps_point['longitude'],
                        'trace_id'=>$trace->id,
                    ])->save();
                }
    
    
                return response()->json([
                    'success' => 'Trace data created successfully',
                    'trace_id' => $trace->id
                ], 201);
            }
            
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

        return response()->json([
            'success' => 'Trace Data',
            'data' => $trace->gps_points
        ]);
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
        if ($request->isJson()) {
            $data = ['data' => $request->input()];

            $trace = Trace::where('id', $id)->first();
            if(!$trace) return response()->json(['error' => 'Invalid Trace ID!'], 404);

            if(count($request->all()) == 0) {
                return response()->json(
                ['error' => 'No Trace data given!'], 400);
            }

            $validator = Validator::make($data, [
                'data.*.latitude' => 'required|numeric|min:-180|max:180',
                'data.*.longitude' => 'required|numeric|min:-180|max:180'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Trace data invalid!',
                    'message' => $data,
                ], 422);
            }else{
                $validated = $validator->validated();

                GPSPoint::where('trace_id', $trace->id)->delete();

                foreach ($validated['data'] as $gps_point) {
                    GPSPoint::create([
                        'latitude'=>$gps_point['latitude'],
                        'longitude'=>$gps_point['longitude'],
                        'trace_id'=>$trace->id,
                    ])->save();
                }
    
    
                return response()->json([
                    'success' => 'Trace data updated successfully',
                    'trace_id' => $trace->id
                ], 201);
            }
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
