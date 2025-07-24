<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Http\Requests\Blog\BlogRequest;
use App\Http\Repositories\Blog\BlogRepository;
use App\Exceptions\GeneralResponse;
use App\Exceptions\GeneralError;
use DataTables, DB, Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BlogController extends Controller
{
    protected $blogRepo;

    public function __construct(BlogRepository $blogRepo)
    {
        $this->blogRepo = $blogRepo;
    }

    public function index(Request $request)
    {
        return view('Admin.Blog.index');
    }

    public function getBlogList(Request $request)
    {
        try {
            $data = Blog::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="manage_button_popup">
                                        <a href="'.route('Blog.detail',$row->id).'"><i class="ri-eye-line" aria-hidden="true" style="color:black;"></i></a>
                                        <a href="'.route('Blog.edit',$row->id).'"><i class="ri-pencil-line" aria-hidden="true" style="color:black;"></i></a>
                                        <a href="javascript:void(0);" id="'.$row->id.'" class="delete"><i class="ri-delete-bin-6-fill" style="color:red;"></i></a>
                                    </div>
                                </div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function create(Request $request)
    {
        return view('Admin.Blog.create');
    }

    public function store(BlogRequest $request)
    {
        $data = $request->all();
        $image = $request->image;
        DB::beginTransaction();
        try{
            $id = $data['id'] ?? null;
            if($request->hasFile('hero_image'))
            {
                $file = $request->file('hero_image');
                $imagePath = UploadImage('Blog',$file);
                $data['hero_image']=$imagePath;
            }

            if($request->hasFile('image'))
            {
                $file = $request->file('image');
                $imagePath = UploadImage('Blog',$file);
                $data['image']=$imagePath;
            }

            if($request->hasFile('banner_image'))
            {
                $file = $request->file('banner_image');
                $imagePath = UploadImage('Blog',$file);
                $data['banner_image']=$imagePath;
            }

            $data['date'] = Carbon::now()->format('d-m-Y');


            $blog = $this->blogRepo->updateOrCreateData($id,$data);

            if($id == null){
                $msg = 'Blog created successfully';
            } else {
                $msg = 'Blog updated successfully';
            }
            toastr()->success($msg);
            DB::commit();
            return redirect()->route('Blog.index');
        }catch(\Exception $e){
            \Log::info('Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Something Went Wrong!']);
        }
    }

    public function edit($id,Request $request)
    {
        $blogDetails = Blog::where('id',$id)->first();
        if(empty($blogDetails)){
            abort(404);
        }
        return view('Admin.Blog.create', compact('blogDetails'));
    }

    public function viewBlogDetails($id)
    {

        $blogDetails = $this->blogRepo->getById($id);

        return view('Admin.Blog.view',compact('blogDetails'));
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = $request->all();
            $this->blogRepo->delete($data['id']);
            DB::commit();
            return response()->json(['success' => true, 'message' => "Blog Deleted Successfully", 'status' => $request->status,'code'=>200]);
        } catch(\Exception $e){
            \Log::info('Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            DB::rollback();
            return array('status' => '0', 'msg_fail' => 'Something went wrong!');
        }
    }
}
