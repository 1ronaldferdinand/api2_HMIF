<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\DivisionModel;
use App\Models\PeriodModel;
use App\Models\StaffModel;
use App\Helpers\ApiFormatter;
use File;
use Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = StaffModel::select('*')->leftjoin('period', 'staff.period_id', '=', 'period.period_id')->get();
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
                    'member_id' => 'required',
                    'staff_level' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'member_id.required' => 'Member ID is required',
                    'staff_level.required' => 'Staff Level is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            if (StaffModel::where('member_id', $params['member_id'])->exists()) {
                $response = APIFormatter::createApi(400, 'member already exists');
                return response()->json($response);
            }

            $data = StaffModel::create([
                'period_id' => $params['period_id'],
                'member_id' => $params['member_id'],
                'staff_level' => $params['staff_level']
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
            $data = StaffModel::findorfail($id);
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
                    'member_id' => 'required',
                    'staff_level' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'member_id.required' => 'Member ID is required',
                    'staff_level.required' => 'Staff Level is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = StaffModel::findorfail($id);
            $data->update([
                'period_id' => $params['period_id'],
                'member_id' => $params['member_id'],
                'staff_level' => $params['staff_level'],
                'updated_at' => now()
            ]);

            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }

    public function destroy($id)
    {
        try {
            $data = StaffModel::findorfail($id);
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