<?php

Route::get('/', function () {
    return view('welcome');
});

Route::post('users/register', function () {
    $name = request('name');
    $email = request('email');
    $password = request('password');
    $hashed = Hash::make($password);
    $user = new \App\User();
    $user->name = $name;
    $user->email = $email;
    $user->password = $hashed;
    $user->save();

    $token = JWTAuth::fromUser($user);
    return response(['status' => 'success', 'data' => ['token' => $token]]);
});

Route::post('users/login', function () {
    $email = request('email');
    $password = request('password');
    if (Auth::attempt(compact('email', 'password'))) {
        $user = \App\User::where('email', $email)->first();
        $token = JWTAuth::fromUser($user);
        return response(['status' => 'success', 'data' => ['token' => $token]]);
    };

    return response(['status'=>'failed', 'data' => ['reason' => 'the credential is not right']]);
});

Route::post('/api/organizations/register', function () {
    $name = request('name');
    $address = request('address');
    $orgType = request('org_type');
    $contactEmail = request('contact_email');
});

Route::post('/api/items/request', function () {

});

Route::post('/api/trigger', function () {
    $type = request('intent_type', 'need');
    $lat = request('lat');
    $long = request('long');
    $device = request('device_id');
});

Route::post('/api/subscribe', function () {

});

Route::get('/api/needs', function () {

});