<?php
//
//namespace App\View\Components;
//
//use Closure;
//use Illuminate\Contracts\View\View;
//use Illuminate\View\Component;
//
//class DangerButton extends Component
//{
//    /**
//     * Create a new component instance.
//     */
//    public function __construct()
//    {
//        //
//    }
//
//    /**
//     * Get the view / contents that represent the component.
//     */
//    public function render(): View|Closure|string
//    {
//        return view('components.danger-button');
//    }
//}


namespace App\View\Components;

use Illuminate\View\Component;

class DangerButton extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.danger-button');
    }
}
