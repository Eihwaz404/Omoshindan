<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class DatabaseController extends Controller
{
    public function index(): View
    {
        Gate::authorize('database.manage');

        return view('admin.database.index', [
            'tables' => [
                [
                    'key' => 'tickets',
                    'label' => 'Tickets',
                    'description' => 'Inclui os tickets de suporte e todo o histórico relacionado.',
                    'count' => Ticket::count(),
                ],
            ],
        ]);
    }

    public function sanitize(Request $request): RedirectResponse
    {
        Gate::authorize('database.manage');

        $data = $request->validate([
            'table' => ['required', 'string', Rule::in(['tickets'])],
        ]);

        if ($data['table'] === 'tickets') {
            DB::table('ticket_events')->delete();
            DB::table('tickets')->delete();

            $this->resetAutoIncrement('ticket_events');
            $this->resetAutoIncrement('tickets');
        }

        return redirect()
            ->route('admin.database.index')
            ->with('status', __('Tabela Tickets sanitizada com sucesso.'));
    }

    private function resetAutoIncrement(string $table): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement(sprintf('ALTER TABLE `%s` AUTO_INCREMENT = 1', $table));

            return;
        }

        if ($driver === 'sqlite') {
            DB::statement("DELETE FROM sqlite_sequence WHERE name = ?", [$table]);

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(sprintf('ALTER SEQUENCE %s_id_seq RESTART WITH 1', $table));
        }
    }
}
