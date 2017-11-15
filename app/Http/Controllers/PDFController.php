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
    	$UriParts = $this->getUriParts($request->getRequestUri());

        $url = urlencode($UriParts['url']);
        $name = $UriParts['pdfName'];

        if ( $url && filter_var($url, FILTER_VALIDATE_URL) && $name ) {
            $file = "$name.pdf";
            $exec = $this->nowMake($url, $file);

            var_dump($exec);

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
	    $UriParts = $this->getUriParts($request->getRequestUri());

	    $url = $UriParts['url'];
	    $name = $UriParts['pdfName'];

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

	/**
	 * Get URI parts - so as to preserve GET variables in URI
	 * @param $uri
	 *
	 * @return array
	 */
    protected function getUriParts($uri)
    {
    	$uri = strtolower($uri);
    	$url = '';
    	$pdfName = '';

    	if ( str_contains($uri, '?url=') ) {
    		$url = explode('?url=', $uri)[1];

    		if ( str_contains($uri, '&pdfname=') ) {
    			$innerParts = explode('&pdfname=', $url);
    			$url = $innerParts[0];
    			$pdfName = str_slug($innerParts[1]);
		    }
		    else
		    	$pdfName = str_slug($url);
	    }
	    elseif ( str_contains($uri, '?pdfname=') && str_contains($uri, '&url=') ) {
    		$parts = explode('&url=', $uri);
    		$url = $parts[1];
    		$pdfName = str_slug(explode('?pdfname=', $parts[0])[1]);
	    }

	    return compact('url', 'pdfName');
    }

}
