<?php

use Cake\Controller\Controller;
use Cake\Network\CakeResponse;

/**
 * Pages Controller of a global-class application that imports the namespaced core classes
 */
class PagesController extends Controller
{
    /**
     * Displays a view
     *
     * @param string ...$path Path segments
     * @return CakeResponse|null
     */
    public function display(...$path)
    {
        return $this->render(implode('/', $path));
    }
}
