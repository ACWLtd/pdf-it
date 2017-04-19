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
            $file = "$name.pdf";
            $exec = shell_exec("xvfb-run wkhtmltopdf -T 0 -B 0 -L 0 -R 0 $url $file");

            if ( $exec && file_exists($file) ) {
                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                unlink($file);
                exit;
            }
        }

    }
}
