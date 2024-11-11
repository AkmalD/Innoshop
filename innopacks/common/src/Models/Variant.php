<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Common\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $table = 'variants';

    protected $fillable = [
        'name', // Sesuaikan kolom-kolom ini dengan yang ada di tabel 'variants'
        'type',
        // Kolom lainnya jika ada
    ];

    // Tambahkan relasi jika dibutuhkan
}
