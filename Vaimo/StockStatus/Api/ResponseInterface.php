<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 12/10/2018
 * Time: 4:53 AM
 */
namespace Vaimo\StockStatus\Api;

interface ResponseInterface
{

    const MINIMUM_STOCK = 0;
    const VAIMO_API = 'http://server-v894.vaimo.com/__ic/testapi/index.php';

    /**
     * @param $url
     * @return array
     */
    public function getResponse($url);
}