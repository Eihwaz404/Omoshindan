<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = $this->loadSettings();
        $this->seedDefaultSettings($settings);

        return view('admin.settings.index', [
            ...$settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'queue_refresh_interval_seconds' => ['required', 'integer', 'min:1', 'max:300'],
            'dashboard_refresh_interval_seconds' => ['required', 'integer', 'min:1', 'max:300'],
        ];

        foreach ($this->workingDays() as $dayKey => $day) {
            foreach (['start', 'end', 'lunch_start', 'lunch_end'] as $field) {
                $rules["work_schedule.{$dayKey}.{$field}"] = ['required', 'date_format:H:i'];
            }
        }

        foreach ($this->priorityLevels() as $priorityKey => $priority) {
            $rules["priority_sla.{$priorityKey}.minutes"] = ['required', 'integer', 'min:1', 'max:10080'];
        }

        $data = $request->validate($rules);

        SystemSetting::put('queue_refresh_interval_seconds', $data['queue_refresh_interval_seconds']);
        SystemSetting::put('dashboard_refresh_interval_seconds', $data['dashboard_refresh_interval_seconds']);

        foreach ($this->workingDays() as $dayKey => $day) {
            foreach (['start', 'end', 'lunch_start', 'lunch_end'] as $field) {
                SystemSetting::put("work_schedule_{$dayKey}_{$field}", Arr::get($data, "work_schedule.{$dayKey}.{$field}"));
            }
        }

        foreach ($this->priorityLevels() as $priorityKey => $priority) {
            SystemSetting::put(
                "priority_{$priorityKey}_resolution_minutes",
                Arr::get($data, "priority_sla.{$priorityKey}.minutes"),
            );
        }

        return back()->with('status', __('Configurações de atualização, jornada e prioridades atualizadas com sucesso.'));
    }

    private function loadSettings(): array
    {
        $settings = [
            'queueRefreshIntervalSeconds' => SystemSetting::integer('queue_refresh_interval_seconds', 15),
            'dashboardRefreshIntervalSeconds' => SystemSetting::integer('dashboard_refresh_interval_seconds', 30),
            'workSchedule' => [],
            'prioritySla' => [],
            'workingDays' => $this->workingDays(),
            'priorityLevels' => $this->priorityLevels(),
        ];

        foreach ($settings['workingDays'] as $dayKey => $day) {
            $settings['workSchedule'][$dayKey] = [
                'start' => SystemSetting::getValue("work_schedule_{$dayKey}_start", '08:00'),
                'end' => SystemSetting::getValue("work_schedule_{$dayKey}_end", '17:00'),
                'lunch_start' => SystemSetting::getValue("work_schedule_{$dayKey}_lunch_start", '12:00'),
                'lunch_end' => SystemSetting::getValue("work_schedule_{$dayKey}_lunch_end", '13:00'),
            ];
        }

        foreach ($settings['priorityLevels'] as $priorityKey => $priority) {
            $settings['prioritySla'][$priorityKey] = [
                'minutes' => SystemSetting::integer("priority_{$priorityKey}_resolution_minutes", $this->priorityDefaultMinutes($priorityKey)),
            ];
        }

        return $settings;
    }

    private function seedDefaultSettings(array $settings): void
    {
        SystemSetting::put('queue_refresh_interval_seconds', $settings['queueRefreshIntervalSeconds']);
        SystemSetting::put('dashboard_refresh_interval_seconds', $settings['dashboardRefreshIntervalSeconds']);

        foreach ($settings['workingDays'] as $dayKey => $day) {
            foreach (['start', 'end', 'lunch_start', 'lunch_end'] as $field) {
                SystemSetting::put("work_schedule_{$dayKey}_{$field}", $settings['workSchedule'][$dayKey][$field]);
            }
        }

        foreach ($settings['priorityLevels'] as $priorityKey => $priority) {
            SystemSetting::put(
                "priority_{$priorityKey}_resolution_minutes",
                $settings['prioritySla'][$priorityKey]['minutes'],
            );
        }
    }

    /**
     * @return array<string, array{label: string}>
     */
    private function workingDays(): array
    {
        return [
            'monday' => ['label' => __('Segunda-feira')],
            'tuesday' => ['label' => __('Terça-feira')],
            'wednesday' => ['label' => __('Quarta-feira')],
            'thursday' => ['label' => __('Quinta-feira')],
            'friday' => ['label' => __('Sexta-feira')],
        ];
    }

    /**
     * @return array<string, array{label: string}>
     */
    private function priorityLevels(): array
    {
        return collect(config('support.priorities', []))
            ->map(fn (array $priority, string $key) => ['label' => $priority['label'] ?? ucfirst($key)])
            ->all();
    }

    private function priorityDefaultMinutes(string $priorityKey): int
    {
        return match ($priorityKey) {
            'low' => 1440,
            'normal' => 720,
            'medium' => 240,
            'high' => 60,
            default => 720,
        };
    }
}
