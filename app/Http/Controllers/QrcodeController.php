<?php

namespace App\Http\Controllers;

use App\Models\Qrcode;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class QrcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $customMessages = [
        'required' => 'Please input the :attribute.',
        'unique' => 'This :attribute has already been taken.',
        'max' => ':Attribute may not be more than :max characters.',
    ];
    public function index()
    {
       
        
        if (request()->ajax()) {
            return datatables()->of(Qrcode::all())
            ->addColumn('action', 'admin.settings.action_qr')
            ->addColumn('qr', 'admin.settings.qrcode')
            ->rawColumns(['action','qr'])
            ->addIndexColumn()
            ->make(true);
        }

        
        return view('admin.qr.qrs');
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
        request()->validate([
            'name' => 'required|string',
            // 'from_date'      => 'required',
            // 'to_date'     => 'required',
            // 'notes'     => 'required|string',

        ], $this->customMessages);
        
       $data = new Qrcode();
       $data->name = $request->name;
       $data->file_url = $request->file_url;
       if($request->file('file')){
            $uploadedFileUrl = Cloudinary ::upload($request->file('file')->getRealPath(),[
                'folder'=>'file'
            ])->getSecurePath();
            $data->file= $uploadedFileUrl;
       }

       $query = $data->save(); 
        return response()->json([
            'code'=>0,
            'message'=> "Data have been successfully added!"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
