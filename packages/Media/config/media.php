<?php

return [
    "name"          		=>"My folder",
    "name_recycle"          =>"Recycle bin",
    "path"          =>"/".config("core.admin-prefix")."/media",
    "path_api"      =>"/".config("core.admin-prefix")."/api/media",
    "length_name"   =>10,
    "length_share"  =>10,
    "length_name_file_crop"=>40,
    "max_folders_in_folder"=>5,
    "paginate" =>20,
    "paginate_file" =>80,
    'thumb_img_width'  => 210,
    'thumb_img_height' => 118,
    'thumb_name_default'=>config("media.thumb_img_width").'x'.config("media.thumb_img_height"),
    'size_upload_image'=>512,//kb
    'max_size_upload'=>10240,//kb
    'type_images'=>[
    	'gif'  => 'GIF Image',
        'jpg'  => 'JPEG Image',
        'jpeg' => 'JPEG Image',
        'png'  => 'PNG Image',
    ],
    'file_type_array' => [
        'pdf'  => 'Adobe Acrobat',
        'doc'  => 'Microsoft Word',
        'docx' => 'Microsoft Word',
        'xls'  => 'Microsoft Excel',
        'xlsx' => 'Microsoft Excel',
        'gif'  => 'GIF Image',
        'jpg'  => 'JPEG Image',
        'jpeg' => 'JPEG Image',
        'png'  => 'PNG Image',
        'ppt'  => 'Microsoft PowerPoint',
        'pptx' => 'Microsoft PowerPoint',
    ],
    'file_icon_array' => [
        'pdf'  => 'fa-file-pdf-o',
        'doc'  => 'fa-file-word-o',
        'docx' => 'fa-file-word-o',
        'xls'  => 'fa-file-excel-o',
        'xlsx' => 'fa-file-excel-o',
        'gif'  => 'fa-file-image-o',
        'jpg'  => 'fa-file-image-o',
        'jpeg' => 'fa-file-image-o',
        'png'  => 'fa-file-image-o',
        'ppt'  => 'fa-file-powerpoint-o',
        'pptx' => 'fa-file-powerpoint-o',
    ],

];