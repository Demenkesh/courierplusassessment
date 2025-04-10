<?php

namespace App\Http\Services\Auth;

use Carbon\Carbon;
use App\Mail\Mails;
use App\Models\User;
use App\Jobs\SendMailJob;
use App\Mail\Passwordreset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UnverifiedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthServices
{
    public function sendResetLinkEmail(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email' => 'required|max:191',
                'callback_url' => 'required',
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
                    'status' => false, // 404
                    'error' => 'User not found',
                ], Response::HTTP_FORBIDDEN);
            }

            $tokens = Str::random(64);

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $tokens,
                'created_at' => Carbon::now(),
            ]);

            $password_Url = $request->callback_url . '?email=' . urlencode($user->email) . '&token=' . $tokens;

            $password_Url = $password_Url;

            $mail = [
                'title' => 'Reset password',
                'email'  => $user->email,
                'name'   => $user->name,
                'subject' => 'Reset password',
                'message' => $password_Url,
                'message1' => "We see you've forgotten your password, not to worry, it happens to the best of us.",
                'message2' => "Click on this link below to reset your password, or copy the URL and paste on your browser.",
                'message3' => $password_Url,
            ];

            Mail::to($mail['email'])->send(new Mails($mail, $request->email));

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Password reset link sent successfully to your email',
                'link' =>  $password_Url
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function resetPassword(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
                'token' => 'required',
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
                    'status' => false, // 404
                    'error' => 'Password rest failed. Try again!',
                ], Response::HTTP_FORBIDDEN);
            }

            $check_token = DB::table('password_reset_tokens')->where([
                'email' => $request->email,
                'token' => $request->token,
            ])->first();

            if (!$check_token) {
                return response()->json([
                    'status' => false, // 401
                    'error' => 'Password reset failed. Try again!',
                ], Response::HTTP_FORBIDDEN);
            }
            $token = Str::random(10);
            User::where('email', $request->email)->update([
                'password' => Hash::make($request->password),
                'remember_token' => $token,
            ]);
            DB::table('password_reset_tokens')->where([
                'email' => $request->email,
            ])->delete();

            DB::commit();
            return response()->json([
                'status' => true, // 200
                'message' => 'Password changed successfully',
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false, // 500
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function verifyEmail(Request $request)
    {
        try {
            $email = $request->email;
            $user_id = User::where('email', $email)->first();

            // request from the user type input
            $code = $request->input('code');

            $minutes = Carbon::now()->diffInMinutes($user_id->created_at);

            // The link should expire after 10 mins
            if ($minutes > 10) {
                return response()->json([
                    'status' => false, // 401
                    'error' => 'Code expired , check your mail for new code  ',
                ], Response::HTTP_FORBIDDEN);
            }

            if ($code == $user_id->verification_code) {
                if (!$user_id->email_verified_at) {
                    $user_id->forceFill([
                        'email_verified_at' => now()
                    ])->save();

                    $user_id->update([
                        'verification_code' => null,
                        'tenant_id' => tenant('id'),
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false, // 401
                    'message' => 'Code incorrect ',
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->json([
                'status' => true, // 200
                // 'access_token' => $token,
                'message' => 'Email verified',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, // 500
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function resendVerificationMail(Request $request)
    {
        $email = $request->email;
        $user_id = User::where('email', $email)->first();

        if (!$user_id) {
            return response()->json([
                'status' => false, // 500
                'message' => 'Email not found',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $randomNumber = rand(4089, 6839);
        $mail = [
            'title' => 'Verification Code',
            'email'  => $email,
            'subject' => 'Verification Code',
            'message' => 'Your Verification code is :' . $randomNumber,
        ];

        // $user_unverified = new UnverifiedMail();
        // $user_unverified->email = $request->email;
        $user_id->token = $randomNumber;

        Mail::to($mail['email'])->send(new Mails($mail , $request->email));

        $user_id->update();

        return response()->json([
            'status' => true, // 200
            'message' => 'Verification email resent successfully',
        ], Response::HTTP_OK);
    }
}
