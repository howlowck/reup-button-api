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

Route::get('users/current/requests', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();
    $requests = $user->requests;
    return response()->json(['status' => 'success', 'data' => ['requests' => $requests]]);
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

    $organizationId = request('organization_id');

    if ( is_null($organizationId)) {
        $organizationId = $user->organizations->first()->id;
    }

    //TODO check if the org id given is owned by the user

    $name = request('item_name');
    $quantity = request('quantity');
    $unit = request('unit');
    $tagString = request('tags');
    $tags = array_map('strtolower', explode(',', $tagString));
    $modelIds = [];
    foreach($tags as $tag) {
       $model = \App\Tag::where('name', $tag)->first();
        if (! $model) {
            $model = \App\Tag::create(['name' => $tag]);
        }
        $modelIds[] = $model->id;
    }

    $request = new \App\Request();

    $request->name = $name;
    $request->quantity = $quantity;
    $request->unit = $unit;

    $request->organization_id = $organizationId;
    $request->user_id = $user->id;
    $request->save();
    $request->tags()->attach($modelIds);
    return response()->json(['status' => 'success']);
}]);

Route::post('subscribe/organization', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();
    $orgId = request('organization_id');
    $subscription = new \App\Subscription();
    $subscription->user_id = $user->id;
    $subscription->organization_id = $orgId;
    $subscription->save();
    return response()->json(['status' => 'success']);
}]);

Route::post('subscribe/tags', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();
    $idStr = request('tags');
    $ids = explode(',', $idStr);

    foreach($ids as $id) {
        $model = new \App\SubscribeTag();
        $model->user_id = $user->id;
        $model->tag_id = (int) $id;
        $model->save();
    }

    return response()->json(['status' => 'success']);
}]);

Route::get('tags', function () {
    $tags = \App\Tag::all();
    return response()->json(['status' => 'success', 'data' => ['tags' => $tags]]);
});

Route::get('users/current/inbox', ['before' => 'jwt.auth', function () {
    $user = JWTAuth::parseToken()->toUser();
    $orgIds = $user->subscriptions->lists('organization_id')->toArray();
    $requests = \App\Request::ofOrgs($orgIds)->open()->get();

    return response()->json(['status' => 'success', 'data' => ['requests' => $requests]]);
}]);