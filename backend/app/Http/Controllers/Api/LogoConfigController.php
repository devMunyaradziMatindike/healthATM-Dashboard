<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class LogoConfigController extends Controller
{
    public function show()
    {
        return response()->json([
            'bmiStartLogoSwitch' => (int) (config('healthatm.logo.start.switch') ?? 0),
            'bmiStartLogoImage' => (string) (config('healthatm.logo.start.image') ?? ''),
            'bmiCompanyLogoSwitch' => (int) (config('healthatm.logo.company.switch') ?? 0),
            'bmiCompanyLogoImage' => (string) (config('healthatm.logo.company.image') ?? ''),
            'bmiMascotLogoSwitch' => (int) (config('healthatm.logo.mascot.switch') ?? 0),
            'bmiMascotLogoImage' => (string) (config('healthatm.logo.mascot.image') ?? ''),
        ]);
    }
}
