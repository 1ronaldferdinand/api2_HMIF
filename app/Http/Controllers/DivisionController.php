<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\DivisionModel;
use App\Models\PeriodModel;
use App\Helpers\ApiFormatter;
use File;
use Validator;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = DivisionModel::select('*')->leftjoin('period', 'division.period_id', '=', 'period.period_id');
        $data = $query->get();
        
        if($request->period_active){
            $data = $query->where('period_status', '1')->get();
        }
        
        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params, 
                [
                    'period_id' => 'required',
                    'division_name' => 'required',
                    'division_description' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'division_name.required' => 'Division Name is required',
                    'division_description.required' => 'Division Description is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = DivisionModel::create([
                'period_id' => $params['period_id'],
                'division_name' => $params['division_name'],
                'division_icon' => $params['division_icon'],
                'division_description' => $params['division_description'],
                'division_program' => $params['division_program'],
                'division_agenda' => $params['division_agenda'],
            ]);

            $response = APIFormatter::createApi(200, 'success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(400, $e->getMessage());
            return response()->json($response);
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
        try {
            $data = DivisionModel::findorfail($id);
            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        try {
            $params = $request->all();

            $validator = Validator::make($params, 
                [
                    'period_id' => 'required',
                    'division_name' => 'required',
                    'division_description' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'division_name.required' => 'Division Name is required',
                    'division_description.required' => 'Division Description is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = DivisionModel::findorfail($id);
            $data->update([
                'period_id' => $params['period_id'],
                'division_name' => $params['division_name'],
                'division_icon' => $params['division_icon'],
                'division_description' => $params['division_description'],
                'division_program' => $params['division_program'],
                'division_agenda' => $params['division_agenda'],
                'updated_at' => now(),
            ]);

            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
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
        try {
            $data = DivisionModel::findorfail($id);
            $data->delete();

            $response = APIFormatter::createApi(200, 'Success');
            return response()->json($response);
        } catch (\Illuminate\Database\QueryException $e) {
            if  ($e->getCode() == "23000") {
                $response = APIFormatter::createApi(400, 'Cannot delete this data because it is used in another table');
                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }
}
