<?php

/**
 * @noinspection PhpUnused
 */

namespace App\Lib;

/**
 * @uses Utils
 * @package App\Lib
 * @version 1.0
 * @since 1.0
 */
class PageSetup
{
    private array $breadCrumbs = [];
    private array $scriptFiles = [];
    private array $styleFiles = [];

    private string $pageTitle = '';
    private string $sectionTitle = '';

    /**
     * @param string $file
     * @param string $version
     * @param string $type
     * @return void
     */
    public function addStyleFile(string $file = '', string $version = '', string $type = ''): void
    {
        $this->styleFiles[] = ['file' => $file, 'version' => $version, 'type' => $type];
    }

    /**
     * @param string $file
     * @param string $version
     * @param string $section
     * @param array $options
     * @return void
     */
    public function addScriptFile(string $file = '', string $version = '', string $section = 'header', array $options = []): void
    {
        $this->scriptFiles[] = ['file' => $file, 'version' => $version, 'options' => $options, 'section' => $section];
    }

    /**
     * @param string $text
     * @param string $link
     * @param string $class
     * @return void
     */
    public function addBreadCrumb(string $text = '', string $link = '', string $class = ''): void
    {
        $this->breadCrumbs[] = ['text' => $text, 'link' => $link, 'class' => $class];
    }

    /**
     * @return array
     */
    public function getBreadCrumbs(): array
    {
        return $this->breadCrumbs;
    }

    /**
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle(string $pageTitle = ''): void
    {
        $this->pageTitle = Utils::cleanup($pageTitle);
    }

    /**
     * Extend the page title
     *
     * @param string $pageTitle
     * @return void
     */
    public function addPageTitle(string $pageTitle = ''): void
    {
        $pageTitle = Utils::cleanup($pageTitle);
        if ($this->pageTitle != '') {
            $this->pageTitle = $pageTitle . ' - ' . $this->pageTitle;
        } else {
            $this->pageTitle = $pageTitle;
        }
    }

    /**
     * @param string $pageTitle
     * @return void
     */
    public function setSectionPageTitle(string $pageTitle = ''): void
    {
        $this->sectionTitle = Utils::cleanup($pageTitle);
    }

    /**
     * @param string $file
     * @return string|int
     */
    private function getVersion(string $file = ''): string|int
    {
        return (strlen($file) > 3 && is_file('.' . $file)) ? filemtime('.' . $file) : '1.0';
    }

    /**
     * @return array
     */
    public function getViewBits(): array
    {
        $header = [];
        $footer = [];

        $templateBits = [];

        if (!empty($this->styleFiles)) {
            foreach ($this->styleFiles as $styleFile) {
                $header[$styleFile['file']] = sprintf(
                    '<link rel="stylesheet" type="text/css" href="%s%s" />',
                    $styleFile['file'],
                    $styleFile['version'] ? '?_v=' . $styleFile['version'] : ''
                );
            }
        }

        // Parse script files
        if (!empty($this->scriptFiles)) {
            foreach ($this->scriptFiles as $scriptFile) {

                $optionsArray = [];

                if (!isset($scriptFile['options']['type'])) {
                    $scriptFile['options']['type'] = 'text/javascript';
                }
                foreach ($scriptFile['options'] as $field => $value) {
                    $optionsArray[] = $field . '="' . $value . '"';
                }
                $options = implode(' ', $optionsArray);

                $version = '?_v=' . ($scriptFile['version'] ?: $this->getVersion($scriptFile['file']));

                $sectionName = $scriptFile['section']; // $header or $footer
                $$sectionName[$scriptFile['file']] = '<script src="' . $scriptFile['file'] . $version . '" ' . $options . '></script>';
            }
        }

        if (!empty($header)) {
            $templateBits['header'] = implode("\n", $header);
        }
        if (!empty($footer)) {
            $templateBits['footer'] = implode("\n", $footer);
        }

        unset($header, $footer);

        // Set the page title
        if (!empty($this->pageTitle)) {
            $pageTitle = $this->pageTitle;
            if (!empty($this->sectionTitle)) {
                $pageTitle .= ' - ' . $this->sectionTitle;
            }
        } else {
            $pageTitle = $this->sectionTitle;
        }
        $templateBits['pageTitle'] = $pageTitle;

        return $templateBits;
    }
}
