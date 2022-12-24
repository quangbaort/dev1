<?php

namespace App\Http\Controllers\Web;

use App\Services\SafetyCheckService;
use Illuminate\Http\Request;

class SafetyCheck
{
    /**
     * @var \App\Services\SafetyCheckService
     */
    protected $safetyCheckService;

    /**
     * @param \App\Services\SafetyCheckService $safetyCheckService
     */
    public function __construct(SafetyCheckService $safetyCheckService)
    {
        $this->safetyCheckService = $safetyCheckService;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $safetyCheckId
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function answer(Request $request, $safetyCheckId)
    {
        $this->safetyCheckService->answer(
            $safetyCheckId,
            $request->input('uid'),
            $request->input('nat'),
            $request->input('sts'),
        );

        return view('notify');
    }
}
