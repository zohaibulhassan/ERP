<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(! function_exists('create_excel')) {
    function create_excel($excel, $filename,$ftype = 'xlsx') {
        if($ftype == "xlsx"){
            header("Content-Type: ".get_mime_by_extension('xlsx')." charset=utf-8");
            header('Content-Disposition: attachment;filename="' . $filename . '.'.$ftype.'"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        }
        else{
            header("Content-Type: ".get_mime_by_extension($ftype)." charset=utf-8");
            header('Content-Disposition: attachment;filename="' . $filename . '.'.$ftype.'"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($excel, $ftype);

        }
        $objWriter->save('php://output');
        exit;
    }
}
