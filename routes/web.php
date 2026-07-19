<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Access\UserController as AccessUserController;
use App\Http\Controllers\Support\TicketController as SupportTicketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('suporte/tickets')->name('support.tickets.')->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('index');
        Route::get('/novo', [SupportTicketController::class, 'create'])->name('create');
        Route::post('/', [SupportTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [SupportTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/comentarios', [SupportTicketController::class, 'comment'])->name('comment');
        Route::post('/{ticket}/assumir', [SupportTicketController::class, 'take'])->name('take');
        Route::post('/{ticket}/tratar', [SupportTicketController::class, 'work'])->name('work');
        Route::post('/{ticket}/encaminhar', [SupportTicketController::class, 'transfer'])->name('transfer');
        Route::post('/{ticket}/finalizar', [SupportTicketController::class, 'resolve'])->name('resolve');
        Route::post('/{ticket}/devolver', [SupportTicketController::class, 'returnToSupport'])->name('return');
        Route::post('/{ticket}/fechar', [SupportTicketController::class, 'close'])->name('close');
    });

    Route::prefix('acesso/usuarios')->name('access.users.')->group(function () {
        Route::get('/', [AccessUserController::class, 'index'])->name('index')->middleware('can:users.view');
        Route::get('/criar', [AccessUserController::class, 'create'])->name('create')->middleware('can:users.create');
        Route::post('/', [AccessUserController::class, 'store'])->name('store')->middleware('can:users.create');
        Route::get('/{user}/editar', [AccessUserController::class, 'edit'])->name('edit')->middleware('can:users.update');
        Route::put('/{user}', [AccessUserController::class, 'update'])->name('update')->middleware('can:users.update');
        Route::patch('/{user}/status', [AccessUserController::class, 'toggle'])->name('toggle')->middleware('can:users.toggle');
        Route::delete('/{user}', [AccessUserController::class, 'destroy'])->name('destroy')->middleware('can:users.delete');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
