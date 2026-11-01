<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Cart;
use App\Models\Order;
use Exception;

/**
 * Class CheckoutController
 * 
 * Manages checkout flows, shipping address validation, mock payment simulation,
 * ACID database stock deductions, and order confirmation.
 */
class CheckoutController extends Controller
{
    /**
     * Show Checkout Page
     */
    public function index(Request $request): void
    {
        $items = Cart::getItems();
        if (empty($items)) {
            Session::flash('warning', 'Your shopping cart is empty. Add products before checking out.');
            $this->redirect('/products');
        }

        $totals = Cart::getTotals();
        $user = Auth::user();

        $this->view('checkout/index', [
            'title'  => 'Secure Checkout',
            'items'  => $items,
            'totals' => $totals,
            'user'   => $user
        ]);
    }

    /**
     * Process Order Submission
     */
    public function process(Request $request): void
    {
        $this->validateCsrf($request);

        $items = Cart::getItems();
        if (empty($items)) {
            Session::flash('danger', 'Your cart is empty.');
            $this->redirect('/cart');
        }

        $totals = Cart::getTotals();
        $userId = (int) Auth::id();

        // 1. Validate Shipping & Payment Inputs
        $shippingName = trim((string) $request->post('shipping_name', ''));
        $shippingAddress = trim((string) $request->post('shipping_address', ''));
        $shippingPhone = trim((string) $request->post('shipping_phone', ''));
        $paymentMethod = strtolower(trim((string) $request->post('payment_method', 'cod')));

        $this->validate([
            'shipping_name'    => $shippingName,
            'shipping_address' => $shippingAddress,
            'shipping_phone'   => $shippingPhone,
            'payment_method'   => $paymentMethod
        ], [
            'shipping_name'    => 'required|min:2|max:100',
            'shipping_address' => 'required|min:5',
            'shipping_phone'   => 'required|min:7|max:30',
            'payment_method'   => 'required|in:cod,card'
        ], $request, '/checkout');

        // 2. Mock Card Payment Simulator
        if ($paymentMethod === 'card') {
            $cardNumber = preg_replace('/\s+/', '', (string) $request->post('card_number', ''));
            $cardExpiry = trim((string) $request->post('card_expiry', ''));
            $cardCvv = trim((string) $request->post('card_cvv', ''));

            if (empty($cardNumber) || empty($cardExpiry) || empty($cardCvv)) {
                Session::flash('danger', 'Please enter complete card details for payment simulation.');
                $this->redirect('/checkout');
            }

            // Test Simulation Rule: card ending in '0000' triggers decline simulation
            if (str_ends_with($cardNumber, '0000')) {
                Session::flash('danger', 'Payment Declined (Mock Simulator): Card ending in 0000 was rejected by the payment gateway. Try any other test card digits (e.g. 4242 4242 4242 4242).');
                $this->redirect('/checkout');
            }
        }

        // 3. Atomically Deduct Stock & Create Order in DB Transaction
        try {
            $orderId = Order::createOrder(
                $userId,
                [
                    'name'    => $shippingName,
                    'address' => $shippingAddress,
                    'phone'   => $shippingPhone
                ],
                $paymentMethod,
                $items,
                $totals['total']
            );

            Session::flash('success', "Order #{$orderId} placed successfully! Thank you for your purchase.");
            $this->redirect("/orders/confirmation/{$orderId}");
        } catch (Exception $e) {
            Session::flash('danger', "Checkout could not be completed: " . $e->getMessage());
            $this->redirect('/cart');
        }
    }

    /**
     * Order Confirmation Page
     */
    public function confirmation(Request $request, string $id): void
    {
        $orderId = (int) $id;
        $order = Order::findWithDetails($orderId, Auth::isAdmin() ? null : (int) Auth::id());

        if (!$order) {
            Session::flash('danger', 'Order not found or permission denied.');
            $this->redirect('/');
        }

        $this->view('checkout/confirmation', [
            'title' => "Order Confirmation #{$orderId}",
            'order' => $order
        ]);
    }
}

