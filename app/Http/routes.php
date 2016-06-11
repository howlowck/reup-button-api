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

Route::get('users/requests', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();
    $requests = $user->requests;
}]);

Route::get('users/test', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();
    return response()->json(['status' => 'success', 'data' => ['user' => $user]]);
}]);

Route::get('users/current/organizations', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();
    $orgs = $user->organizations;
    return response()->json(['status' => 'success', 'data' => ['organizations' => $orgs]]);
}]);

Route::post('organizations/register', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();

    $name = request('org_name');
    $street = request('street');
    $city = request('city');
    $state = request('state');

    $org = new \App\Organization();
    $org->email = $user->email;
    $org->name = $name;
    $org->street = $street;
    $org->city = $city;
    $org->state = $state;

    $org->save();
    $user->organizations()->attach($org->id, ['role' => 'admin']);
    return response()->json(['status' => 'success']);
}]);

Route::post('requests', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();

    $item = request('item');
    $quantity = request('quantity');
    $unit = request('unit');

}]);

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