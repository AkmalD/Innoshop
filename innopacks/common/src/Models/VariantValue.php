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

class VariantValue extends Model
{
    protected $table = 'variant_values';

    protected $fillable = [
        'variant_id', // Foreign key yang menghubungkan ke tabel 'variants'
        'value', // Sesuaikan dengan kolom-kolom yang ada di tabel 'variant_values'
        // Kolom lainnya jika ada
    ];

    /**
     * Relasi ke model Variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }
}
