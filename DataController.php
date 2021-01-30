<?php

namespace App\Http\Controllers;

use App\Dokumen;
use App\Karyawan;
use App\Klasifikasi;
use App\TypeKlasifikasi;
use App\UnitKerja;

use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use League\CommonMark\Block\Element\Document;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function search(Request $request)
    {
        $batas = 10;
        // $jumlah = Event::count();
        $cari = $request->cari;
        // $data = Event::where('e_name', 'like', "%" . $cari . "%");
        $data = Dokumen::where('NamaDokumen', 'like', "%" . $cari . "%")->orderBy('NamaDokumen', 'desc')->paginate($batas);
        $no = $batas * ($data->currentPage() - 1);
        // dd($request);
        // return view('welcome');
        // return dd($request);
        return view('cari', ['data' => $data], ['no' => $no])->with('status', 'Ditemukan ' . count($data) . ' Dokumen');
        // echo $request;
    }

    public function index()
    {
        //
        $batas = 10;
        $datas = Dokumen::count();
        $data = Dokumen::orderBy('IdDokumen', 'desc')->paginate($batas);;
        $no = $batas * ($data->currentPage() - 1);
        return view('cari', compact('data', 'no'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $karyawan = Karyawan::all();
        $klasifikasi = Klasifikasi::all();
        $tipe = TypeKlasifikasi::all();
        $unit = UnitKerja::all();
        return view('dokumen', compact('karyawan', 'klasifikasi', 'tipe', 'unit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // dd($request);
        $request->validate(
            [
                'NamaDokumen' => 'required',
                'id_KdUnitKerja' => 'required',
                'id_KdKlasifikasi' => 'required',
                'id_KdTypeKlasifikasi' => 'required',
                'TanggalQA' => 'required|before:today',
                'PenanggungJawab' => 'required',
                'LampiranDokumen' => 'required',
            ],
            [
                'NamaDokumen.required' => 'Nama Dokumen harus diisi',
                'id_KdUnitKerja.required' => 'Harus dipilih',
                'id_KdKlasifikasi.required' => 'Harus dipilih',
                'id_KdTypeKlasifikasi.required' => 'Harus dipilih',
                'TanggalQA.required' => 'Harus dipilih',
                'TanggalQA.before' => 'Harus lebih kecil dari hari sekrang',
                'PenanggungJawab.required' => 'Harus dipilih',
                'LampiranDokumen.required' => 'Harus dilampirkan',
            ]

            
        );

        // // file validation
        // $validator      =   Validator::make($request->all(),
        // ['LampiranDokumen'      =>   'required|mimes:xl,xlsx,jpeg,png,jpg,bmp,doc,pdf,docx,zip|max:4096']);

        // // if validation fails
        // if($validator->fails()) {
        //     return back()->withErrors($validator->errors());
        // }

        
        // if validation success
        if($file   =   $request->LampiranDokumen) {
        $name      =   $file->getClientOriginalName();
        // $name      =   time().time().'.'.$file->getClientOriginalName();
        
        $target_path    =   public_path('storage');
        
            if($file->store($target_path, $name)) {
                
                // save file name in the database
                $file   =   Dokumen::create(['LampiranDokumen' => $name]);
            
                return back()->with("success", "File uploaded successfully");
            }
        }   
        // dd($request);
        // $fi = $request->LampiranDokumen;
        // $data = $request->file('LampiranDokumen');
        // return $data;
        // if ($file = $request->file('LampiranDokumen')) {
        //     $name = $file->getClientOriginalName();
        //     echo $name;
        // }
        // $tujuan = 'doc';

        // $file = $request->file('LampiranDokumen');

        // echo $file;
        // echo 'File Name: ' . $file->getClientOriginalName();
        // echo '<br>';

        // // ekstensi file
        // echo 'File Extension: ' . $file->getClientOriginalExtension();
        // echo '<br>';

        // // real path
        // echo 'File Real Path: ' . $file->getRealPath();
        // echo '<br>';

        // // ukuran file
        // echo 'File Size: ' . $file->getSize();
        // echo '<br>';

        // // tipe mime
        // echo 'File Mime Type: ' . $file->getMimeType();


        Dokumen::create($request->all());
        return redirect()->route('data.index');
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
        $karyawan = Karyawan::all();
        $klasifikasi = Klasifikasi::all();
        $tipe = TypeKlasifikasi::all();
        $unit = UnitKerja::all();
        $dokumen = Dokumen::find($id);
        // dd($dokumen);
        // return $dokumen;
        return view('edit', compact('karyawan', 'klasifikasi', 'tipe', 'unit', 'dokumen'));
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
        // return $id;
        $request->validate(
            [
                'NamaDokumen' => 'required',
                'id_KdUnitKerja' => 'required',
                'id_KdKlasifikasi' => 'required',
                'id_KdTypeKlasifikasi' => 'required',
                'TanggalQA' => 'required|before:today',
                'PenanggungJawab' => 'required',
            ],
            [
                'NamaDokumen.required' => 'Nama Dokumen harus diisi',
                'id_KdUnitKerja.required' => 'Harus dipilih',
                'id_KdKlasifikasi.required' => 'Harus dipilih',
                'id_KdTypeKlasifikasi.required' => 'Harus dipilih',
                'TanggalQA.required' => 'Harus dipilih',
                'TanggalQA.before' => 'Harus lebih kecil dari hari sekrang',
                'PenanggungJawab.required' => 'Harus dipilih',
                ]
            );
            Dokumen::find($id)
            ->update([
                'NamaDokumen' => $request->NamaDokumen,
                'id_KdUnitKerja' => $request->id_KdUnitKerja,
                'id_KdKlasifikasi' => $request->id_KdKlasifikasi,
                'id_KdTypeKlasifikasi' => $request->id_KdTypeKlasifikasi,
                'TanggalQA' => $request->TanggalQA,
                'PenanggungJawab' => $request->PenanggungJawab
                ]);
                
                return redirect()->route('data.index');
            }
            
    public function download($id){
        dd($id);
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
        // dd($id);
        $data = Dokumen::destroy($id);

        // return $data;
        return redirect()->route('data.index');
    }
}
