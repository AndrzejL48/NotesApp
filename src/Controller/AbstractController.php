<?php

declare(strict_types=1);

namespace App\Controller;

use App\View;
use App\Request;
use App\Session;
use App\Validator\ValidatorManager;
use App\Exception\StorageException;
use App\Exception\NotFoundException;

abstract class AbstractController
{
    private View $view;

    protected Request $request;
    protected ValidatorManager $validator;
    protected Session $session;
    protected array $viewParams = [];
    protected string $defaultAction;

    public function __construct(Request $request)
    {
        $this->view = new View();
        $this->request = $request;
        $this->validator = new ValidatorManager();
        $this->session = new Session();
    }

    final public function run()
    {
        try {
            $page = $this->getAction();
            $action = $page . 'Action';
    
            if (!method_exists($this, $action)) {
                $action = str_replace($page, $this->defaultAction, $action);
                $page = $this->defaultAction;
            }

            $this->$action();
            $this->view->render(
                $page,
                $this->viewParams
            );
        } catch(StorageException $e) {
            $this->view->render(
                'error',
                [
                    'message' => $e->getMessage()
                ]
            );
        } catch (NotFoundException $e) {
            $redirectParams = [
                'error' => 'noteNotFound'
            ];
            $this->redirect($redirectParams);
            exit;
        }
    }

    private function getAction(): string
    {
        return $this->request->getParam('action', $this->defaultAction);
    }

    final protected function redirect(array $params = [], string $location = '/'): void
    {
        $formattedParams = http_build_query($params);
        $paramsUrlPart = !empty($formattedParams) ? '?' . $formattedParams : '';
        $redirectUrl = $location . $paramsUrlPart;

        header("Location: $redirectUrl");
        exit;
    }
}
