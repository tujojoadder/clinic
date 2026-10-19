<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (\DomainException $exception, $request) {
            if ($request->expectsJson()) return response()->json(['message' => $exception->getMessage()], 422);
            return back()->withInput()->withErrors(['workflow' => $exception->getMessage()]);
        });
        $this->renderable(function (\Illuminate\Database\QueryException $exception, $request) {
            if ($request->method() !== 'GET' && (string) $exception->getCode() === '23000') {
                return back()->withInput()->withErrors(['database' => 'ডুপ্লিকেট বা অন্য রেকর্ডে ব্যবহৃত তথ্য। সংশ্লিষ্ট রেকর্ড ডিলেট না করে নিষ্ক্রিয় করুন।']);
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
