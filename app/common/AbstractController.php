<?php

declare(strict_types=1);

namespace common;

use Exception;
use Throwable;

abstract class AbstractController
{
    public function __construct(
        protected AbstractApplication $app,
        protected string $layout,
        protected string $viewsPath
    ) {
    }

    /**
     * @throws AppException with a 404 status code if the action cannot be found.
     * @return string the rendered content.
     */
    public function runAction(string $action): string
    {
        $action = [$this, $action];
        if (is_callable($action)) {
            return $action();
        } else {
            throw new AppException('Page not found.', 404);
        }
        return '';
    }

    /**
     *
     */
    protected function render($view, $params = []): string
    {
        $content = $this->renderPhpFile($this->viewsPath . '/' . $view . '.php', $params);
        return $this->renderPhpFile($this->layout, ['content' => $content]);
    }

    /**
     *
     */
    protected function renderPhpFile(string $_file_, array $_params_ = []): string
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;
            return ob_get_clean();
        } catch (Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }
}
