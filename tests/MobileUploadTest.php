<?php

// use Illuminate\Http\UploadedFile;
// use Laravel\Lumen\Testing\DatabaseTransactions;

// class MobileUploadTest extends TestCase
// {
//     use DatabaseTransactions;
//     public $connectionsToTransact = ['mongodb', 'pgsql'];

//     public function test_file_upload()
//     {
//         $stub = __DIR__.'/resources/sky.jpg';
//         $name = 'sky.jpg';
//         $path = sys_get_temp_dir().'/'.$name;

//         copy($stub, $path);

//         $file = new UploadedFile($path, $name, 'image/jpg', filesize($path), null, true);

//         $this->loginWithFakeUser();
//         $payload = ['loc' => '{"type":"Point","coordinates":[22,40]}'];
//         $response = $this->call('POST', '/photos/sky', $payload, [], ['file' => $file], ['Accept' => 'application/json']);

//         $this->assertResponseOk();
//         $content = json_decode($response->getContent());

//         $path = $content->data[0]->resources->source_info->file_path;

//         $this->assertFileExists(base_path().DIRECTORY_SEPARATOR.$path);

//         @unlink($path);
//     }
// }
