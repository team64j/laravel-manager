<?php

declare(strict_types=1);

namespace Team64j\LaravelManager\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('manager.auth:manager', [
            'except' => ['login', 'formLogin', 'formForgot'],
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function formLogin(): View | Factory | Application
    {
        return view('manager::login', [
            'basePath' => str_replace([base_path(), DIRECTORY_SEPARATOR], ['', '/'], dirname(__DIR__, 3)) . '/',
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function formForgot(): View | Factory | Application
    {
        return view('manager::forgot', [
            'basePath' => str_replace([base_path(), DIRECTORY_SEPARATOR], ['', '/'], dirname(__DIR__, 3)) . '/',
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth('manager')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        auth('web')->login(auth('manager')->user(), $request->boolean('remember'));

        $request->session()->regenerate();
        $request->session()->put([
            'XSRF-TOKEN' => Str::random(60),
        ]);

        return $this->createNewToken((string) $token);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth('manager')->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->createNewToken(auth('manager')->refresh());
    }

    /**
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        return response()->json(auth('manager')->user());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function createNewToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('manager')->factory()->getTTL() * 60,
            'user' => auth('manager')->user(),
            'redirect' => url('manager'),
        ]);
    }
}
