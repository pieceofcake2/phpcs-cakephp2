<?php

use Cake\Controller\Controller;
use Cake\Network\CakeRequest;

/**
 * A controller with a use statement for a class that nothing names
 */
class UnusedController extends Controller
{
    /**
     * Index action
     *
     * @return null|string
     */
    public function index()
    {
        return null;
    }
}
