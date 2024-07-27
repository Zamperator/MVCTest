<?php

namespace App\Lib;

const DEFAULT_LANGUAGE = 'de';

class L8N
{

    protected string $language = DEFAULT_LANGUAGE;
    protected string $lastLanguage = '';
    protected array $_strings = [];

    const string FILE_PATH = '../config/locale_%s.json';

    function __construct()
    {
        $this->language = Registry::get('language') ?? $this->language;
        $this->loadFile($this->language);
    }

    /**
     * @param string $language
     * @return void
     */
    public function loadFile(string $language): void
    {

        $filePath = sprintf(self::FILE_PATH, $language);
        if (!is_file($filePath)) {
            $filePath = sprintf(self::FILE_PATH, DEFAULT_LANGUAGE);
        }

        $this->_strings = json_decode(file_get_contents($filePath), true);
    }

    /**
     * @return array
     */
    public function getStrings(): array
    {
        return $this->_strings;
    }

    /**
     * @param string $language
     * @return void
     */
    public function setLanguage(string $language): void
    {
        if ($language === $this->lastLanguage)
            return;

        $this->language = $this->lastLanguage = $language;
        $this->loadFile($this->language);
    }

    /**
     * @param string $index
     * @return array|string|null
     */
    public function translate(string $index): string|array|null
    {
        $multipleIndex = explode('.', $index);
        if (sizeof($multipleIndex) > 1) {
            return $this->_strings[$multipleIndex[0]][$multipleIndex[1]] ?? $index;
        }

        return $this->_strings[$index] ?? $index;
    }

    /**
     * @param string $index
     * @return array|string|null
     */
    static public function _(string $index): string|array|null
    {
        $instance = new self();
        return $instance->translate($index);
    }

    /**
     * @param string $index
     * @return array|string|string[]|null
     */
    public function get(string $index)
    {
        $strings = $this->translate($index);
        if (is_array($strings)) {
            return $strings;
        }
        return preg_replace_callback('#{{([^}]+)}}#', function ($matches) {
            return $this->translate($matches[1]);
        }, $strings);
    }

    /**
     * @param string $index
     * @param array $replace
     * @return string
     */
    public function getReplace(string $index, array $replace = []): string
    {
        $translate = $this->get($index);
        if ($translate !== $index && !empty($replace)) {
            $translate = preg_replace_callback('#\[\[([^}]+)]]#', function ($matches) use($replace) {
                return $replace[$matches[1]] ?? $matches[1];
            }, $translate);
        }

        return $translate;
    }

    /**
     * @param string $section
     * @return bool
     */
    public function hasSection(string $section): bool
    {
        return isset($this->_strings[$section]);
    }

}