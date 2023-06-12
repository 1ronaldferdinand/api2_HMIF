<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManagementModel;
use App\Models\StructureModel;
use App\Models\DivisionModel;
use App\Models\StaffModel;
use App\Models\PeriodModel;
use App\Helpers\ApiFormatter;
use Validator; 

class ManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ManagementModel::select('*')
            ->leftjoin('structure', 'management.structure_id', '=', 'structure.structure_id')
            ->leftjoin('division', 'management.division_id', '=', 'division.division_id')
            ->leftjoin('staff', 'management.staff_id', '=', 'staff.staff_id')
            ->get();
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
        try {
            $params = $request->all();

            $validator = Validator::make($params, 
                [
                    'structure_id' => 'required',
                    'division_id' => 'required',
                    'staff_id' => 'required',
                ],
                [
                    'structure_id.required' => 'Structure ID is required',
                    'division_id.required' => 'Division ID is required',
                    'staff_id.required' => 'Staff ID is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = ManagementModel::create([
                'structure_id' => $params['structure_id'],
                'division_id' => $params['division_id'],
                'staff_id' => $params['staff_id']
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
            $data = ManagementModel::findorfail($id);
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
                    'structure_id' => 'required',
                    'division_id' => 'required',
                    'staff_id' => 'required',
                ],
                [
                    'structure_id.required' => 'Structure ID is required',
                    'division_id.required' => 'Division ID is required',
                    'staff_id.required' => 'Staff ID is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = ManagementModel::findorfail($id);
            $data->update([
                'structure_id' => $params['structure_id'],
                'division_id' => $params['division_id'],
                'staff_id' => $params['staff_id'],
                'updated_at' => now()
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
            $data = ManagementModel::findorfail($id);
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
