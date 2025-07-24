<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use DataTables, DB, Log;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Exceptions\GeneralError;
use App\Exceptions\GeneralResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Repositories\Product\ProductRepository;
use App\Http\Repositories\Product\ProductGalleryRepository;
use App\Http\Requests\Product\ProductRequest;

class ProductController extends Controller
{
    protected $productRepo,$productGalleryRepo;

    public function __construct(ProductRepository $productRepo,ProductGalleryRepository $productGalleryRepo)
    {
        $this->productRepo = $productRepo;
        $this->productGalleryRepo = $productGalleryRepo; 
    }

    public function index(Request $request)
    {
        return view('Admin.Product.index');
    }

    public function getProjectList(Request $request)
    {
        try {
            $data = Product::with('productCategory')->get();
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
                                        <a href="'.route('Product.edit',$row->id).'"><i class="ri-pencil-line" aria-hidden="true"></i></a>
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
        $categories = ProductCategory::all(); // Fetch all categories from the database
        return view('Admin.Product.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $image = $request->image;
        DB::beginTransaction();
        try{
            $id = $data['id'] ?? null;

            if($request->hasFile('banner_image'))
            {
                $file = $request->file('banner_image');
                $imagePath = UploadImage('ProductBanner',$file);
                $data['banner_image']=$imagePath;
            }

            $productDetails = $this->productRepo->updateOrCreateData($id,$data);
            
            if($request->hasFile('image'))
            {
                foreach($request->file('image') as $files)
                {
                    $imagePath = uploadImage('ProjectGallery',$files);
                    $image = $imagePath;
                    $projectGalleryUpload = [
                        'product_id' => $productDetails->id,
                        'image' => $image,
                    ];
                    $this->productGalleryRepo->updateOrCreateData($id,$projectGalleryUpload);
                }
            }
            if($id == null){
                $msg = 'Product created successfully';
            } else {
                $msg = 'Product updated successfully';
            }
            toastr()->success($msg);
            DB::commit();
            return redirect()->route('Product.index');
        }catch(\Exception $e){
            \Log::info('Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Something Went Wrong!']);
        }
    }

    public function edit($id,Request $request)
    {
        $productDetails = Product::where('id',$id)->with('productCategory','productGallery')->first();
        if(empty($productDetails)){
            abort(404);
        }
        $categories = ProductCategory::all();
        return view('Admin.Product.create', compact('productDetails','categories'));
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = $request->all();
            $this->productRepo->delete($data['id']);
            DB::commit();
            return response()->json(['success' => true, 'message' => "Product Deleted Successfully", 'status' => $request->status,'code'=>200]);
        } catch(\Exception $e){
            \Log::info('Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            DB::rollback();
            return array('status' => '0', 'msg_fail' => 'Something went wrong!');
        }
    }

    public function updateStatus(Request $request)
    {
        $category = Product::find($request->id);
        $msg = "Status Updated Successfully";

        $category->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => $msg, 'status' => $request->status,'code'=>200]);
    }

    public function DeleteOneImage(Request $request)
    {
        $imageId = $request->input('imageId');
        $image = \App\Models\ProductGallery::find($imageId);
        if ($image) {
            $img = public_path('storage/'.$image->image);
            if(File::exists($img)) {
                File::delete($img);
            }
            $image->delete($image->id);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Image not found']);
    }
}
