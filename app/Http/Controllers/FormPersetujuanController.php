<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormPersetujuanController extends Controller
{
    public function index()
    {
        $dokters = [
            'Dr. drg. Anggraeni, Sp.KG',
            'Dr. drg. Dini Asrianti, Sp. KG (K)',
            'drg. Waviyatul Ahdi, Sp. KG',
            'drg. Dewi Isroyati, Sp. KG',
            'drg. Sasi Ramadhani, Sp. KG',
            'drg. Nurul Astrina, Sp. KG',
            'drg. Indira Larasputri, Sp. KG',
            'drg. Valeria Widita W, Sp. KG',
            'drg. Hillary Natasya, Sp. KG',

            'drg. Riana Napitupulu, Sp.Perio',
            'drg. Fathia Agzarine Deandra, Sp. Perio',

            'drg. Irma Aryani, Sp. Ortho',
            'drg. Efrina Paramitha, Sp. Ortho',
            'drg. Tri Wahyudi, Sp. Ortho',
            'drg. Benazir Amriza Dini, Sp. Ortho',

            'drg. Triana Sari Putri, Sp. KGA',
            'drg. Irvina Desiyanti, Sp. KGA',
            'drg. Annisa Rizki Amalia, Sp. KGA',
            'drg. Priska Angelia B, Sp. KGA',
            'drg. Inayu Mahardhika, Sp. KGA',
            'drg. Nidia Risky Primanda, Sp. KGA',

            'drg. Teuku Ahmad Arbi, Sp.BM',
            'drg. Isma Tria Savitri, Sp. BM',

            'drg. Isya Hanin, Sp. Prostho',
            'drg. Sonia Margareth, Sp. Prostho',
            'drg. Savedra Pratama, Sp. Prostho',
            'drg. Brian Vensen Lika, Sp. Prostho',

            'drg. Tantri Widyarani',
            'drg. Andiana Rizqi Indirayani',
            'drg. Rista Lewiyonah',
            'drg. Zatira',
            'drg. Aulia Karina Fitriananda',
            'drg. Achmad Riwandy',
            'drg. Asry Muda',
            'drg. Rizky Aditya Irwandi',
        ];

        return view('form', compact('dokters'));
    }
}
