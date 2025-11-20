<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    UserController,
    MasterRoleController,
    KnowledgeBaseController,
    MasterDepartmentController,
    MasterMenuController,
    TicketHeadController,
    MasterDivisionController,
    MasterPositionController,
    TicketPrioritiesController,
    PdfController,
    MasterSubMenuController,
    MasterSiteController,
    ProfileController
};
use App\Models\Ticket;

// Redirect login
Route::get('/', fn() => redirect()->route('login'));
Route::get('/register', fn() => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




//SUPER ADMIN + ADMIN SISTEM
// Route::middleware(['auth', 'verified', 'role:1,2'])->group(function () {
//     Route::prefix('masters')->group(function () {
//         Route::resource('/users', UserController::class);
//         Route::resource('/roles', MasterRoleController::class);
//         Route::resource('/departments', MasterDepartmentController::class);
//         Route::resource('/menus', MenuController::class);
//         Route::resource('/sub-menus', SubMenuController::class);
//         Route::resource('/divisions', MasterDivisionController::class);
//         Route::resource('/positions', MasterPositionController::class);
//         Route::resource('/priorities', TicketPrioritiesController::class);
//         Route::resource('/sites', MasterSiteController::class);
//     });


//     Route::get('menus/{id}/setting', [MenuController::class, 'setting'])->name('menus.setting');
//     Route::put('menus/{menu}/setup', [MenuController::class, 'setup'])->name('menus.setup');
//     Route::prefix('reports')->name('reports.')->group(function () {
//         Route::get('tickets', [App\Http\Controllers\Report\TicketReportController::class, 'index'])->name('tickets.index');
//         Route::get('tickets/data', [App\Http\Controllers\Report\TicketReportController::class, 'data'])->name('tickets.data'); // DataTables ajax
//         Route::get('tickets/export', [App\Http\Controllers\Report\TicketReportController::class, 'export'])->name('tickets.export'); // export
//     });

//     Route::prefix('knowledge-base')->name('kb.')->group(function () {
//         Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
//         Route::get('/{category:slug}/create', [KnowledgeBaseController::class, 'create'])->name('article.create');
//         Route::post('/{category:slug}', [KnowledgeBaseController::class, 'store'])->name('article.store');
//         Route::get('/{category:slug}/{article:slug}/edit', [KnowledgeBaseController::class, 'edit'])->name('article.edit');
//         Route::put('/{category:slug}/{article:slug}', [KnowledgeBaseController::class, 'update'])->name('article.update');
//         Route::delete('/{category:slug}/{article:slug}', [KnowledgeBaseController::class, 'destroy'])->name('article.destroy');
//     });
//     Route::post('/kb-upload-image', [KnowledgeBaseController::class, 'uploadImage'])->name('kb.image.upload');
//     Route::post('/kb/tinymce-image-upload', [KnowledgeBaseController::class, 'tinymceUpload'])->name('kb.tinymce.upload');

//     Route::delete('/approval-rules/{id}', [App\Http\Controllers\ApprovalRuleController::class, 'destroy'])
//         ->name('approval-rules.destroy');
// });

// Route::middleware(['auth', 'verified', 'role:1'])->group(function () {
//     Route::prefix('tickets')->name('tickets.')->group(function () {
//         Route::get('{ticket}/assign', [TicketController::class, 'assignForm'])->name('assign');
//         Route::post('{ticket}/assign', [TicketController::class, 'assignStore'])->name('assign.store');
//         Route::get('/assign', [TicketController::class, 'assignView'])->name('assigner');
//     });
// });
// Route::middleware(['auth', 'verified', 'role:1,4'])->group(function () {
//     Route::prefix('tickets')->name('tickets.')->group(function () {
//         Route::get('/data', [TicketController::class, 'all'])->name('all');
//     });
// });
// Route::middleware(['auth', 'verified', 'role:2,1'])->group(function () {
//     Route::prefix('tickets')->name('tickets.')->group(function () {
//         Route::post('{ticket}/complete', [TicketController::class, 'complete'])
//             ->name('tickets.complete');
//         Route::post('{ticket}/start', [TicketController::class, 'start'])
//             ->name('start');
//         Route::post('{ticket}/throw', [TicketController::class, 'throw'])
//             ->name('throw');
//         Route::get('/work', [TicketController::class, 'workView'])
//             ->name('worker');
//     });
// });


// // =========================================
// // STAFF / USER
// Route::middleware(['auth', 'verified'])->group(function () {
//     // Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
//     Route::view('profile', 'profile')->name('profile');

//     Route::prefix('tickets')->name('tickets.')->group(function () {
//         Route::get('{ticket:slug}/tracking', [TicketController::class, 'show'])->name('show');
//         Route::resource('/', TicketController::class)
//             ->parameters(['' => 'ticket'])
//             ->only(['index', 'create', 'store', 'update', 'destroy', 'edit']);
//     });

//     Route::get('/get-submenus/{menu_id}', [TicketController::class, 'getSubMenus'])->name('getSubMenus');

//     Route::prefix('tickets')->name('tickets.')->group(function () {
//         Route::post('{ticket}/approve', [TicketController::class, 'approve'])->name('approve');
//         Route::post('{ticket}/reject', [TicketController::class, 'reject'])->name('reject');
//         Route::delete('{ticket}/attachment/{attachment}/delete', [TicketController::class, 'deleteAttachment'])
//             ->name('attachment.delete');
//         Route::post('attachment/{ticket}/upload', [TicketController::class, 'uploadAttachment'])
//             ->name('attachment.upload');
//         Route::post('{ticket}/feedback', [TicketController::class, 'feedback'])->name('feedback');
//         Route::post('{ticket}/close', [TicketController::class, 'close'])->name('close');
//         Route::get('/approver', [TicketController::class, 'approverView'])->name('approver');
//         Route::get('/{ticket}/details/{line}', [TicketController::class, 'getDetailLine'])
//             ->name('details.line');
//     });

//     Route::prefix('knowledge-base')->name('kb.')->group(
//         function () {
//             Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');

//             Route::get('/{category:slug}', [KnowledgeBaseController::class, 'category'])->name('category');
//             Route::get('/{category:slug}/{article:slug}', [KnowledgeBaseController::class, 'show'])->name('article');

//             Route::post('/{category:slug}/{article:slug}/feedback', [KnowledgeBaseController::class, 'feedback'])
//                 ->middleware('throttle:20,1')
//                 ->name('article.feedback');
//         }
//     );

//     Route::get('/pdf/generate/{ticket:slug}', [PdfController::class, 'generate'])
//         ->name('pdf.generate');
// });

// =========================================
// AUTH
// =========================================
require __DIR__ . '/auth.php';
