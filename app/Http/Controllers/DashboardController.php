<?php namespace Igestis\Http\Controllers;

use Igestis\Http\Requests;
use Igestis\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return "dashboard";
    }
}
