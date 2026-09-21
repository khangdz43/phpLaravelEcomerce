<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;

abstract class Controller
{
    use ApiResponse; // dùng ở đây mọi thằng kế thừa thằng này đều ko cần use trait này nữa


    
}
