<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Models\Order;

/**
 * Class OrderController
 * 
 * Manages customer order history ("My Orders") and live status tracking timeline.
 */
class OrderController extends Controller
{
    /**
     * Display list of customer's previous orders
     */
    public function index(Request $request): void
    {
        $userId = (int) Auth::id();
        $orders = Order::getUserOrders($userId);

        $this->view('orders/index', [
            'title'  => 'My Orders',
            'orders' => $orders
        ]);
    }

    /**
     * Display order detail and chronological tracking timeline
     */
    public function show(Request $request, string $id): void
    {
        $orderId = (int) $id;
        $userId = Auth::isAdmin() ? null : (int) Auth::id();

        $order = Order::findWithDetails($orderId, $userId);

        if (!$order) {
            Session::flash('danger', 'Order not found or you do not have permission to view it.');
            $this->redirect('/orders');
        }

        $this->view('orders/show', [
            'title' => "Track Order #{$orderId}",
            'order' => $order
        ]);
    }
}

