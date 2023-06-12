<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StructureModel;
use App\Models\PeriodModel;
use App\Helpers\ApiFormatter;
use Validator;

class StructureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = StructureModel::select('*') ->leftjoin ('period','structure.period_id','=','period.period_id') -> get();
        $response = APIFormatter:: createApi(200,'Success',$data);
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
            $params =$request->all();
            $validator = Validator::make($params, 
                [
                    'period_id' => 'required',
                    'structure_name' => 'required',
                    'structure_level' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'structure_name.required' => 'Structure name is required',
                    'structure_level.required' => 'Structure level is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400,'Validation Error',$validator->errors()->all());
                return response()->json($response);
            }
            
            if (StructureModel::where ('structure_name',$params['structure_name'])-> exists()) {
                $response = APIFormatter::createApi(400,'structure name already exists');
                return response()->json($response);
            }

            $data = StructureModel::create([
                 //ini nnti dihapus 
                'period_id'=> $params['period_id'],
                'structure_name' => $params['structure_name'],
                'structure_level'=> $params['structure_level']
            ]);

            $response = APIFormatter::createApi(200,'Success',$data);
            return response()->json($response);
        } catch (Exception $e) {
            $response =APIFormatter::createApi(500,'Internal Server Error', $e->getMessage() );
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
            $data = StructureModel::findorfail($id);
            $response = APIFormatter::createAPI(200,'succes',$data);
            return response()->json($response);
        } catch (Exception $e) {
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
                    'structure_name' => 'required',
                    'structure_level' => 'required',
                ],
                [
                    'period_id.required' => 'Period ID is required',
                    'structure_name.required' => 'Structure name is required',
                    'structure_level.required' => 'Structure level is required',
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400,'Validation Error',$validator->errors()->all());
                return response()->json($response);
            }

            $data = StructureModel::findorfail($id);
            
            $data->update([
                'period_id' => $params['period_id'],
                'structure_name' => $params['structure_name'],
                'structure_level' => $params['structure_level'],
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
        try 
        { 
            $data = StructureModel::findorfail($id);
            $data->delete();
            $response = APIFormatter::createAPI(200,'succes');
            return response()->json($response);
        } catch (\Illuminate\Database\QueryException $e) {
            if  ($e->getCode() == "23000") {
                $response = APIFormatter::createApi(400, 'Cannot delete this data because it is used in another table');
                return response()->json($response);
            }
        } catch (Exception $e) {
            $response = APIFormatter::createApi(500, 'Internal Server Error', $e->getMessage());
            return response()->json($response);
        }
    }
}
