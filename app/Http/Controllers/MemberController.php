<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\MemberModel;
use App\Models\PeriodModel;
use App\Models\User;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\UserController;
use Auth;
use File;
use Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = MemberModel::select('*')->leftjoin('period', 'member.period_id', '=', 'period.period_id')->get();
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
                    'period_id' => 'required | exists:period,period_id',
                    'member_nim' => 'required | unique:member,member_nim',
                    'member_name' => 'required',
                    'member_status' => 'required | in:active,inactive,alumni',
                    'member_email' => 'required | email | unique:member,member_email',
                    'member_password' => 'required | min:8 | regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', // at least 1 lowercase, 1 uppercase, 1 number
                ], 
                [
                    'period_id.required' => 'Period ID is required.',
                    'period_id.exists' => 'The selected period is invalid.',
                    'member_nim.required' => 'NIM is required.',
                    'member_name.required' => 'Name is required.',
                    'member_status.required' => 'Status is required.',
                    'member_status.in' => 'The selected status is invalid.',
                    'member_email.required' => 'Email is required.',
                    'member_email.email' => 'Email is invalid.',
                    'member_password.required' => 'Password is required.',
                    'member_password.min' => 'Password must be at least 8 characters.',
                    'member_password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, and one number.'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Validation Error', $validator->errors()->all());
                return response()->json($response);
            }

            if (MemberModel::where('member_nim', $params['member_nim'])->exists()) {
                $response = APIFormatter::createApi(400, 'Member NIM already exists');
                return response()->json($response);
            }

            if ($request->hasFile('member_image')) {
                $file_dir = public_path('/files/member/');
                if (!File::exists($file_dir)) {
                    File::makeDirectory($file_dir, $mode = 0777, true, true);
                }

                $image = $request->file('member_image');
                $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->member_nim)));
                $image_name = "img_" . $slug . "_" . time() . "." . $image->getClientOriginalExtension();
                $image->move($file_dir, $image_name);
            } else {
                $image_name = NULL;
            }

            $password = Hash::make($params['member_password']);

            $data = MemberModel::create([
                'period_id' => $params['period_id'],
                'member_nim' => $params['member_nim'],
                'member_name' => $params['member_name'],
                'member_status' => $params['member_status'],
                'member_birthdate' => $params['member_birthdate'],
                'member_address' => $params['member_address'],
                'member_phone' => $params['member_phone'],
                'member_email' => $params['member_email'],
                'member_password' => $password,
                'member_image_url' => $image_name,
            ]);

            // AUTO REGISTER
            $user = User::create([
                'nim' => $data->member_nim,
                'member_id' => $data->member_id,
                'name' => $data->member_name,
                'email' => $data->member_email,
                'password' => $password,
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
            $data = MemberModel::findorfail($id)->leftjoin('period', 'member.period_id', '=', 'period.period_id')->first();
            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(400, $e->getMessage());
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
                    'period_id' => 'required | exists:period,period_id',
                    'member_nim' => 'required',
                    'member_name' => 'required',
                    'member_status' => 'required | in:active,inactive,alumni',
                    'member_email' => 'required | email',
                    'member_password' => 'required | min:8 | regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', // at least 1 lowercase, 1 uppercase, 1 number
                ],
                [
                    'period_id.required' => 'Period ID is required.',
                    'period_id.exists' => 'The selected period is invalid.',
                    'member_nim.required' => 'NIM is required.',
                    'member_name.required' => 'Name is required.',
                    'member_status.required' => 'Status is required.',
                    'member_status.in' => 'The selected status is invalid.',
                    'member_email.required' => 'Email is required.',
                    'member_email.email' => 'Email is invalid.',
                    'member_password.required' => 'Password is required.',
                    'member_password.min' => 'Password must be at least 8 characters.',
                    'member_password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, and one number.'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, $validator->errors()->all());
                return response()->json($response);
            }

            if ($request->hasFile('member_image')) {
                $file_dir = public_path('/files/member/');
                if (!File::exists($file_dir)) {
                    File::makeDirectory($file_dir, $mode = 0777, true, true);
                }

                $image = $request->file('member_image');
                $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->member_nim)));
                $image_name = "img_" . $slug . "_" . time() . "." . $image->getClientOriginalExtension();
                $image->move($file_dir, $image_name);
            } else {
                $image_name = NULL;
            }

            $data = MemberModel::where('member_id', $id)->first();

            if ($request->member_password != $data->member_password) {
                $password = Hash::make($params['member_password']);
            } else {
                $password = $data->member_password;
            }

            $data->period_id = $params['period_id'];
            $data->member_nim = $params['member_nim'];
            $data->member_name = $params['member_name'];
            $data->member_status = $params['member_status'];
            $data->member_birthdate = $params['member_birthdate'];
            $data->member_address = $params['member_address'];
            $data->member_phone = $params['member_phone'];
            $data->member_email = $params['member_email'];
            $data->member_password = $password;
            $data->member_image_url = $image_name;
            $data->updated_at = now();
            $data->save();

            // UPDATE AUTO REGISTER
            $user = User::where('nim', $data->member_nim)->first();
            if(empty($user)){
                $user = User::create([
                    'nim' => $data->member_nim,
                    'member_id' => $data->member_id,
                    'name' => $data->member_name,
                    'email' => $data->member_email,
                    'password' => $password,
                ]);
            } else {
                $user->member_nim = $data->member_nim;
                $user->member_id = $data->member_id;
                $user->name = $data->member_name;
                $user->email = $data->member_email;
                $user->password = $password;
                $user->save();
            };

            $response = APIFormatter::createApi(200, 'Success', $data);
            return response()->json($response);
        } catch (\Exception $e) {
            $response = APIFormatter::createApi(400, $e->getMessage());
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
            $data = MemberModel::findorfail($id);
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

    public function get_member() {
        $member = auth()->user();
        if(auth()->user()->member_id){
            $member = MemberModel::where('member_email', auth()->user()->email)->first();
        }
        return $member;
    }

    public function memberAuth() {
        $data = $this->get_member();
        
        if($data){
            $res = ['success'=> true, 'user'=> $data];
        } else {
            $res = ['success'=> false, 'user'=> null];
        }

        return response()->json($res, 200);
    }
}
