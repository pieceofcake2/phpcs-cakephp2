<?php
/**
 * Bad style
 */
class BadStyle
{
    /**
     * Not camel caps outside controllers, shells and behaviors
     *
     * @return int
     */
    public function do_something()
    {
        $a = 1;
        $bcd    = 2;

        return $a + $bcd;
    }
}
