<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Invoices_attachments;
use App\Models\Invoices_details;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::all();
        return view('invoices.invoices' , compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::all();
        return view('invoices.add_invoice' , compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Invoice::create([
            'invoice_number' =>$request->invoice_number,
            'invoice_date' =>$request->invoice_date,
            'due_date' =>$request->due_date,
            'product' =>$request->product,
            'section_id' =>$request->section_id,
            'amount_collection' =>$request->amount_collection,
            'amount_commission' =>$request->amount_commission,
            'discount' =>$request->discount,
            'value_vat' =>$request->value_vat,
            'rate_vat' =>$request->rate_vat,
            'total' =>$request->total,
            'status' => 'غير مدفوعه',
            'value_status' => 2,
            'note' =>$request->note,
        ]);
        $invoice_id = Invoice::latest()->first()->id;
        Invoices_details::create([
            'id_invoice' =>$invoice_id,
            'invoice_number' =>$request->invoice_number,
            'product' =>$request->product,
            'section' =>$request->section_id,
            'status' => 'غير مدفوعه',
            'value_status' => 2,
            'note' =>$request->note,
            'user' =>(Auth::user()->name),

        ]);
        if($request->hasFile('pic')){
            $invoice_id = Invoice::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new Invoices_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id; 
            $attachments->save();

            $image_name = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('attachments/'.$invoice_number),$image_name);
        }
        session()->flash('Add' , 'تم اضافه الفاتوره بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoices = Invoice::where('id' , $id)->first();
        return view('invoices.Status_update' , compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices = Invoice::where('id' , $id)->first();
        $sections = Section::all();
        return view('invoices.edit_invoice' , compact('invoices' , 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $invoices = Invoice::findorfail($request->invoice_id);
        $invoices->update([
            'invoice_number' =>$request->invoice_number,
            'invoice_date' =>$request->invoice_date,
            'due_date' =>$request->due_date,
            'product' =>$request->product,
            'section_id' =>$request->Section,
            'amount_collection' =>$request->amount_collection,
            'amount_commission' =>$request->amount_commission,
            'discount' =>$request->discount,
            'value_vat' =>$request->value_vat,
            'rate_vat' =>$request->rate_vat,
            'total' =>$request->total,
            'note' =>$request->note,
        ]);

        $Invoices_details = Invoices_details::where('id_invoice' , $request->invoice_id);
        $Invoices_details->update([
            'invoice_number' =>$request->invoice_number,
            'product' =>$request->product,
            'section' =>$request->Section,
            'note' =>$request->note,
        ]);
        session()->flash('edit' , 'تم تعديل الفاتوره بنجاح');
        return back();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = Invoice::where('id' , $invoice_id);
        $attachments = Invoices_attachments::where('invoice_id', $invoice_id)->first();

        if (!empty($attachments->invoice_number)) {
            Storage::disk('public_uploads')->deleteDirectory($attachments->invoice_number);
        }

        $invoice->forcedelete();

        session()->flash('delete_invoice');
        return redirect('invoices');
    }
    public function getproducts($id)
    {
        $states = DB::table('products')->where('section_id' , $id)->pluck('product_name' , 'id');
        return json_encode($states);
    }

    public function status_update(Request $request , $id) 
    {
        
        
        $invoices = invoice::findOrFail($id);

        if ($request->status === 'مدفوعة') {

            $invoices->update([
                'value_status' => 1,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);

            invoices_Details::create([
                'id_invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->Section,
                'status' => $request->status,
                'value_status' => 1,
                'note' => $request->note,
                'payment_date' => $request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        }

        else {
            $invoices->update([
                'value_status' => 3,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);
            invoices_Details::create([
                'id_invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->Section,
                'status' => $request->status,
                'value_status' => 3,
                'note' => $request->note,
                'payment_date' => $request->payment_date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');

    }
}
