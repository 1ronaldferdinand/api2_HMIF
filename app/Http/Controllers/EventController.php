<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ProgramModel;
use App\Models\EventModel;
use App\Models\StaffModel;
use App\Helpers\ApiFormatter;
USE File;
use Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=  EventModel::select('*')->leftjoin('program', 'event.program_id', '=', 'program.program_id')->leftjoin('staff', 'staff.staff_id','=','event.staff_id')->get();
        $response = APIFormatter:: createApi(200,'succes',$data);
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

            $validator = Validator::make($params, [
                'program_id' => 'required',
                'staff_id' => 'required',
                'event_name' => 'required',
                'event_date' => 'required',
            ],
            [
                'program_id.required' => 'Program ID is required',
                'staff_id.required' => 'Staff ID is required',
                'event_name.required' => 'Event Name is required',
                'event_date.required' => 'Event Date is required',
            ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = EventModel::create([
                'program_id' => $params['program_id'],
                'staff_id' => $params['staff_id'],
                'event_name' => $params['event_name'],
                'event_date' => $params['event_date'],
                'event_status' => $params['event_status']
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
            $data = EventModel::findorfail($id);
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

            $validator = Validator::make($params, [
                'program_id' => 'required',
                'staff_id' => 'required',
                'event_name' => 'required',
                'event_date' => 'required',
            ],
            [
                'program_id.required' => 'Program ID is required',
                'staff_id.required' => 'Staff ID is required',
                'event_name.required' => 'Event Name is required',
                'event_date.required' => 'Event Date is required',
            ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            $data = EventModel::findorfail($id);
            $data->update([
                'program_id' => $params['program_id'],
                'staff_id' => $params['staff_id'],
                'event_name' => $params['event_name'],
                'event_date' => $params['event_date'],
                'event_status' => $params['event_status'],
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
            $data = EventModel::findorfail($id);
            $data->ProgramModel()->delete();
            $data->staff()->delete();


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
