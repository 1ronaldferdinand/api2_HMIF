<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\PeriodModel;
use App\Models\PartnershipModel;
use App\Models\ManagementModel;
use App\Helpers\ApiFormatter;
use File;
use Validator;

class PartnershipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PartnershipModel::select('*')->get();
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
                    'period_id' => 'required',
                    'management_id' => 'required',
                    'partnership_name' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'management_id.required' => 'Management ID is required',
                    'partnership_id.required' => 'Partnership ID is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = PartnershipModel::create([
                'period_id' => $params['period_id'],
                'management_id' => $params['management_id'],
                'partnership_name' => $params['partnership_name'],
                'partnership_phone' => $params['partnership_phone'],
                'partnership_email' => $params['partnership_email']
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
            $data = PartnershipModel::findorfail($id);
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
                    'management_id' => 'required',
                    'partnership_name' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'management_id.required' => 'Management ID is required',
                    'partnership_id.required' => 'Partnership ID is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = PartnershipModel::findorfail($id);
            $data->update([
                'period_id' => $params['period_id'],
                'management_id' => $params['management_id'],
                'partnership_name' => $params['partnership_name'],
                'partnership_phone' => $params['partnership_phone'],
                'partnership_email' => $params['partnership_email'],
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
            $data = PartnershipModel::findorfail($id);
            $data->delete();
            $response = APIFormatter::createApi(200, 'Succes');
            return response()->json($response);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                $response = APIFormatter::createApi(400, 'Cannot delete this data because it is used in another table');
                return response()->json($response);
            }
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }
}
