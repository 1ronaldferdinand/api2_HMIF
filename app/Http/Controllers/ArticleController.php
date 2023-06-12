<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArticleModel;
use App\Models\StaffModel;
use App\Models\CategoryModel;
use App\Helpers\ApiFormatter;
use File;
use Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ArticleModel::select('article.*', 'category.category_name', 'category.category_name', 'staff.period_id', 'staff.member_id', 'staff.staff_level')
            ->leftjoin('category', 'article.category_id', '=', 'category.category_id')
            ->leftjoin('staff', 'article.staff_id', '=', 'staff.staff_id')
            ->orderBy('article.created_at', 'desc')
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
                    'staff_id' => 'required',
                    'category_id' => 'required',
                    'article_title' => 'required'
                ],
                [
                    'staff_id.required' => 'Staff id is required',
                    'category_id.required' => 'Category id is required',
                    'artice_title.required' => 'Article title is required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }

            if ($request->hasFile('article_image')) {
                $file_dir = public_path('/files/article/');
                if (!File::exists($file_dir)) {
                    File::makeDirectory($file_dir, $mode = 0777, true, true);
                }

                $image = $request->file('article_image');
                $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));
                $image_name = "img_" . $slug . "_" . time() . "." . $image->getClientOriginalExtension();
                $image->move($file_dir, $image_name);

                $host = env('APP_URL');

                $image_name = $host. '/public/files/article/' . $image_name;
            } else {
                $image_name = NULL;
            }

            $article_slugs = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));

            $data = ArticleModel::create([
                'staff_id' => $params['staff_id'],
                'category_id' => $params['category_id'],
                'article_title' => $params['article_title'],
                'article_content' => $params['article_content'],
                'article_image' => $image_name,
                'article_slug' => $article_slugs,
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
            $data = ArticleModel::findorfail($id);
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
                    'staff_id' => 'required',
                    'category_id' => 'required',
                    'article_title' => 'required'
                ],
                [
                    'staff_id.required' => 'Staff id is required',
                    'category_id.required' => 'Category id is required',
                    'artice_title.required' => 'Article title is required'
                ]
            );

            if ($validator->fails()) {
                $response = APIFormatter::createApi(400, 'Bad Request', $validator->errors()->all());
                return response()->json($response);
            }    
            
            if ($request->hasFile('article_image')) {
                $file_dir = public_path('/files/article/');
                if (!File::exists($file_dir)) {
                    File::makeDirectory($file_dir, $mode = 0777, true, true);
                }

                $image = $request->file('article_image');
                $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));
                $image_name = "img_" . $slug . "_" . time() . "." . $image->getClientOriginalExtension();
                $image->move($file_dir, $image_name);

                $host = env('APP_URL');

                $image_name = $host. '/public/files/article/' . $image_name;
            } else {
                $image_name = NULL;
            }

            $article_slugs = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace('.', '', strtolower($request->article_title)));

            $data = ArticleModel::where('article_id', $id)->first();

            $data->staff_id = $params['staff_id'];
            $data->category_id = $params['category_id'];
            $data->article_title = $params['article_title'];
            $data->article_content = $params['article_content'];
            $data->article_image = $image_name;
            $data->article_slug = $article_slugs;
            $data->updated_at = now();
            $data->save();

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
            $data = ArticleModel::findorfail($id);
            $data->delete();

            $response = APIFormatter::createApi(200, 'Success');
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

    public function getArticles()
    {
        $data = ArticleModel::select('article.*')
            ->leftJoin('category', 'article.category_id', '=', 'category.category_id')
            ->leftJoin('staff', 'article.staff_id', '=', 'staff.staff_id')
            ->leftJoin('member', 'staff.member_id', '=', 'member.member_id')
            ->leftJoin('period', 'staff.period_id', '=', 'period.period_id')
            ->where('category.category_name', '=', 'article') // Filter data only article
            ->orderBy('article.created_at', 'desc') // Order data by the newest
            ->with('category', 'staff.member', 'staff.period') // Append the "category", "member", and "period" objects
            ->get();
        
        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }

    public function getNews()
    {
        $data = ArticleModel::select('article.*')
            ->leftJoin('category', 'article.category_id', '=', 'category.category_id')
            ->leftJoin('staff', 'article.staff_id', '=', 'staff.staff_id')
            ->leftJoin('member', 'staff.member_id', '=', 'member.member_id')
            ->leftJoin('period', 'staff.period_id', '=', 'period.period_id')
            ->where('category.category_name', '=', 'news') // Filter data only news
            ->orderBy('article.created_at', 'desc') // Order data by the newest
            ->with('category', 'staff.member', 'staff.period') // Append the "category", "member", and "period" objects
            ->get();

        $response = APIFormatter::createApi(200, 'Success', $data);
        return response()->json($response);
    }
}
