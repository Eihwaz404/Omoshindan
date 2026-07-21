<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $queueRefreshIntervalSeconds = SystemSetting::integer('queue_refresh_interval_seconds', 15);

        SystemSetting::put('queue_refresh_interval_seconds', $queueRefreshIntervalSeconds);

        return view('admin.settings.index', [
            'queueRefreshIntervalSeconds' => $queueRefreshIntervalSeconds,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'queue_refresh_interval_seconds' => ['required', 'integer', 'min:1', 'max:300'],
        ]);

        SystemSetting::put('queue_refresh_interval_seconds', $data['queue_refresh_interval_seconds']);

        return back()->with('status', __('Configurações da fila atualizadas com sucesso.'));
    }
}
