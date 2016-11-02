<?php
/**
 * Created by PhpStorm.
 * User: johnpaul
 * Date: 1/20/2016
 * Time: 3:59 PM
 */
function upload_file($dir, $key, $filename='', $allowed=array())
{
    $return = 0;

    // Set filename to write to
    $oldfile = preg_replace('/\s+/', '_', $_FILES[$key]['name']);
    if($filename != ''){
        $newfile = preg_replace('/\s+/', '_', $filename);
    }
    else{
        $newfile = strtolower(str_replace('\\', '', $oldfile));
    }

    // Build full path names
    $olduploadfile = $dir.$oldfile;
    $newuploadfile = $dir.$newfile;
    $oldinfo = pathinfo($olduploadfile);
    $newinfo = pathinfo($newuploadfile);

    // Check if extension is allowed
    $ok = 1;
    if(!empty($allowed)){
        if(!in_array(strtolower($oldinfo['extension']), $allowed)){
            $ok = 0;
        }
    }

    if($ok){
        $file_no_ext = substr($newinfo['basename'],0,strlen($newinfo['basename']) - (strlen($newinfo['extension']) + 1));

        // Check if file exists, create unique name if so.
        $i = 0;
        while(file_exists($newuploadfile)){
            $newuploadfile = $dir.$file_no_ext.'_'.$i.'.'.$newinfo['extension'];
            $i++;
        }
        if(move_uploaded_file($_FILES[$key]['tmp_name'], $newuploadfile)){
            $return = basename($newuploadfile);
        }
    }

    return $return;
}
function image_resize($source_file, $dest_file, $target_width=0, $target_height=0)
{
    // Check file extensions
    $source_type = strtolower(substr($source_file, -4));
    $dest_type = strtolower(substr($dest_file, -4));

    // Create image form source file
    if($source_type == ".jpg" || $source_type == "jpeg"){
        $source_image = imagecreatefromjpeg($source_file);
    }
    elseif($source_type == ".gif") {
        $source_image = imagecreatefromgif($source_file);
    }
    elseif($source_type == ".png") {
        $source_image = imagecreatefrompng($source_file);
    }
    else{
        return FALSE;
    }

    if($source_image == FALSE){
        return FALSE;
    }
    else{
        // Check current dimensions
        $source_width = imagesx($source_image);
        $source_height = imagesy($source_image);

        // Copy defaults
        $copy_x = 0;
        $copy_y = 0;
        $copy_width = $source_width;
        $copy_height = $source_height;

        // Figure out target dimensions
        if($target_width == 0 && $target_height == 0){
            $target_width = $source_width;
            $target_height = $source_height;
        }
        elseif($target_height == 0){
            // Scale Y based on target X (if target < source. no up-sizing.)
            $scale = (float)($source_width / $target_width);
            if($scale > 1){
                $target_height = round($source_height / $scale);
            }
            else{
                $target_width = $source_width;
                $target_height = $source_height;
            }
        }
        elseif($target_width == 0){
            // Scale X based on target Y (if target < source. no up-sizing.)
            $scale = (float)($source_height / $target_height);
            if($scale > 1){
                $target_width = round($source_width / $scale);
            }
            else{
                $target_width = $source_width;
                $target_height = $source_height;
            }
        }
        else{
            // Crop to target dimensions
            $ratio = (float)($source_width / $source_height);
            $target_ratio = (float)($target_width / $target_height);

            if($ratio > $target_ratio){
                // wider than target ratio, size down height to target Y
                $scale = (float)($source_height / $target_height);
                $copy_width = round($target_width * $scale);
                $copy_x = round(($source_width - $copy_width) / 2);
            }
            elseif($ratio < $target_ratio){
                // taller than target ratio, size down width to target X
                $scale = (float)($source_width / $target_width);
                $copy_height = round($target_height * $scale);
                $copy_y = round(($source_height - $copy_height) / 2);
            }
        }

        // Copy
        $dest_image = imagecreatetruecolor($target_width, $target_height);
        if($dest_type == '.png'){
            imagealphablending($dest_image, false);
            imagesavealpha($dest_image, true);
        }

        imagecopyresampled($dest_image, $source_image, 0, 0, $copy_x, $copy_y,
            $target_width, $target_height, $copy_width, $copy_height);

        // Save image to destination file
        if($dest_type == ".jpg" || $dest_type == "jpeg"){
            imagejpeg($dest_image, $dest_file, 90);
        }
        elseif($dest_type == ".gif") {
            imagegif($dest_image, $dest_file);
        }
        elseif($dest_type == ".png") {
            imagepng($dest_image, $dest_file);
        }
        else{
            return FALSE;
        }

        return TRUE;
    }
}


/*------------------------------------------------------------*/
$key = 'Filedata';
$filename = '';
$dir = './'.$_POST['sub_dir'];
$type = $_POST['type'];
$token = $_POST['token'];

if( isset($_POST['allow_all']) &&  $_POST['allow_all'] == '1'){

    $allowed = array();
} else{
    $allowed = array('jpg','jpeg','gif','png');
}


$check_token = md5('ob-t0ken123!'.$_POST['timestamp']);
//var_dump($token);
//var_dump($check_token);

if (!empty($_FILES && ($check_token == $token) ) ) {

    $file = upload_file($dir, $key, $filename, $allowed);
    $thumb = 'small_'.$file;
    $filepath = $dir.$file;
    $thumbpath = $dir.$thumb;

    if($file && file_exists($filepath)){

        // Image processing specific to type
        if($type == 'profile_image'){
            //bring the image width down to 1300

            image_resize($filepath, $thumbpath, 200);


        } elseif($type == 'measurement_photo') {

            image_resize($filepath, $thumbpath, 300);

        } elseif($type == 'featuredImage') {

            image_resize($filepath, $thumbpath, 300);

        }else{
            //max size of all photos

            image_resize($filepath, $filepath, 1140);


        }

        echo $file;
    }
    else{
        echo '0';
    }
}else{
    echo 'failed toke';
    echo '0';}

exit();
?>