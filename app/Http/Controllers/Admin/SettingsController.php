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
        $dashboardRefreshIntervalSeconds = SystemSetting::integer('dashboard_refresh_interval_seconds', 30);

        SystemSetting::put('queue_refresh_interval_seconds', $queueRefreshIntervalSeconds);
        SystemSetting::put('dashboard_refresh_interval_seconds', $dashboardRefreshIntervalSeconds);

        return view('admin.settings.index', [
            'queueRefreshIntervalSeconds' => $queueRefreshIntervalSeconds,
            'dashboardRefreshIntervalSeconds' => $dashboardRefreshIntervalSeconds,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'queue_refresh_interval_seconds' => ['required', 'integer', 'min:1', 'max:300'],
            'dashboard_refresh_interval_seconds' => ['required', 'integer', 'min:1', 'max:300'],
        ]);

        SystemSetting::put('queue_refresh_interval_seconds', $data['queue_refresh_interval_seconds']);
        SystemSetting::put('dashboard_refresh_interval_seconds', $data['dashboard_refresh_interval_seconds']);

        return back()->with('status', __('Configurações da fila e do dashboard atualizadas com sucesso.'));
    }
}
