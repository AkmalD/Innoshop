use Illuminate\Support\Facades\DB;
use InnoShop\Common\Models\Order;
use InnoShop\Common\Models\Product;
use InnoShop\Common\Models\CartItem;

class CheckoutService {
    public function processCheckout($customerData) {
        try {
            DB::transaction(function () use ($customerData) {
                // Ambil semua item di keranjang pelanggan
                $cartItems = CartItem::where('customer_id', $customerData->id)->get();

                // Langkah 1: Hitung Total dan Buat Pesanan
                $total = 0;
                $order = Order::create([
                    'customer_id' => $customerData->id,
                    'total' => $total,
                    'currency_code' => 'USD', // Misalnya, Anda dapat menentukan mata uang
                    'status' => 'pending',
                ]);

                // Langkah 2: Proses setiap item di keranjang
                foreach ($cartItems as $cartItem) {
                    $product = $cartItem->product;

                    // Cek ketersediaan stok
                    if ($product->stock < $cartItem->quantity) {
                        throw new \Exception("Stok tidak mencukupi untuk produk " . $product->name);
                    }

                    // Kurangi stok produk
                    $product->decrement('stock', $cartItem->quantity);

                    // Tambahkan item ke dalam pesanan
                    $order->items()->create([
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->productSku->price,
                    ]);

                    // Tambahkan ke total
                    $total += $cartItem->getSubtotalAttribute();
                }

                // Perbarui total pesanan setelah semua item ditambahkan
                $order->update(['total' => $total]);

                // Langkah 3: Hapus item dari keranjang setelah checkout berhasil
                CartItem::where('customer_id', $customerData->id)->delete();

                // Langkah 4: Kirim Notifikasi Pesanan Baru
                $order->notifyNewOrder();
            });
        } catch (\Exception $e) {
            // Jika terjadi error, rollback otomatis
            \Log::error('Checkout gagal: ' . $e->getMessage());
            throw $e; // Atau berikan umpan balik kepada pengguna
        }
    }
}
