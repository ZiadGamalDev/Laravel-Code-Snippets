<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Interfaces\Web\DashboardInterface;

class DashboardController extends Controller
{
    public function __construct(private DashboardInterface $dashboardInterface)
    {
    }

    public function __invoke()
    {
        return $this->dashboardInterface->index();
    }
}
