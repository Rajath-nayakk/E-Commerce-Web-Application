<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Cart;
use App\Models\Product;

/**
 * Class CartController
 * 
 * Manages shopping cart operations for both guest visitors (session-based)
 * and authenticated members (database-backed), with AJAX support.
 */
class CartController extends Controller
{
    /**
     * Display Shopping Cart page
     */
    public function index(Request $request): void
    {
        $items = Cart::getItems();
        $totals = Cart::getTotals();

        $this->view('cart/index', [
            'title'  => 'Your Shopping Cart',
            'items'  => $items,
            'totals' => $totals
        ]);
    }

    /**
     * Add product to cart
     */
    public function add(Request $request): void
    {
        $this->validateCsrf($request);

        $productId = (int) $request->post('product_id', 0);
        $quantity = max(1, (int) $request->post('quantity', 1));

        $result = Cart::addItem($productId, $quantity);

        if ($request->isAjax() || $request->isJson()) {
            $totals = Cart::getTotals();
            $result['cart_count'] = $totals['total_quantity'];
            $result['totals'] = $totals;
            $this->json($result, $result['success'] ? 200 : 400);
            return;
        }

        if ($result['success']) {
            Session::flash('success', $result['message']);
        } else {
            Session::flash('danger', $result['message']);
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/cart');
    }

    /**
     * Update quantity of an item in the cart
     */
    public function update(Request $request): void
    {
        $this->validateCsrf($request);

        $productId = (int) $request->post('product_id', 0);
        $quantity = (int) $request->post('quantity', 1);

        $result = Cart::updateQuantity($productId, $quantity);

        if ($request->isAjax() || $request->isJson()) {
            $totals = Cart::getTotals();
            $result['cart_count'] = $totals['total_quantity'];
            $result['totals'] = $totals;

            $product = Product::find($productId);
            if ($product) {
                $result['item_subtotal'] = format_price(((float) $product['price']) * $quantity);
            }

            $this->json($result, $result['success'] ? 200 : 400);
            return;
        }

        if (!$result['success']) {
            Session::flash('danger', $result['message']);
        } else {
            Session::flash('success', 'Cart updated successfully.');
        }

        $this->redirect('/cart');
    }

    /**
     * Remove an item from the cart
     */
    public function remove(Request $request): void
    {
        $this->validateCsrf($request);

        $productId = (int) $request->post('product_id', 0);
        $result = Cart::removeItem($productId);

        if ($request->isAjax() || $request->isJson()) {
            $totals = Cart::getTotals();
            $result['cart_count'] = $totals['total_quantity'];
            $result['totals'] = $totals;
            $this->json($result, 200);
            return;
        }

        Session::flash('info', 'Item removed from your cart.');
        $this->redirect('/cart');
    }

    /**
     * Clear all items in the cart
     */
    public function clear(Request $request): void
    {
        $this->validateCsrf($request);
        Cart::clear();

        Session::flash('info', 'Your shopping cart has been cleared.');
        $this->redirect('/cart');
    }
}

