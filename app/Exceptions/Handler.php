<?php

namespace App\Exceptions;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Router;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        if (is_callable($reportCallable = [$e, 'report'])) {
            return $this->container->call($reportCallable);
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $e;
        }

        $logger->error(
            $e->getMessage(),
            array_merge(
                $this->exceptionContext($e),
                $this->context()
            )
        );
    }

    /**
     * Get the default exception context variables for logging.
     *
     * @param  \Exception  $e
     * @return array
     */
    protected function exceptionContext(Exception $e)
    {
        return [
            'message'   => $e->getMessage(),
            'exception' => get_class($e) . '[' . $e->getCode() . ']',
            'file'      => str_replace(base_path() . DIRECTORY_SEPARATOR, '', $e->getFile()) . ':' . $e->getLine(),
            'trace'     => $this->getAppTrace($e),
        ];
    }

    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context()
    {
        try {
            $start_time = Carbon::createFromTimestamp(LARAVEL_START);
            $context = [
                'time_elapsed'         => now()->shortAbsoluteDiffForHumans($start_time, 3),
                'seconds_elapsed'      => now()->floatDiffInSeconds($start_time),
                'memory_peak_usage_mb' => memory_get_peak_usage() / 1024 / 1024,
                'php_sapi_name'        => php_sapi_name(),
                'user_process'         => (
                        function_exists('posix_getpwuid') ?
                        posix_getpwuid(posix_geteuid())['name'] : // Linux
                        getenv('USERNAME') // Windows
                    ),
            ];

            if (Auth::check()) {
                $context['user_id']    = Auth::id();
                $context['user_email'] = Auth::user()->email;
            }

            if ($context['php_sapi_name'] == 'cli') {
                $context['cmd_console'] = $this->getCommandFromConsole();
            }

            if ($context['php_sapi_name'] != 'cli') {
                $context['user_agent']     = request()->header('user-agent');
                $context['request_method'] = request()->method();
                $context['request_url']    = request()->fullUrl();
                $context['request_ip']     = request()->ip();
            }

            if (request()->fullUrl() != url()->previous()) {
                $context['previous_url'] = url()->previous();
            }

            $request_data = request()->except($this->dontFlash);

            if (!empty($request_data)) {
                $context['request_data'] = $request_data;
            }

            return $context;
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Render an exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $e)
    {
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($e instanceof Responsable) {
            return $e->toResponse($request);
        }

        $e = $this->prepareException($e);

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        return $request->expectsJson() || $request->isApiRoute()
                        ? $this->prepareJsonResponse($request, $e)
                        : $this->prepareResponse($request, $e);
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  \Exception  $e
     * @return \Exception
     */
    protected function prepareException(Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException('Resource not found', $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new AccessDeniedHttpException($e->getMessage(), $e);
        } elseif ($e instanceof TokenMismatchException) {
            $e = new HttpException(419, $e->getMessage(), $e);
        } elseif ($e instanceof SuspiciousOperationException) {
            $e = new NotFoundHttpException('Bad hostname provided.', $e);
        }

        return $e;
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson() || $request->isApiRoute()
                    ? response()->json(['message' => $exception->getMessage()], 401)
                    : redirect()->guest(route('login'));
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        return $request->expectsJson() || $request->isApiRoute()
                    ? $this->invalidJson($request, $e)
                    : $this->invalid($request, $e);
    }

    /**
     * Convert the given exception to an array.
     *
     * @param  \Exception  $e
     * @return array
     */
    protected function convertExceptionToArray(Exception $e)
    {
        return config('app.debug')
            ? array_merge(
                $this->exceptionContext($e),
                $this->context()
            )
            : [
                'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
            ];
    }

    /**
     * @return string
     */
    public function getCommandFromConsole()
    {
        global $argv; // $_SERVER['argv']

        if (isset($argv) && count($argv)) {
            return implode(' ', $argv);
        }
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public static function jsonEncodePretty($data)
    {
        return str_replace(
            ['\\\\\\', '\\\\', '\r\n', '\n', '\"'],
            ['\\\\', '\\', PHP_EOL, PHP_EOL, '"'],
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Remove all lines no reference to App from getTraceAsString
     *
     * @param \Throwable $e Throwable Instance
     *
     * @return array
     */
    public function getAppTrace(Throwable $e)
    {
        $trace = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $e->getTraceAsString());
        $lines = explode(PHP_EOL, $trace);

        // On windows
        if (empty($lines) || count($lines) == 1) {
            $lines = explode("\n", $trace);
        }

        // Only get lines that not contains vendor/
        return array_values(preg_grep('/vendor\//i', $lines, PREG_GREP_INVERT));
    }
}
