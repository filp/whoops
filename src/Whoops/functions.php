<?php
/**
 * Created by PhpStorm.
 * User: staabm
 * Date: 21.11.2015
 * Time: 16:08
 */

namespace Whoops;

function isAjaxRequest()
{
    return (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}