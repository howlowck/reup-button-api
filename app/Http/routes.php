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

Route::get('users/test', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();
    return response()->json(['status' => 'success', 'data' => ['user' => $user]]);
}]);

Route::post('/api/organizations/register', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();

    $name = request('name');
    $address = request('address');
    $orgType = request('org_type');
    $contactEmail = request('contact_email');

    $org = new \App\Organization();
    $org->name = $name;
    
}]);

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