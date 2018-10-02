<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 03/11/16
 * Time: 12:46
 */

use Intervention\Image\Facades\Image;

if (!function_exists('route_parameter')) {
    /**
     * Get a given parameter from the route.
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    function route_parameter($name, $default = null)
    {
        $routeInfo = app('request')->route();

        return array_get($routeInfo[2], $name, $default);
    }
}

if (!function_exists('coordinates_str_to_array')) {
    /**
     * Convert a number of coordinates from string to array
     *
     * @param string $coordinatesString e.g. -20,30|45,60
     * @return array $locationArray
     */
    function coordinates_str_to_array($coordinatesString)
    {
        $locationArray = array();
        foreach($coordinatesString as $c) {
            $arr = explode(',', $c);
            $lon = (float) $arr[0];
            $lat = (float) $arr[1];
            $locationArray[] = array($lon,$lat);
        }
        return $locationArray;
    }
}

if (!function_exists('createImageThumb')) {
    /**
     * Create an image thumbnail and store in the specified path.
     *
     * @param string $image
     * @param string $thumbWidth
     * @param string $thumbHeight
     * @param string $path
     * @return object $img
     */
    function createImageThumb($image, $thumbWidth, $thumbHeight, $path, $fileName = null)
    {
        if (empty($fileName)) {
            $original_file_name = $image->getClientOriginalName();
            $hash = sha1(Auth::id() . microtime());
            $file_extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
            $fileName = $hash . '.' . $file_extension;
        }

        $imagePath = base_path() . '/' . $path . '/' . $fileName;
        $img = Image::make($image->getRealPath());
        $img->resize($thumbWidth, $thumbHeight, function ($constraint) {
            $constraint->aspectRatio();
        })->save($imagePath);
        $img->dirname = str_replace(base_path() . '/', '', $img->dirname);

        return $img;
    }
}

if (!function_exists('generateImageThumbfromURL')) {
    /**
     * Create an image thumbnail from URL.
     *
     * @param string $image
     * @param string $thumbWidth
     * @param string $thumbHeight
     * @param string $format
     * @return object $img
     */
    function generateImageThumbfromURL($imageUrl, $thumbWidth, $thumbHeight, $format=null)
    {
        $imageName = basename($imageUrl);
        $imageExtension = strtolower(substr(strrchr($imageName,"."), 1));

        switch( $imageExtension ) {
            case "gif": 
                $ctype = 'image/gif';
                $format = 'gif';
                break;
            case "png": 
                $ctype = 'image/png'; 
                $format = 'png';
                break;
            case "jpeg":
            case "jpg": 
            default:
                $ctype = 'image/jpeg'; 
                $format = 'jpg';
                break;
        }

        $img = Image::make($imageUrl);
        $img->resize($thumbWidth, $thumbHeight, function ($constraint) {
            $constraint->aspectRatio();
        });

        header('Content-type: ' . $ctype);
        echo $img->stream($format);
    }
}
