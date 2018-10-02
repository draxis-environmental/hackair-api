<?php

// use Illuminate\Http\UploadedFile;

// class MobileProfilePhotoUploadTest extends TestCase
// {
//     public function test_file_upload()
//     {
//         $stub = __DIR__.'/resources/sky.jpg';
//         $name = 'sky.jpg';
//         $path = sys_get_temp_dir().'/'.$name;

//         copy($stub, $path);

//         $file = new UploadedFile($path, $name, 'image/jpg', filesize($path), null, true);

//         $this->loginWithFakeUser();
//         $response = $this->call('POST', '/users/1/profile_picture', [], [], ['profile_picture' => $file], ['Accept' => 'application/json']);

//         $this->assertResponseOk();
//         $content = json_decode($response->getContent());

//         $profile_picture_url = $content->data->profile_picture;
//         $uploaded = str_replace(url() . DIRECTORY_SEPARATOR, '', $profile_picture_url);
//         $uploaded = 'public'.DIRECTORY_SEPARATOR.$uploaded;

//         $this->assertFileExists(base_path().DIRECTORY_SEPARATOR.$uploaded);

//         @unlink($uploaded);
//     }
// }
