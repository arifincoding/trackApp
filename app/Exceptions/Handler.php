<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    protected array $exceptionMap = [
        NotFoundHttpException::class => [
            'status' => 404,
            'message' => 'error 404',
            'error' => 'permintaan gagal, resource tidak ditemukan'
        ],
        ModelNotFoundException::class => [
            'status' => 404,
            'message' => 'error 404',
            'error' => 'permintaan gagal, data tidak ditemukan'
        ],
        ValidationException::class => [
            'status' => 422,
            'message' => 'permintaan gagal, kesalahan validasi',
            'errors' => 'kesalahan dalam input'
        ]
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        $exceptionClass = get_class($exception);

        if ($this->shouldntReport($exception)) {
            if ($exceptionClass == ValidationException::class) {
                return;
            } else if ($exceptionClass == NotFoundHttpException::class) {
                Log::warning("resource not found");
            } else if ($exceptionClass == ModelNotFoundException::class) {
                return;
            } else if ($exceptionClass == AuthorizationException::class) {
                Log::warning($exception->getMessage(), ['exception' => $exception]);
            } else {
                Log::warning($exception->getMessage(), ['exception' => $exception]);
            }
            return;
        }

        if ($exceptionClass == QueryException::class) {
            Log::stack(["errorLog", "daily"])->emergency($exception->getMessage(), ["exception" => $exception]);
        } else {
            Log::stack(["errorLog", "daily"])->error($exception->getMessage(), ["at" => $exception->getFile()]);
        }
        // parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $exceptionClass = get_class($exception);
        $defidation = $this->exceptionMap[$exceptionClass] ?? [
            'status' => 500,
            'message' => 'internal server error',
            'error' => 'something went wrong'
        ];
        if ($defidation['status'] == 422) {
            $defidation['errors'] = $exception->errors();
        }
        if (env("APP_DEBUG") === true) {
            if ($defidation['status'] == 500) {
                $defidation['error'] = $exception->getMessage();
            }
        }
        return response()->json($defidation, $defidation['status']);
    }
}