<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanAttendanceRequest;
use App\Models\SchoolSetting;
use App\Services\AttendanceValidationService;
use App\Services\QrTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly QrTokenService $tokens,
        private readonly AttendanceValidationService $validation,
    ) {
    }

    public function scanPage(): View
    {
        $school = SchoolSetting::query()->firstOrFail();
        $token = $this->tokens->issue($school);

        return view('attendance.scan', [
            'school' => $school,
            'activeUser' => auth()->user(),
            'demoQrToken' => $token->plain_token,
            'hideNav' => true,
        ]);
    }

    public function scan(ScanAttendanceRequest $request): JsonResponse
    {
        $result = $this->validation->scan(
            user: $request->user(),
            rawToken: $request->string('qr_token')->toString(),
            latitude: (float) $request->input('latitude'),
            longitude: (float) $request->input('longitude'),
            accuracy: $request->filled('accuracy') ? (float) $request->input('accuracy') : null,
        );

        return response()->json($result, $result['ok'] ? 200 : 422);
    }
}
