<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\ChatRouletteEvent;
use App\Http\Controllers\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/send-message', function (Request $request) {
    $message = $request->input('message');
    $continent = $request->input('continent');
    $userId = $request->input('userId');
    
    event(new ChatRouletteEvent($message, $continent, $userId));
    
    return response()->json(['status' => 'Message sent']);
});


Route::post('/join-chat', [ChatController::class, 'joinChat']);
Route::post('/send-message', [ChatController::class, 'sendMessage']);
Route::post('/leave-chat', [ChatController::class, 'leaveChat']);