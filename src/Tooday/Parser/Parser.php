<?php

namespace Tooday\Parser;

/**
 * <p>
 * Simple single unit parser for a so called "ride post"
 * </p>
 *
 * @package Tooday\Parser
 * @author Ivo Hradek <ivohradek@gmail.com>
 */
interface Parser
{
    /**
     * <p>Parse specific post</p>
     *
     * @param string $post
     * @return mixed
     */
    public function parse($post);
}
