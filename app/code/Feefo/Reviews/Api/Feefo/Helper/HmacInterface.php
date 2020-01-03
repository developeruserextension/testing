<?php

namespace Feefo\Reviews\Api\Feefo\Helper;

/**
 * Interface HmacInterface
 *
 * Encapsulates logic of generating hmac tokens
 */
interface HmacInterface
{
    /**
     * Generate hmac token
     *
     * @return string
     */
    public function get();
}