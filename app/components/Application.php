<?php

declare(strict_types=1);

namespace components;

use common\AbstractApplication;
use common\AbstractController;
use common\AppException;
use controllers\ErrorController;
use controllers\SubscriberController;
use repositories\SubscriberRepository;
use services\SubscriberService;
use Throwable;

class Application extends AbstractApplication
{
    // public readonly string $basePath;
    public readonly string $viewsPath;
    public readonly string $layoutsPath;
    public readonly Request $request;
    public readonly ResponseHeader $responseHeader;
    public readonly UrlManager $urlManager;
    public readonly SubscriberService $subscriberService;

    /**
     * @return array<string,array{controller:string,action:string}>
     * route map (route => [controllerClass, action])
     */
    protected function routeMap(): array
    {
        return [
            // UrlManager::ROUTE_INDEX => [
            //     'controller' => SubscriberController::class, 'action' => '',
            // ],
            UrlManager::ROUTE_SUBSCRIBER_INDEX => ['controller' => SubscriberController::class, 'action' => 'actionIndex',],
            UrlManager::ROUTE_SUBSCRIBER_VIEW => ['controller' => SubscriberController::class, 'action' => 'actionView',],
            UrlManager::ROUTE_SUBSCRIBER_CREATE => ['controller' => SubscriberController::class, 'action' => 'actionCreate',],
            UrlManager::ROUTE_SUBSCRIBER_UPDATE => ['controller' => SubscriberController::class, 'action' => 'actionUpdate',],
            UrlManager::ROUTE_SUBSCRIBER_DELETE => ['controller' => SubscriberController::class, 'action' => 'actionDelete',],
        ];
    }

    /**
     * @return array<string,array{layout:string,viewsPath:string}>
     */
    protected function controllerViewMap(): array
    {
        return [
            SubscriberController::class => [
                'layout' => $this->layoutsPath . '/main.php',
                'viewsPath' => $this->viewsPath . '/subscriber'
            ],
            ErrorController::class => [
                'layout' => $this->layoutsPath . '/main.php',
                'viewsPath' => $this->viewsPath . '/error'
            ],
        ];
    }

    /**
     *
     */
    public function __construct()
    {
        parent::__construct(
            realpath(__DIR__ . '/..'),
        );
        $this->viewsPath = $this->basePath . '/views';
        $this->layoutsPath = $this->basePath . '/views/layouts';
    }

    /**
     *
     */
    protected function init(): void
    {
        $this->request = new Request();
        $this->urlManager = new UrlManager();
    }

    private SubscriberService|null $_subscriberService = null;

    /**
     *
     */
    public function getSubscriberService(): SubscriberService
    {
        if (null === $this->_subscriberService) {
            try {
                $repository = new SubscriberRepository(
                    $this->basePath . '/db',
                    true
                );
                $repository->load();
            } catch (Throwable $th) {
                throw new AppException($th->getMessage(), 500);
            }

            $this->_subscriberService = new SubscriberService(
                $repository
            );
        }
        return $this->_subscriberService;
    }

    /**
     *
     */
    protected function createController(string $controllerClass): AbstractController
    {
        $controllerViews = $this->controllerViewMap()[$controllerClass];
        return new $controllerClass(
            $this,
            $controllerViews['layout'],
            $controllerViews['viewsPath']
        );
    }

    /**
     *
     */
    public function run(): void
    {
        try {
            // echo phpinfo();
            $this->init();
            $this->responseHeader = $this->urlManager->resolveRequest($this->request, true);
            $route = $this->routeMap()[$this->responseHeader->route];
            $controller = $this->createController($route['controller']);
            $this->responseHeader->content = $controller->runAction($route['action']);
            $this->responseHeader->send();
        } catch (AppException $e) {
            if (!isset($this->responseHeader)) {
                $this->responseHeader = new ResponseHeader();
            }
            $this->responseHeader->setStatusCode($e->getCode(), $e->getMessage());
            $this->responseHeader->content = $this->createController(ErrorController::class)
                ->runAction('ActionIndex');
            $this->responseHeader->send();
        } catch (Throwable $th) {
            throw $th;
        }
    }
}
