<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile as File;

if (!function_exists('getRandomString')) {
    function getRandomString($length = 8): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('uploadImage')) {
    function uploadImage($path, $file, $fieldName = null, $data = null)
    {
        if (!empty($data)) {
            if ($data->$fieldName !== null) {
                $img = $data->$fieldName;
                Storage::delete('public/'.$img);
            }
        }

        $fileName = $path.'/'.getRandomString().'.'.$file->getClientOriginalExtension();
        $saved = Storage::disk('public')->put($fileName, file_get_contents($file), 'public');

        if (!$saved) {
            throw new \Exception();
        }
        return $fileName;
    }
}

if (!function_exists('sendNotification')) {
    function sendNotification($data, $device_token)
    {
        $firebase_api_key = Config::get('constant.FCM_KEY');

        $data_arr = array(
            "title" => $data['title'],
            "body" => $data['description'],
            'type' => $data['type'],
            'id' => $data['id'],
        );

        if (isset($data['image']) && $data['image'] != '') {
            $data_img_arr = array("image" => env('APP_URL').'/'.$data['image']);
            $data_arr = array_merge($data_arr, $data_img_arr);
        }

        // Push Data's
        $data1 = array(
            "registration_ids" => $device_token,
            "notification" => $data_arr,
            "data" => $data_arr
        );
        $dataString = json_encode($data1);

        $headers = [
            'Authorization: key='.$firebase_api_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
