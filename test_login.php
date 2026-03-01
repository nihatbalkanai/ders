<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'test@smarttest.com')->first();

if (!$user) {
    echo "ERROR: User not found!\n";
    exit(1);
}

echo "User found: ID={$user->id}, Name={$user->name}, Email={$user->email}\n";
echo "Grade: {$user->grade_level}\n";
echo "Password hash: " . substr($user->password, 0, 30) . "...\n";
echo "Hash::check('password123'): " . (Hash::check('password123', $user->password) ? 'TRUE' : 'FALSE') . "\n";

// Also test API login directly
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::create('/api/login', 'POST', [
    'email' => 'test@smarttest.com',
    'password' => 'password123',
], [], [], [
    'HTTP_ACCEPT' => 'application/json',
    'CONTENT_TYPE' => 'application/json',
]);
$response = $kernel->handle($request);
echo "\nAPI Login Response Status: " . $response->getStatusCode() . "\n";
echo "API Login Response Body: " . $response->getContent() . "\n";
