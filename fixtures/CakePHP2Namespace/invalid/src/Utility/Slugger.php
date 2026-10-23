<?php
namespace App\Utility;

/**
 * A class in a file that is not named after it
 */
class Slug
{
    /**
     * Make a slug
     *
     * @param string $title Title
     * @return string
     */
    public function make($title)
    {
        return strtolower($title);
    }
}
