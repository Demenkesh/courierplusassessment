<?php

namespace App\Http\Controllers\Api\Tenants\Auth;

/**
 * @OA\Info(
 *    title="Vetibrea Api",
 *    version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 *   @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST1,
 *      description="Demo API Server"
 * )
 * components:
 *     securitySchemes:
 *         bearerAuth: {}  # Define an empty security scheme for bearerAuth
 *
 * security:
 *   - bearerAuth: []   # Apply bearerAuth to endpoints that require JWT authentication
 */

use Carbon\Carbon;
use App\Mail\Mails;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Http\Services\Auth\AuthServices;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api",
     *     summary="Get API Version",
     *     tags={"General"},
     *     @OA\Response(
     *         response=200,
     *         description="API version information",
     *         @OA\JsonContent(
     *             @OA\Property(property="Vertibrea", type="string", example="1.0.0"),
     *         ),
     *     ),
     * )
     */
    public function home()
    {
        return [
            'Vetibrae' => '1.0',
            'Status' => "Cors added!"

        ];
    }

    // register controller
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     description="Handles user registration and sends an email verification code",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","name","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="tenant_name", type="string", format="string", example="tenant123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully registered! Please verify your mail, email code sent."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *                     @OA\Property(property="verification_code", type="integer", example=445089)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The email has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server error message")
     *         )
     *     )
     * )
     */

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            // Generate a random verification code
            $randomNumber = rand(445089, 645839);

            // Validate the incoming request
            $emailExists = User::where('email', $request->email)->exists();
            if ($emailExists) {
                return redirect()->back()->with('error', 'The email has already been taken.');
            }

            $request->validate([
                'email' => 'required|email',
                'name' => 'required|string|max:255',
                'password' => 'required|min:8',
                'tenant_name' => 'required|string|max:255|unique:tenants,id',
            ]);

            $tenantId = $request->input('tenant_name');
            $tenant = Tenant::find($tenantId);

            if ($tenant) {
                return response()->json([
                    'status' => false, //401
                    'message' => 'Subdomain has been taken.',
                    'code' => 'E-117',
                ], Response::HTTP_FORBIDDEN);
            }

            // Create the new tenant admin user
            $user = new User();
            $user->email = strtolower($request->email);
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->verification_code = $randomNumber;
            $user->verified_by_admin = 0;



            // tenancy start
            $domain = Config::get('app.urls');
            $main_domain = $request->input('tenant_name') . '.' . $domain;
            // Remove "http://" or "https://"
            $main_domains = str_replace(['http://', 'https://'], '', $main_domain);
            // Create the tenant

            $tenant = Tenant::create([
                'id' => $request->tenant_name,
            ]);
            // dd($domain );
            $role = 'tenantadmin';
            $tenant->domains()->create(['domain' => $main_domains]);

            $user->tenant_id = $request->tenant_name;
            $user->save();

            $user->tenants()->attach($tenant->id, ['role_as' => $role]);

            // Log in the user
            Auth::login($user);
            // Send the verification email
            Mail::to($user->email)->send(new Mails([
                'title' => 'Verification Code',
                'email' => $user->email,
                'name' => $user->name,
                'subject' => 'Email Verification',
                'code' => $randomNumber,
                'imagelogo' => 'https://vintageapartelle.com/frontend/images/logoblack.png',
                'textmessagefirst' => "Welcome to " . env('APP_NAME') . ", Thank you for signing up.",
                'textmessagesecond' => "Here is your Verification Code: $randomNumber",
            ]));

            // Commit the transaction and redirect with success message
            DB::commit();
            $responses[] = [
                'status' => true,
                'message' => 'Successfully registered! Please verify your mail, email code sent.',
                'data' => [
                    'user' => $user,
                ],
            ];

            // Return consolidated response
            return response()->json($responses, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="Authenticate user and generate access token",
     *     description="User login endpoint to authenticate and return an access token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully authenticated user",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="username", type="string", example="John"),
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             ),
     *             @OA\Property(property="message", type="string", example="successfully logged user!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="The email field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authentication failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="User not found!"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Internal server error."),
     *         )
     *     )
     * )
     */

    public function login(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|max:191',
                'password' => 'required|min:8',
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->all()[0],
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'error' => 'User not found!'
                ], Response::HTTP_FORBIDDEN);
            }

            $minutes = Carbon::now()->diffInMinutes($user->updated_at);

            if ($user->email_verified_at === null) {

                if ($minutes > 10) {
                    return response()->json([
                        'status' => false, // 401
                        'error' => 'Code expired , check your mail for new code  ',
                    ], Response::HTTP_FORBIDDEN);
                }


                return response()->json([
                    'status' => false,
                    'error' => 'Email not verified! Check your email to continue!!',
                ], Response::HTTP_FORBIDDEN);
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'error' => 'Password is incorrect!',
                ], Response::HTTP_FORBIDDEN);
            } else {
                $token = $user->createToken($request->email . '_UserToken')->plainTextToken;
                return response()->json([
                    'status' => true,
                    'data' => [
                        'username' => $user->name,
                        'access_token' => $token,
                    ],
                    "message" => 'successfully logged user!',
                ], Response::HTTP_OK); //200

            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout user and revoke tokens",
     *     description="Logout endpoint to revoke all access tokens for the authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Successfully logged out user",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out user!"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not authenticated."),
     *         )
     *     )
     * )
     */

    public function logout(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated.',
            ], Response::HTTP_FORBIDDEN);
        }

        $request->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            "message" => 'Successfully logged out user!'
        ], Response::HTTP_CREATED);
    }




    /**
     * @OA\Post(
     *      path="/api/auth/password/forgot",
     *     summary="Send Password Reset Link",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "callback_url"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="callback_url", type="string", example="https://example.com/reset-password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Password reset link sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset link sent successfully to your email"),
     *             @OA\Property(property="link", type="string", example="https://example.com/reset-password?email=user@example.com&token=abc123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="The email field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="User not found")
     *         )
     *     )
     * )
     */


    public function sendResetLinkEmail(Request $request)
    {
        return (new AuthServices)->sendResetLinkEmail($request);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/password/reset",
     *     summary="Reset Password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "token"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="token", type="string", example="abc123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password changed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="The email field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Password reset failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Password reset failed. Try again!")
     *         )
     *     )
     * )
     */



    public function reset(Request $request)
    {
        return (new AuthServices)->resetPassword($request);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/email/verify",
     *     summary="Verify Email",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "code"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email verified")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Verification code incorrect or expired",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Code incorrect or expired")
     *         )
     *     )
     * )
     */




    public function show(Request $request)
    {
        return (new AuthServices)->verifyEmail($request);
    }
    /**
     * @OA\Post(
     *     path="/api/auth/resend-verification-mail",
     *     summary="Resend Verification Email",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification email resent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Verification email resent successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Email not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Email not found")
     *         )
     *     )
     * )
     */

    public function resendVerificationMail(Request $request)
    {
        return (new AuthServices)->resendVerificationMail($request);
    }
}
