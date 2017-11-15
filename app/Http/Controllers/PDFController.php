<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function __construct()
    {

    }

    /**
     * PDF maker
     * @param $url
     * @param $file
     * @return string
     */
    protected function nowMake($url, $file)
    {
        return shell_exec("xvfb-run wkhtmltopdf -T 5 -B 5 -L 10 -R 10 --print-media-type $url $file");
    }

    /**
     * Create and download PDF
     * @param Request $request
     */
    public function makePdf(Request $request)
    {
        $url = $request->url;
        var_dump($request->getRequestUri());
        dd($request->getPathInfo());


        $name = $request->pdfName ?: str_slug($url);

        if ( $url && filter_var($url, FILTER_VALIDATE_URL) && $name ) {
            $file = "$name.pdf";
            $exec = $this->nowMake($url, $file);

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

    /**
     * @param Request $request
     * @return bool|string
     */
    public function grabPdfData(Request $request)
    {
        $url = $request->url;
        $name = $request->pdfName ?: str_slug($url);

        if ( $url && filter_var($url, FILTER_VALIDATE_URL) && $name ) {
            $file = "$name.pdf";
            $exec = $exec = $this->nowMake($url, $file);

            if ( $exec && file_exists($file) ) {
                $data = file_get_contents($file);
                unlink($file);
                return $data;
            }
        }
    }
}
