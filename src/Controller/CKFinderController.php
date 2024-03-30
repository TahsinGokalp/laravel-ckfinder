<?php

namespace CKSource\CKFinderBridge\Controller;

use CKSource\CKFinder\CKFinder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Controller for handling requests to CKFinder connector.
 */
class CKFinderController extends Controller
{
    /**
     * Use custom middleware to handle custom authentication and redirects.
     */
    public function __construct()
    {
        $authenticationMiddleware = config('ckfinder.authentication');

        if (! is_callable($authenticationMiddleware)) {
            if (isset($authenticationMiddleware) && is_string($authenticationMiddleware)) {
                $this->middleware($authenticationMiddleware);
            } else {
                $this->middleware(\CKSource\CKFinderBridge\CKFinderMiddleware::class);
            }
        }
    }

    /**
     * Action that handles all CKFinder requests.
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function requestAction(ContainerInterface $container, Request $request)
    {
        /** @var CKFinder $connector */
        $connector = $container->get('ckfinder.connector');

        // If debug mode is enabled then do not catch exceptions and pass them directly to Laravel.
        $enableDebugMode = config('ckfinder.debug');

        return $connector->handle($request, HttpKernelInterface::MASTER_REQUEST, ! $enableDebugMode);
    }

    /**
     * Action that displays CKFinder browser.
     *
     * @return string
     */
    public function browserAction(ContainerInterface $container, Request $request)
    {
        return view('ckfinder::browser');
    }
}
