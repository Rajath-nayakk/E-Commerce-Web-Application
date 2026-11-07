<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\Product;
use App\Models\Category;

/**
 * Class HomeController
 * 
 * Manages the public storefront landing page, hero banner,
 * category highlights, and featured hardware showcase.
 */
class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $categories = Category::allWithCount();
        $featuredProducts = Product::getFeatured(8);
        $deals = Product::getDeals(4);

        $this->view('home/index', [
            'title'            => 'NovaTech Gear | Premium Indian Electronics & Hardware Store',
            'categories'       => $categories,
            'featuredProducts' => $featuredProducts,
            'deals'            => $deals
        ]);
    }
}

