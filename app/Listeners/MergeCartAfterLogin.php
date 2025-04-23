<?php
// app/Listeners/MergeCartAfterLogin.php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\SyncsCart;

class MergeCartAfterLogin
{
    use SyncsCart;

    public function handle(Login $event): void
    {
        $this->mergeDBCartIntoSession();          // đọc DB & gộp
        $this->syncCartToDB(session('cart', [])); // ghi ngược DB (đề phòng guest đã chọn sp)
    }
}
