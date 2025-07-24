<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Http\Requests\Banner\BannerRequest;
use App\Http\Repositories\Banner\BannerRepository;
use App\Exceptions\GeneralResponse;
use App\Exceptions\GeneralError;
use DataTables, DB, Log;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    protected $bannerRepo;

    public function __construct(BannerRepository $bannerRepo)
    {
        $this->bannerRepo = $bannerRepo;
    }

    public function index(Request $request)
    {
        return view('Admin.Banner.index');
    }

    public function getBannerList(Request $request)
    {
        try {
            $data = Banner::all();
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
                                        <a href="'.route('Banner.detail',$row->id).'"><i class="ri-eye-line" aria-hidden="true" style="color:black;"></i></a>
                                        <a href="'.route('Banner.edit',$row->id).'"><i class="ri-pencil-line" aria-hidden="true" style="color:black;"></i></a>
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
        return view('Admin.Banner.create');
    }

    public function store(BannerRequest $request)
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

            if($request->hasFile('background_image'))
            {
                $file = $request->file('background_image');
                $imagePath = UploadImage('Banner',$file);
                $data['background_image']=$imagePath;
            }

            $review = $this->bannerRepo->updateOrCreateData($id,$data);
            if($id == null){
                $msg = 'Banner created successfully';
            } else {
                $msg = 'Banner updated successfully';
            }
            toastr()->success($msg);
            DB::commit();
            return redirect()->route('Banner.index');
        }catch(\Exception $e){
            \Log::info('Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Something Went Wrong!']);
        }
    }

    public function edit($id,Request $request)
    {
        $bannerDetails = Banner::where('id',$id)->first();
        if(empty($bannerDetails)){
            abort(404);
        }
        return view('Admin.Banner.create', compact('bannerDetails'));
    }

    public function viewClientReviewDetails($id)
    {

        $bannerDetails = $this->bannerRepo->getById($id);

        return view('Admin.Banner.view',compact('bannerDetails'));
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = $request->all();
            $this->bannerRepo->delete($data['id']);
            DB::commit();
            return response()->json(['success' => true, 'message' => "Banner Deleted Successfully", 'status' => $request->status,'code'=>200]);
        } catch(\Exception $e){
            \Log::info('Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            DB::rollback();
            return array('status' => '0', 'msg_fail' => 'Something went wrong!');
        }
    }

    public function updateStatus(Request $request)
    {
        $banner = Banner::find($request->id);
        $msg = "Status Updated Successfully";

        $banner->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => $msg, 'status' => $request->status,'code'=>200]);
    }
}
