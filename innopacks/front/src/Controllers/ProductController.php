<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Front\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use InnoShop\Common\Models\Product;
use InnoShop\Common\Repositories\CategoryRepo;
use InnoShop\Common\Repositories\ProductRepo;
use InnoShop\Common\Repositories\ReviewRepo;
use InnoShop\Common\Resources\ReviewListItem;
use InnoShop\Common\Resources\SkuListItem;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws Exception
     */
    public function index(Request $request): mixed
    {
        $filters  = $request->all();
        $products = ProductRepo::getInstance()->withActive()->list($filters);

        $data = [
            'products'   => $products,
            'categories' => CategoryRepo::getInstance()->getTwoLevelCategories(),
        ];

        return inno_view('products.index', $data);
    }

    /**
     * @param  Request  $request
     * @param  Product  $product
     * @return mixed
     */
    public function show(Request $request, Product $product): mixed
    {
        if (! $product->active) {
            abort(404);
        }

        $skuId = $request->get('sku_id');

        return $this->renderShow($product, $skuId);
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws Exception
     */
    public function slugShow(Request $request): mixed
    {
        $slug = $request->slug;

        // Caching data produk berdasarkan slug
        $cacheKey = "product_slug_{$slug}";
        $product = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($slug) {
            return ProductRepo::getInstance()->withActive()
                ->builder(['slug' => $slug])
                ->firstOrFail();
        });

        $skuId = $request->get('sku_id');
        return $this->renderShow($product, $skuId);
    }

    /**
     * @param  Product  $product
     * @param  $skuId
     * @return mixed
     */
    private function renderShow(Product $product, $skuId): mixed
    {
        if ($skuId) {
            $sku = Product\Sku::query()->find($skuId);
        }

        if (empty($sku)) {
            $sku = $product->masterSku;
        }

        $product->increment('viewed');
        $reviews = ReviewRepo::getInstance()->getListByProduct($product);

        $data = [
            'product'    => $product,
            'sku'        => (new SkuListItem($sku))->jsonSerialize(),
            'skus'       => SkuListItem::collection($product->skus)->jsonSerialize(),
            'variants'   => $product->variables,
            'attributes' => $product->groupedAttributes(),
            'reviews'    => ReviewListItem::collection($reviews)->jsonSerialize(),
        ];

        return inno_view('products.show', $data);
    }
}
