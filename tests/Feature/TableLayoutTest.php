<?php

namespace Tests\Feature;

use DOMDocument;
use DOMElement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * Verifica que todas las tablas del sistema "cuadren":
 *  - cada fila tiene la misma cantidad de columnas que el encabezado,
 *  - los encabezados y las celdas usan la misma alineación (centro / derecha).
 */
class TableLayoutTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();

        // Datos reales para que las tablas tengan filas con contenido.
        $admin = $this->userWithRole('admin');

        $this->makeProduct(['name' => 'Producto Con Stock', 'stock' => 3, 'stock_min' => 5]);
        $otro = $this->makeProduct(['name' => 'Producto Sano', 'stock' => 50, 'stock_min' => 5]);

        $this->makeMovement($otro, $admin, \App\Models\Movement::ENTRADA, 50);
        $this->makeMovement($otro, $admin, \App\Models\Movement::SALIDA, 10);
    }

    /** Páginas del sistema que contienen tablas. */
    private function pages(): array
    {
        return [
            'dashboard' => route('dashboard'),
            'productos' => route('products.index'),
            'categorias' => route('categories.index'),
            'entradas' => route('entradas.index'),
            'salidas' => route('salidas.index'),
            'movimientos' => route('movimientos.index'),
            'usuarios' => route('usuarios.index'),
            'reportes' => route('reportes.index'),
        ];
    }

    public function test_every_table_has_matching_header_and_body_columns(): void
    {
        $admin = $this->userWithRole('admin');
        $problems = [];

        foreach ($this->pages() as $name => $url) {
            $html = $this->actingAs($admin)->get($url)->assertOk()->getContent();

            foreach ($this->tables($html) as $index => $table) {
                $header = $this->headerCells($table);

                if ($header['columns'] === 0) {
                    $problems[] = "{$name}: tabla #{$index} sin encabezado";
                    continue;
                }

                foreach ($this->bodyRows($table) as $row => $cells) {
                    $total = array_sum(array_map(fn ($cell) => max(1, (int) $cell->getAttribute('colspan')), $cells));

                    if ($total !== $header['columns']) {
                        $problems[] = sprintf(
                            '%s: tabla #%d, fila %d tiene %d columnas y el encabezado %d',
                            $name, $index, $row, $total, $header['columns']
                        );
                    }
                }
            }
        }

        $this->assertSame([], $problems, "Tablas desalineadas:\n".implode("\n", $problems));
    }

    public function test_headers_and_cells_use_the_same_alignment(): void
    {
        $admin = $this->userWithRole('admin');
        $problems = [];

        foreach ($this->pages() as $name => $url) {
            $html = $this->actingAs($admin)->get($url)->assertOk()->getContent();

            foreach ($this->tables($html) as $index => $table) {
                $headers = $this->headerCells($table)['cells'];

                foreach ($this->bodyRows($table) as $row => $cells) {
                    foreach ($cells as $column => $cell) {
                        // Las celdas combinadas (colspan) son estados vacíos: ya van centradas por CSS.
                        if ($cell->hasAttribute('colspan') || ! isset($headers[$column])) {
                            continue;
                        }

                        $headerAlign = $this->alignment($headers[$column]);
                        $cellAlign = $this->alignment($cell);

                        if ($headerAlign !== $cellAlign) {
                            $problems[] = sprintf(
                                '%s: tabla #%d, fila %d, columna %d → encabezado "%s" vs celda "%s"',
                                $name, $index, $row, $column,
                                $headers[$column]->textContent, $cell->textContent
                            );
                        }
                    }
                }
            }
        }

        $this->assertSame([], $problems, "Alineaciones distintas entre encabezado y celda:\n".implode("\n", $problems));
    }

    /** @return array<int, DOMElement> */
    private function tables(string $html): array
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOERROR);
        libxml_clear_errors();

        return iterator_to_array($document->getElementsByTagName('table')->getIterator());
    }

    /** @return array{columns:int, cells:array<int, DOMElement>} */
    private function headerCells(DOMElement $table): array
    {
        $headers = $table->getElementsByTagName('th');

        if ($headers->length === 0) {
            return ['columns' => 0, 'cells' => []];
        }

        $firstRow = $headers->item(0)->parentNode;
        $cells = [];
        $columns = 0;

        foreach ($firstRow->childNodes as $child) {
            if ($child instanceof DOMElement && $child->tagName === 'th') {
                $cells[] = $child;
                $columns += max(1, (int) $child->getAttribute('colspan'));
            }
        }

        return ['columns' => $columns, 'cells' => $cells];
    }

    /** @return array<int, array<int, DOMElement>> */
    private function bodyRows(DOMElement $table): array
    {
        $rows = [];

        foreach ($table->getElementsByTagName('tr') as $row) {
            if ($row->parentNode->nodeName !== 'tbody') {
                continue;
            }

            $cells = [];
            foreach ($row->childNodes as $child) {
                if ($child instanceof DOMElement && $child->tagName === 'td') {
                    $cells[] = $child;
                }
            }

            if ($cells !== []) {
                $rows[] = $cells;
            }
        }

        return $rows;
    }

    /** Devuelve la alineación usada por una celda (vacío si es la por defecto). */
    private function alignment(DOMElement $cell): string
    {
        foreach (explode(' ', $cell->getAttribute('class')) as $class) {
            if (in_array($class, ['text-start', 'text-center', 'text-end'], true)) {
                return $class;
            }
        }

        return '';
    }
}
