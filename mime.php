<?php

function get_mime($filePath) {
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    switch($extension) {
    case "xlsx" : $mime="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
    case "xltx" : $mime="application/vnd.openxmlformats-officedocument.spreadsheetml.template"; break;
    case "potx" : $mime="application/vnd.openxmlformats-officedocument.presentationml.template"; break;
    case "ppsx" : $mime="application/vnd.openxmlformats-officedocument.presentationml.slideshow"; break;
    case "pptx" : $mime="application/vnd.openxmlformats-officedocument.presentationml.presentation"; break;
    case "sldx" : $mime="application/vnd.openxmlformats-officedocument.presentationml.slide"; break;
    case "docx" : $mime="application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;
    case "dotx" : $mime="application/vnd.openxmlformats-officedocument.wordprocessingml.template"; break;
    case "xlam" : $mime="application/vnd.ms-excel.addin.macroEnabled.12"; break;
    case "xlsb" : $mime="application/vnd.ms-excel.sheet.binary.macroEnabled.12"; break;
    case "pdf"  : $mime="application/pdf"; break;
    default: $mime = NULL;
    }
    return $mime;

}

?>