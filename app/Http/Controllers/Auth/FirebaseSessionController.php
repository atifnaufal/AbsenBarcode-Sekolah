<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\FirebaseSessionRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Throwable;

class FirebaseSessionController extends Controller
{
    public function store(FirebaseSessionRequest $request): JsonResponse
    {
        abort_unless(config('firebase_integration.enabled'), 503, 'Firebase server belum diaktifkan.');

        try {
            $verifiedToken = Firebase::auth()->verifyIdToken($request->string('id_token')->toString());
            $uid = (string) $verifiedToken->claims()->get('sub');
            $email = $verifiedToken->claims()->get('email');

            $user = User::query()
                ->where('active', true)
                ->where(function ($query) use ($uid, $email) {
                    $query->where('firebase_uid', $uid);

                    if ($email) {
                        $query->orWhere('email', $email);
                    }
                })
                ->first();

            if (! $user) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Akun Firebase belum dipasangkan dengan user sekolah.',
                ], 403);
            }

            if (! $user->firebase_uid) {
                $user->forceFill(['firebase_uid' => $uid])->save();
            }

            Auth::login($user, false);
            $request->session()->regenerate();

            $redirectRoute = match ($user->role?->value) {
                UserRole::ADMIN_SEKOLAH->value => route('dashboard'),
                UserRole::GURU->value => route('student.dashboard'),
                UserRole::SISWA->value => route('student.dashboard'),
                default => route('login'),
            };

            return response()->json([
                'ok' => true,
                'redirect' => $redirectRoute,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'ok' => false,
                'message' => 'Sesi Firebase tidak dapat diverifikasi. Periksa project dan service account.',
            ], 401);
        }
    }
}
