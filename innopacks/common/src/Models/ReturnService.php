use Illuminate\Support\Facades\DB;
use InnoShop\Common\Models\OrderReturn;
use InnoShop\Common\Models\Order;
use InnoShop\Common\Models\Product;
use InnoShop\Common\Models\Order\Item;

class ReturnService {
    public function processReturn($returnData) {
        try {
            DB::transaction(function () use ($returnData) {
                // Langkah 1: Buat entri baru di tabel OrderReturn
                $orderReturn = OrderReturn::create([
                    'customer_id' => $returnData['customer_id'],
                    'order_id' => $returnData['order_id'],
                    'order_number' => $returnData['order_number'],
                    'number' => $returnData['return_number'],
                    'name' => $returnData['name'],
                    'email' => $returnData['email'],
                    'telephone' => $returnData['telephone'],
                ]);

                // Langkah 2: Ambil semua item yang di-return berdasarkan order_id
                $orderItems = Item::where('order_id', $returnData['order_id'])->get();

                foreach ($orderItems as $item) {
                    // Tambahkan stok kembali ke produk
                    $product = Product::find($item->product_id);
                    $product->increment('stock', $item->quantity);

                    // Update status item jika perlu, misalnya status "returned"
                    $item->update(['status' => 'returned']);
                }

                // Langkah 3: Perbarui status Order untuk menunjukkan bahwa pengembalian telah diproses
                $order = Order::find($returnData['order_id']);
                $order->update(['status' => 'partially_returned']); // atau status lain yang sesuai
            });
        } catch (\Exception $e) {
            // Jika terjadi error, rollback otomatis
            \Log::error('Return process failed: ' . $e->getMessage());
            throw $e; // Atau berikan umpan balik kepada pengguna
        }
    }
}
