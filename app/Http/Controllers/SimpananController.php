<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Simpanan;
use App\Models\JenisSimpanan;
use App\Models\Transaction;

class SimpananController extends Controller
{
    public $status='success';

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function data()
    {
        $data['simpanan_pokok'] = format_idr(\Auth::user()->simpanan_pokok);
        $data['simpanan_sukarela'] = format_idr(\Auth::user()->simpanan_sukarela);
        $data['simpanan_wajib'] = format_idr(\Auth::user()->simpanan_wajib);
        $data['simpanan_lain_lain'] = format_idr(\Auth::user()->simpanan_lain_lain);
        $data['shu'] = format_idr(\Auth::user()->shu);

        log_activity('Simpanan');

        return response()->json(['status'=>$this->status,'data'=>$data], 200);
    }

    public function store(Request $r)
    {
        $validator = \Validator::make($r->all(), [
            'amount'=> 'required',
            'jenis_simpanan'=> 'required',
        ]);
        
        log_activity('Simpanan Store');

        if ($validator->fails()) {
            $msg = '';
            foreach ($validator->errors()->getMessages() as $key => $value) {
                $msg .= $value[0]."\n";
            }
            return response()->json(['status'=>'failed','message'=>$msg], 200);
        }

        $jenis_simpanan = JenisSimpanan::where('name',$r->jenis_simpanan)->first();
        if($jenis_simpanan){
            $data = new Simpanan();
            $data->no_transaksi = $jenis_simpanan->id.date('my').str_pad((Simpanan::count()+1),4, '0', STR_PAD_LEFT);
            $data->jenis_simpanan_id = $jenis_simpanan->id;
            $data->amount = $r->amount;
            $data->user_member_id = \Auth::user()->member->id;
            $data->status = 0;
            $data->save();

            $transaksi = new Transaksi();
            $transaksi->no_transaksi = "S".date('my').\Auth::user()->member->id.str_pad((Transaksi::count()+1),4, '0', STR_PAD_LEFT);
            $transaksi->user_member_id = \Auth::user()->member->id;
            $transaksi->user_id = \Auth::user()->id;
            $transaksi->amount = $r->amount;
            $transaksi->name = isset($jenis_simpanan->name) ? $jenis_simpanan->name :'-';
            $transaksi->transaction_table = 'simpanan';
            $transaksi->transaction_id = $data->id;
            $transaksi->save();
        }

        return response()->json(['status'=>$this->status], 200);
    }
    /**
     * Simpanan Pokok
     * @return : json
     */
    public function pokok()
    {
        log_activity('Simpanan Pokok');

        $simpanan = Simpanan::where(['user_id'=>\Auth::user()->id,'jenis_simpanan_id'=>1])->orderBy('id','DESC');
        $data = [];
        foreach($simpanan->paginate(100) as $k => $item){
            $data[$k]['id'] = $item->id;
            $data[$k]['amount'] = format_idr($item->amount);
            $data[$k]['date'] = date('d F Y',strtotime($item->created_at));
            $data[$k]['status'] = $item->status;
        }
        
        return response()->json(['status'=>$this->status,'data'=>$data], 200);
    }

    public function pokokStatus()
    {
        $simpanan = Simpanan::where(['user_id'=>\Auth::user()->id,'jenis_simpanan_id'=>1])->orderBy('bulan','ASC');
        if(isset($_GET['tahun']))
            $simpanan->where('tahun',$_GET['tahun']);
        else
            $simpanan->where('tahun',date('Y'));

        $data = [];
        foreach($simpanan->paginate(100) as $k => $item){
            $data[$k]['id'] = $item->id;
            $data[$k]['amount'] = format_idr($item->amount);
            $data[$k]['date'] = date('d F Y',strtotime($item->created_at));
            $data[$k]['bulan'] = date('F', mktime(0, 0, 0, $item->bulan, 10)); 
            $data[$k]['status'] = $item->status;
            $data[$k]['payment_date'] = $item->payment_date ? date('d F Y', strtotime($item->payment_date)) : '-';
        }
        
        return response()->json(['status'=>$this->status,'data'=>$data], 200);
    }

    public function wajib()
    {
        log_activity('Simpanan Wajib');

        $simpanan = Simpanan::where(['user_id'=>\Auth::user()->id,'jenis_simpanan_id'=>2])->orderBy('id','DESC');
        $data = [];
        foreach($simpanan->paginate(100) as $k => $item){
            $data[$k]['id'] = $item->id;
            $data[$k]['amount'] = format_idr($item->amount);
            $data[$k]['date'] = date('d F Y',strtotime($item->created_at));
            $data[$k]['status'] = $item->status;
        }
        
        return response()->json(['status'=>$this->status,'data'=>$data], 200);
    }

    public function wajibStatus()
    {   
        $simpanan = Simpanan::where(['user_id'=>\Auth::user()->id,'jenis_simpanan_id'=>2])->orderBy('bulan','ASC');
        if(isset($_GET['tahun']))
            $simpanan->where('tahun',$_GET['tahun']);
        else
            $simpanan->where('tahun',date('Y'));

        $data = [];
        foreach($simpanan->paginate(100) as $k => $item){
            $data[$k]['id'] = $item->id;
            $data[$k]['amount'] = format_idr($item->amount);
            $data[$k]['date'] = date('d F Y',strtotime($item->created_at));
            $data[$k]['bulan'] = date('F', mktime(0, 0, 0, $item->bulan, 10)); 
            $data[$k]['status'] = $item->status;
            $data[$k]['payment_date'] = $item->payment_date ? date('d F Y', strtotime($item->payment_date)) : '-';
        }
        
        return response()->json(['status'=>$this->status,'data'=>$data], 200);
    }

    public function sukarela()
    {   
        log_activity('Simpanan Sukarela');

        $simpanan = Simpanan::where(['user_id'=>\Auth::user()->id,'jenis_simpanan_id'=>3])->orderBy('id','DESC');
        $data = [];
        foreach($simpanan->paginate(100) as $k => $item){
            $data[$k]['id'] = $item->id;
            $data[$k]['amount'] = format_idr($item->amount);
            $data[$k]['date'] = date('d F Y',strtotime($item->created_at));
            $data[$k]['status'] = $item->status;
        }
        
        return response()->json(['status'=>$this->status,'data'=>$data], 200);
    }

    public function lainnya()
    {
        log_activity('Simpanan Lainnya');

        $simpanan = Simpanan::where(['user_id'=>\Auth::user()->id,'jenis_simpanan_id'=>4])->orderBy('id','DESC');
        $data = [];
        foreach($simpanan->paginate(100) as $k => $item){
            $data[$k]['id'] = $item->id;
            $data[$k]['amount'] = format_idr($item->amount);
            $data[$k]['date'] = date('d F Y',strtotime($item->created_at));
            $data[$k]['status'] = $item->status;
        }
        
        return response()->json(['status'=>$this->status,'data'=>$data], 200);
    }
}
