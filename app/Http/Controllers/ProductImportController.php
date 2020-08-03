<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductImportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $products = [];

        foreach ($this->getRows($request->file) as $line => $row) {
            if (count($row) === 4) {
                $products[] = [
                    'name' => trim($row[0]),
                    'price' => trim($row[1]),
                    'sku' => trim($row[2]),
                    'description' => trim($row[3]),
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }

            if ($line % 1000 === 0) {
                Product::insert($products);

                $products = [];
            }
        }

        if (!empty($products)) {
            Product::insert($products);
        }

        return response()->json([
            'message' => 'Products imported successfully.'
        ], 200);
    }

    private function getRows($file)
    {
        $handle = $file->openFile('r');

        // skip the headers row
        $handle->current();

        while (!$handle->eof()) {
            yield $handle->fgetcsv();
        }
    }
}
