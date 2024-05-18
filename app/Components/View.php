<?php
/* @noinspection PhpUnused */

namespace App\Components;

use App\Lib\Registry;
use App\Lib\Utils;

/**
 *
 */
class View
{

    protected array $_variables = [];
    protected string $_controller;
    protected string $_action;
    protected string $_bodyContent;

    public string $viewPath;
    public array $section = [];
    public array $breadcrumb = [];
    public bool $doOutput = true;
    public string $pageTitle;
    public string $layout;
    public mixed $pageSetup;

    /**
     * Template constructor.
     *
     * @param string $controller
     * @param string $action
     */
    public function __construct(string $controller = '', string $action = '')
    {
        $this->setController($controller);
        $this->setAction($action);

        $this->pageSetup = Registry::get('pageSetup');
    }

    /**
     * @param string $controllerName
     * @return void
     */
    public function setController(string $controllerName): void
    {
        $this->_controller = $controllerName;
    }

    /**
     * @param string $actionName
     * @return void
     */
    public function setAction(string $actionName): void
    {
        $this->_action = $actionName;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set(string $name, mixed $value): void
    {
        $this->_variables[$name] = $value;
    }

    /**
     * @param array $toSet
     * @return void
     */
    public function setMultiple(array $toSet = []): void
    {
        if (!empty($toSet)) {
            foreach ($toSet as $name => $value) {
                if (is_array($value)) {
                    $this->setMultiple($value);
                } else {
                    $this->set($name, $value);
                }
            }
        }
    }

    /**
     * @param bool $return
     * @return string
     */
    public function renderBody(bool $return = false): string
    {
        // if we have content, then deliver it
        if (!empty($this->_bodyContent)) {
            if ($return) {
                return $this->_bodyContent;
            } else {
                echo $this->_bodyContent;
            }
        }

        return '';
    }

    /**
     * @param string $section
     * @param mixed $value
     * @return void
     */
    public function setSection(string $section, mixed $value): void
    {
        $this->section[$section] = $value;
    }

    /**
     * @param string $section
     * @return void
     */
    public function renderSection(string $section = ''): void
    {
        if (!empty($this->section[$section])) {
            echo $this->section[$section];
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getVar($name): mixed
    {
        return $this->_variables[$name] ?? null;
    }

    /**
     * @param string $path
     * @param string $sub
     * @return string
     */
    public static function UrlContent(string $path = '', string $sub = 'app'): string
    {
        $ds = DIRECTORY_SEPARATOR;

        if (str_starts_with($path, '~')) {
            $path = preg_replace('#\\\/#', $ds, $path);
            $path = preg_replace(
                '#(\\\\' . preg_quote($ds) . '){2,}#',
                $ds,
                preg_replace(
                    '#~#',
                    $_SERVER['DOCUMENT_ROOT'] . '/../' . $sub . (str_starts_with($path, '~/') ? '' : $ds),
                    $path
                )
            );
            $path = realpath($path);
        }

        return $path;
    }

    /**
     * @return void
     */
    public function render(): void
    {
        // extract($this->_variables);

        $bits = $this->pageSetup->getViewBits();
        $this->setSection('header', $bits['header'] ?? '');
        $this->setSection('footer', $bits['footer'] ?? '');

        $this->breadcrumb = $this->pageSetup->getBreadCrumbs();
        $this->pageTitle = $bits['pageTitle'];

        $ds = DIRECTORY_SEPARATOR;

        // the view path
        $defaultPath = self::UrlContent('~/Views/') . $ds . $this->_controller . $ds . $this->_action . '.php';

        // start buffering
        ob_start();

        // render page content
        include (empty($this->viewPath) || !is_file($this->viewPath)) ? $defaultPath : $this->viewPath;

        // get the body contents
        $this->_bodyContent = ob_get_clean();

        // check if we have any layout defined
        if (!empty($this->layout) && (!Utils::isAjaxCall())) {
            // we need to check the path contains app prefix (~)
            $this->layout = self::UrlContent($this->layout);
            // include the template
            include $this->layout;
        } else {
            // just output the content
            if ($this->doOutput) {
                echo $this->_bodyContent;
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->render();
        return '';
    }
}
