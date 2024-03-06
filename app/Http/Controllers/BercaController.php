<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\Penjualan;
use App\Models\Payment;

class BercaController extends Controller
{
    public function getOmzet()
    {
        $result1 = Penjualan::leftJoin('marketing', 'penjualan.marketing_id', '=', 'marketing.id')->select('nama', DB::raw("DATE_FORMAT(date, '%M') as bulan"), DB::raw("SUM(total_balance) as omzet"))->where('date', 'like', '%-05-%')->groupBy('marketing_id', 'bulan', 'nama');
        $result2 = Penjualan::leftJoin('marketing', 'penjualan.marketing_id', '=', 'marketing.id')->select('nama', DB::raw("DATE_FORMAT(date, '%M') as bulan"), DB::raw("SUM(total_balance) as omzet"))->where('date', 'like', '%-06-%')->groupBy('marketing_id', 'bulan', 'nama');
        $result3 = $result1->union($result2)->orderBy('bulan', 'desc')->get();

        foreach ($result3 as $rs3) {
            if ($rs3->omzet >= 0 && $rs3->omzet <= 100000000) {
                $rs3->komisi = 0;
            } else if ($rs3->omzet > 100000000 && $rs3->omzet <= 200000000) {
                $rs3->komisi = 2.5;
            } else if ($rs3->omzet > 200000000 && $rs3->omzet <= 500000000) {
                $rs3->komisi = 5;
            } else if ($rs3->omzet > 500000000) {
                $rs3->komisi = 10;
            }

            $rs3->omzet2 = number_format($rs3->omzet, 0, ',', '.');
            $rs3->komisi_nominal = number_format($rs3->omzet * $rs3->komisi / 100, 0, ',', '.');
        }

        return response()->json($result3);
    }

    public function payment(Request $request)
    {
        $data1 = $request->validate([
            'id_card' => 'required',
            'total_payment' => 'required',
        ]);

        Payment::create($data1);

        $data2 = $request->validate([
            'id' => 'required',
            'transaction_number' => 'required',
            'marketing_id' => 'required',
            'date' => 'required',
            'cargo_fee' => 'required',
            'total_balance' => 'required',
            'grand_total' => 'required',
        ]);

        $penjualan = Penjualan::create($data2);
        $transaction_number = "TRX0-" . $penjualan->id;

        $penjualan->update(['transaction_number' => $transaction_number]);

        return redirect('/')->with('success', 'Data inserted successfully!');
    }
}
