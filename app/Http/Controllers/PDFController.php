<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function __construct()
    {

    }

    public function makePdf(Request $request)
    {
        $url = $request->url;
        $name = $request->pdfName ?: str_slug($url);

        if ( $url && filter_var($url, FILTER_VALIDATE_URL) && $name ) {
            var_dump(shell_exec("xvfb-run wkhtmltopdf $url $name.pdf"));
        }

    }
}
