<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Repositories\Product\ProjectCategoryRepository;
use App\Exceptions\GeneralResponse;
use App\Exceptions\GeneralError;
use DataTables, DB, Log;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    protected $projectCategoryRepo;

    public function __construct(ProjectCategoryRepository $projectCategoryRepo)
    {
        $this->projectCategoryRepo = $projectCategoryRepo;
    }

    public function index(Request $request)
    {
        return view('Admin.Category.index');
    }

    public function getProjectCategoryList(Request $request)
    {
        try {
            $data = ProductCategory::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $class = 'rounded-pill  bg-label-danger';
                    $title = 'Activate';
                    $status = 'ACTIVE';
                    if($row->status == "ACTIVE"){
                        $class = "rounded-pill bg-label-success";
                        $title = 'InActivate';
                        $status = 'INACTIVE';
                    }

                    return '<div class="flex space-x-2">
                                <a href="javascript:;"><span class="statusUpdate user-status text-xs inline-block py-1 px-4 leading-none text-center whitespace-nowrap align-baseline  '.$class.' rounded-full" title="'.$title.'" data-id='.$row->id.' status="'.$status.'" current="'.$row->status.'">'.$row->status.'</span></a>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="manage_button_popup">
                                        <a href="'.route('Category.edit',$row->id).'"><i class="ri-pencil-line" aria-hidden="true"></i></a>
                                        <a href="javascript:void(0);" id="'.$row->id.'" class="delete"><i class="ri-delete-bin-6-fill" style="color:red;"></i></a>
                                    </div>
                                </div>';
                    return $actionBtn;
                })
                ->rawColumns(['status','action'])
                ->make(true);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function create(Request $request)
    {
        return view('Admin.Category.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $image = $request->image;
        DB::beginTransaction();
        try{
            $id = $data['id'] ?? null;

            if($request->hasFile('image'))
            {
                $file = $request->file('image');
                $imagePath = UploadImage('Banner',$file);
                $data['image']=$imagePath;
            }

            $projectCategory = $this->projectCategoryRepo->updateOrCreateData($id,$data);
            if($id == null){
                $msg = 'Product Category created successfully';
            } else {
                $msg = 'Product Category updated successfully';
            }
            toastr()->success($msg);
            DB::commit();
            return redirect()->route('Category.index');
        }catch(\Exception $e){
            \Log::info('Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Something Went Wrong!']);
        }
    }

    public function edit($id,Request $request)
    {
        $categoryDetails = ProductCategory::where('id',$id)->first();
        if(empty($categoryDetails)){
            abort(404);
        }
        return view('Admin.Category.create', compact('categoryDetails'));
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = $request->all();
            $this->projectCategoryRepo->delete($data['id']);
            DB::commit();
            return response()->json(['success' => true, 'message' => "Product Category Deleted Successfully", 'status' => $request->status,'code'=>200]);
        } catch(\Exception $e){
            \Log::info('Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            DB::rollback();
            return array('status' => '0', 'msg_fail' => 'Something went wrong!');
        }
    }

    public function updateStatus(Request $request)
    {
        $category = ProductCategory::find($request->id);
        $msg = "Status Updated Successfully";

        $category->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => $msg, 'status' => $request->status,'code'=>200]);
    }
}
