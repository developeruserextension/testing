<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Helper;


class Mimetypes
{
    /**
     * @var self
     */
    protected static $instance;

    /**
     * Returns an associative array with extension => mimetype mappings.
     *
     * @return array An associative array with extension => mimetype mappings.
     */
    public function getMimeTypes()
    {
        return [
            'pdf' => 'application/pdf',
        ];
    }

    /**
     * Get a singleton instance of the class
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get a mimetype value from a file extension
     *
     * @param string $extension File extension
     *
     * @return string|null
     *
     */
    public function fromExtension($extension)
    {
        $mimetypes = $this->getMimeTypes();

        return isset($mimetypes[$extension]) ? $mimetypes[$extension] : null;
    }

    /**
     * Get a mimetype from a filename
     *
     * @param string $filename Filename to generate a mimetype from
     *
     * @return string|null
     */
    public function fromFilename($filename)
    {
        return $this->fromExtension(pathinfo($filename, PATHINFO_EXTENSION));
    }
}