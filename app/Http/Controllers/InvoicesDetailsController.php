<?php

namespace App\Http\Controllers;

use App\invoices;
use App\invoice_attachments;
use App\Models\Invoice;
use App\Models\Invoices_attachments;
use App\Models\Invoices_details;
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Http\Request;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    
    {
        $invoices = Invoice::where('id',$id)->first();
        $details  = Invoices_details::where('id_Invoice',$id)->get();
        $attachments  = Invoices_attachments::where('invoice_id',$id)->get();

        return view('invoices.invoicesdetails',compact('invoices','details','attachments'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $invoices = Invoices_attachments::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }

    public function get_file($invoice_number, $file_name)
    {
        $path = $invoice_number . '/' . $file_name;       
        if (Storage::disk('public_uploads')->exists($path)) {
            return response()->download(Storage::disk('public_uploads')->path($path));
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }


    public function open_file($invoice_number, $file_name)
    {
        $path = $invoice_number . '/' . $file_name;
        if (Storage::disk('public_uploads')->exists($path)) {
            return response()->file(Storage::disk('public_uploads')->path($path));
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }

}