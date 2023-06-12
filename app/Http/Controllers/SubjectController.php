<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SubjectModel;
use App\Helpers\ApiFormatter;
use File;
use Validator;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = SubjectModel::select('*')->get();
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
       
                    'subject_name' => 'required',
                    'subject_code' => 'required',
                ],
                [
                    
                    'subject_name.required' => 'subject name is required',
                    'subject_code.required' => 'subject code is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = SubjectModel::create([
                'subject_code' => $params['subject_code'],
                'subject_name' => $params['subject_name'],
                
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
            $data = SubjectModel::findorfail($id);
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
                    
                    'subject_name' => 'required',
                    'subject_code' => 'required',
                ],
                [
                    
                    'subject_name.required' => 'subject name is required',
                    'subject_code.required' => 'subject code is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            $data = SubjectModel::findorfail($id);
            $data->update([
                'subject_code' => $params['subject_code'],
                'subject_name' => $params['subject_name'],
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
            $data = SubjectModel::findorfail($id);
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
