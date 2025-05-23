<?php

use App\Http\Controllers\AksesController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\BarangDaganganController;
use App\Http\Controllers\CashflowController;
use App\Http\Controllers\DashboardKandangController;
use App\Http\Controllers\DataKandangController;
use App\Http\Controllers\ExportRecordingController;
use App\Http\Controllers\JualController;
use App\Http\Controllers\JurnalPenyesuaianController;
use App\Http\Controllers\ObatPakanController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\Penjualan_telurmartadahController;
use App\Http\Controllers\PenjualanUmumController;
use App\Http\Controllers\PenutupController;
use App\Http\Controllers\PenyetoranController;
use App\Http\Controllers\PeralatanController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\PoController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\rakController;
use App\Http\Controllers\StokTelurMtdController;
use App\Http\Controllers\SuplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/403', function () {
        view('error.403');
    })->name('403');

    Route::get('/import', function () {
        return view('import_jurnal');
    })->name('import');

    Route::controller(PoController::class)
        ->prefix('po')
        ->name('po.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/load_view_add', 'load_view_add')->name('load_view_add');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::post('/', 'create')->name('create');
            Route::get('/{gudang_id}', 'index')->name('detail');
            Route::get('/edit/{id_produk}', 'edit_load')->name('edit_load');
            Route::post('/edit', 'edit')->name('edit');
            Route::get('/delete/{id_produk}', 'delete')->name('delete');
        });

    Route::controller(OpnameController::class)
        ->prefix('opname')
        ->name('opname.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::get('/add/{gudang_id}', 'add')->name('add_detail');
            Route::get('/delete/{no_nota}', 'delete')->name('delete');
            Route::post('/', 'save')->name('save');
            Route::get('/cetak', 'cetak')->name('cetak');
            Route::get('/edit/{no_nota}', 'edit')->name('edit');
            Route::post('/update', 'update')->name('update');
            Route::get('/detail/{no_nota}', 'detail')->name('detail');
            Route::get('/{gudang_id}', 'index')->name('detail');
        });

    Route::controller(BahanBakuController::class)
        ->prefix('bahan_baku')
        ->name('bahan_baku.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/create', 'create')->name('create');
            Route::get('/stok_masuk', 'stokMasuk')->name('stok_masuk');
            Route::get('/add', 'add')->name('stok_masuk_add');
            Route::get('/opname', 'opname')->name('opname');
            Route::get('/delete', 'delete')->name('delete');
            Route::post('/edit', 'edit')->name('edit');
            Route::post('/store', 'store')->name('store');
            Route::get('/load_menu', 'load_menu')->name('load_menu');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/opname', 'opname')->name('opname');
            Route::get('/opname/add', 'opname_add')->name('opname.add');
            Route::post('/opname/add', 'opname_store')->name('opname.save');
            Route::get('/opname/add/{gudang_id}', 'opname_add')->name('opname.add_detail');
            Route::get('/opname/cetak/{no_nota}', 'opname_cetak')->name('opname.cetak');
            Route::get('/opname/detail/{gudang_id}', 'opname_detail')->name('opname.detail');
            Route::get('/opname/{gudang_id}', 'opname')->name('opname.detail');
            Route::get('/stok_masuk/{gudang_id}', 'stokMasuk')->name('stok_masuk_segment');
            // Route::get('/stok_masuk/edit/{id_produk}', 'edit_load')->name('edit_load');
            Route::get('/{gudang_id}', 'index')->name('detail');
            Route::get('/edit/{id_produk}', 'edit_load')->name('edit_load');
        });

    Route::controller(BarangDaganganController::class)
        ->prefix('barang_dagangan')
        ->name('barang_dagangan.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/create', 'create')->name('create');
            Route::post('/edit', 'edit')->name('edit');
            Route::get('/stok_masuk', 'stokMasuk')->name('stok_masuk');
            Route::get('/add', 'add')->name('add');
            Route::get('/load_menu', 'load_menu')->name('load_menu');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/get_stok_sebelumnya', 'get_stok_sebelumnya')->name('get_stok_sebelumnya');
            Route::post('/store', 'store')->name('store');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/opname', 'opname')->name('opname');
            Route::get('/opname/add', 'opname_add')->name('opname.add');
            Route::post('/opname/add', 'opname_store')->name('opname.save');
            Route::post('/opname/update', 'opname_update')->name('opname.update');
            Route::get('/opname/add/{gudang_id}', 'opname_add')->name('opname.add_detail');
            Route::get('/opname/delete/{gudang_id}', 'opname_delete')->name('opname.delete');
            Route::get('/opname/edit/{no_nota}', 'opname_edit')->name('opname.edit');
            Route::get('/opname/cetak/{no_nota}', 'opname_cetak')->name('opname.cetak');
            Route::get('/opname/detail/{gudang_id}', 'opname_detail')->name('opname.detail');
            Route::get('/opname/{gudang_id}', 'opname')->name('opname.detail');
            Route::get('/{gudang_id}', 'index')->name('detail');
            Route::get('/stok_masuk/{gudang_id}', 'stokMasuk')->name('stok_masuk_segment');
            Route::get('/stok_masuk/edit/{id_produk}', 'detail')->name('edit_load');
        });

    Route::controller(PenutupController::class)
        ->prefix('penutup')
        ->name('penutup.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/saldo', 'saldo')->name('saldo');
            Route::get('/history', 'history')->name('history');
        });

    Route::controller(SuplierController::class)
        ->prefix('suplier')
        ->name('suplier.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'create')->name('create');
            Route::post('/update', 'update')->name('update');
            Route::get('/delete/{id_suplier}', 'delete')->name('delete');
            Route::get('/edit/{id_suplier}', 'edit')->name('edit');
        });

    Route::controller(JurnalPenyesuaianController::class)
        ->prefix('penyesuaian')
        ->name('penyesuaian.')
        ->group(function () {
            Route::get('/', 'jurnal')->name('index');
            Route::get('/aktiva', 'index')->name('aktiva');
            Route::post('/aktiva', 'save_aktiva')->name('save_aktiva');

            Route::get('/peralatan', 'peralatan')->name('peralatan');
            Route::post('/save_peralatan', 'save_peralatan')->name('save_peralatan');

            Route::get('/atk', 'atk')->name('atk');
            Route::get('/atk/{gudang_id}', 'atk')->name('atk_gudang');
            Route::post('/save_atk', 'save_atk')->name('save_atk');
        });

    Route::controller(UserController::class)
        ->prefix('user')
        ->name('user.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'create')->name('create');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
        });

    Route::controller(AksesController::class)
        ->prefix('akses')
        ->name('akses.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/navbar', 'detail_edit')->name('navbar');
            Route::get('/{id}', 'detail')->name('detail');
            Route::get('/{id}', 'navbar_delete')->name('navbar_delete');
            Route::get('/detail/{id}', 'detail_get')->name('detail_get');
            Route::post('/', 'save')->name('save');
            Route::post('/add_menu', 'addMenu')->name('add_menu');
            Route::post('/edit_menu', 'editMenu')->name('edit_menu');
        });

    Route::controller(JualController::class)
        ->prefix('jual')
        ->name('jual.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/aldiexport', 'aldiexport')->name('aldiexport');
            Route::get('/bayar', 'bayar')->name('bayar');
            Route::get('/add', 'add')->name('add');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::get('/get_kredit_pi', 'get_kredit_pi')->name('get_kredit_pi');
            Route::get('/tbh_add', 'tbh_add')->name('tbh_add');
            Route::get('/export', 'export')->name('export');
            Route::get('/edit', 'edit')->name('edit');
            Route::get('/edit_pembayaran', 'edit_pembayaran')->name('edit_pembayaran');
            Route::post('/edit_save_penjualan', 'edit_save_penjualan')->name('edit_save_penjualan');
            Route::post('/edit_save_pembayaran', 'edit_save_pembayaran')->name('edit_save_pembayaran');
            Route::post('/piutang', 'piutang')->name('piutang');
            Route::post('/', 'create')->name('create');
            Route::get('/delete', 'delete')->name('delete');
        });

    Route::controller(ProfitController::class)
        ->prefix('profit')
        ->name('profit.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/load', 'load')->name('load');
            Route::get('/add', 'add')->name('add');
            Route::get('/modal', 'modal')->name('modal');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/print', 'print')->name('print');
            Route::get('/view_akun', 'view_akun')->name('view_akun');
            Route::get('/load_uraian', 'load_uraian')->name('load_uraian');
            Route::get('/count_sisa', 'count_sisa')->name('count_sisa');
            Route::get('/save_subkategori', 'save_subkategori')->name('save_subkategori');
            Route::get('/delete_subkategori', 'delete_subkategori')->name('delete_subkategori');
            Route::get('/update', 'update')->name('update');
        });

    Route::controller(PeralatanController::class)
        ->prefix('peralatan')
        ->name('peralatan.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::post('/save_kelompok', 'save_kelompok')->name('save_kelompok');
            Route::get('/delete_kelompok', 'delete_kelompok')->name('delete_kelompok');
            Route::get('/delete_peralatan', 'delete_peralatan')->name('delete_peralatan');
            Route::get('/edit_kelompok', 'edit_kelompok')->name('edit_kelompok');
            Route::get('/load_edit', 'load_edit')->name('load_edit');
            Route::get('/load_aktiva', 'load_aktiva')->name('load_aktiva');
            Route::get('/get_data_kelompok', 'get_data_kelompok')->name('get_data_kelompok');
            Route::post('/save_aktiva', 'save_aktiva')->name('save_aktiva');
        });

    Route::controller(PenjualanUmumController::class)
        ->prefix('penjualan2')
        ->name('penjualan2.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
            Route::post('/add', 'store')->name('store');
            Route::get('/tbh_add', 'tbh_add')->name('tbh_add');
            Route::get('/tbh_pembayaran', 'tbh_pembayaran')->name('tbh_pembayaran');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/edit', 'edit')->name('edit');
            Route::post('/edit', 'update')->name('update');
            Route::get('/print', 'print')->name('print');
            Route::get('/detail/{no_nota}', 'detail')->name('detail');
        });

    Route::controller(PiutangController::class)
        ->prefix('piutang')
        ->name('piutang.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/edit', 'edit')->name('edit');
            Route::get('/bayar', 'bayar')->name('bayar');
            Route::get('/tbh_baris', 'tbh_baris')->name('tbh_baris');
            Route::post('/create', 'create')->name('create');
            Route::get('/export', 'export')->name('export');
            Route::get('/get_kredit_pi', 'get_kredit_pi')->name('get_kredit_pi');
            Route::get('/edit_pembayaran', 'edit_pembayaran')->name('edit_pembayaran');
            Route::post('/edit_save_pembayaran', 'edit_save_pembayaran')->name('edit_save_pembayaran');
            Route::get('/detail/{no_nota}', 'detail')->name('detail');
        });
    Route::controller(PenyetoranController::class)
        ->prefix('penyetoran')
        ->name('penyetoran.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/perencanaan', 'perencanaan')->name('perencanaan');
            Route::post('/perencanaan', 'save_perencanaan')->name('save_perencanaan');
            Route::get('/bayar', 'bayar')->name('bayar');
            Route::get('/load_perencanaan', 'load_perencanaan')->name('load_perencanaan');
            Route::get('/load_history', 'load_history')->name('load_history');
            Route::get('/print', 'print')->name('print');
            Route::get('/print_setor', 'print_setor')->name('print_setor');
            Route::get('/export', 'export')->name('export');
            Route::post('/hapus_setor', 'hapus_setor')->name('hapus_setor');
            Route::get('/edit/{nota}', 'edit')->name('edit');
            Route::get('/kembali/{nota}', 'kembali')->name('kembali');
            Route::post('/edit', 'save_setor')->name('save_setor');
            Route::get('/delete', 'delete')->name('delete');
        });
    Route::controller(DataKandangController::class)
        ->prefix('data_kandang')
        ->name('data_kandang.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::post('/update', 'update')->name('update');
            Route::get('/delete', 'delete')->name('delete');
            Route::get('/edit/{id}', 'edit')->name('edit');
        });

    Route::controller(StokTelurMtdController::class)
        ->prefix('stok_telur_mtd')
        ->name('stok_telur_mtd.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/add', 'add')->name('add');
        });

    Route::post('commercial_layer', [ExportRecordingController::class, 'index'])->name('commercial_layer');

    Route::controller(DashboardKandangController::class)
        ->prefix('dashboard_kandang')
        ->name('dashboard_kandang.')
        ->group(function () {
            Route::get('/rumus', 'rumus')->name('rumus');
            Route::get('/print_perencanaan', 'print_perencanaan')->name('print_perencanaan');
            Route::get('/detail_pop', 'detail_pop')->name('detail_pop');
            Route::post('/set_font', 'set_font')->name('set_font');
            Route::get('/', 'index')->name('index');
            Route::post('/tambah_telur', 'tambah_telur')->name('tambah_telur');
            Route::post('/export_telur', 'export_telur')->name('export_telur');
            Route::get('/load_telur/{id_kandang}', 'load_telur')->name('load_telur');
            Route::get('/kandang_selesai/{id_kandang}', 'kandang_selesai')->name('kandang_selesai');
            Route::get('/kandang_belum_selesai/{id_kandang}', 'kandang_belum_selesai')->name('kandang_belum_selesai');
            Route::get('/load_populasi/{id_kandang}', 'load_populasi')->name('load_populasi');
            Route::post('/tambah_populasi', 'tambah_populasi')->name('tambah_populasi');
            Route::post('/tambah_karung', 'tambah_karung')->name('tambah_karung');

            // perencanaan
            Route::get('/load_perencanaan/{id_kandang}', 'load_perencanaan')->name('load_perencanaan');
            Route::post('/tambah_perencanaan', 'tambah_perencanaan')->name('tambah_perencanaan');

            Route::post('/daily_layer', 'daily_layer')->name('daily_layer');
            Route::post('/week_layer', 'week_layer')->name('week_layer');
            Route::get('/load_pakan_perencanaan', 'load_pakan_perencanaan')->name('load_pakan_perencanaan');
            Route::get('/export_perencanaan', 'export_perencanaan')->name('export_perencanaan');
            Route::get('/tbh_pakan', 'tbh_pakan')->name('tbh_pakan');
            Route::get('/get_stok_pakan', 'get_stok_pakan')->name('get_stok_pakan');
            Route::get('/save_tambah_pakan', 'save_tambah_pakan')->name('save_tambah_pakan');

            Route::get('/load_obat_pakan', 'load_obat_pakan')->name('load_obat_pakan');
            Route::get('/tbh_obatPakan', 'tbh_obatPakan')->name('tbh_obatPakan');
            Route::get('/get_stok_obat_pakan', 'get_stok_obat_pakan')->name('get_stok_obat_pakan');
            Route::get('/save_tambah_obat_pakan', 'save_tambah_obat_pakan')->name('save_tambah_obat_pakan');

            Route::get('/load_obat_air', 'load_obat_air')->name('load_obat_air');
            Route::get('/tbh_obatAir', 'tbh_obatAir')->name('tbh_obatAir');
            Route::get('/save_tambah_obat_air', 'save_tambah_obat_air')->name('save_tambah_obat_air');
            Route::get('/get_stok_obat_air', 'get_stok_obat_air')->name('get_stok_obat_air');

            Route::get('/load_obat_ayam', 'load_obat_ayam')->name('load_obat_ayam');
            Route::get('/save_tambah_obat_ayam', 'save_tambah_obat_ayam')->name('save_tambah_obat_ayam');
            Route::get('/get_stok_obat_ayam', 'get_stok_obat_ayam')->name('get_stok_obat_ayam');
            Route::get('/get_populasi', 'get_populasi')->name('get_populasi');

            // history perencanaan
            Route::get('/load_detail_perencanaan/{id_kandang}', 'load_detail_perencanaan')->name('load_detail_perencanaan');
            Route::get('/viewHistoryPerencanaan', 'viewHistoryPerencanaan')->name('viewHistoryPerencanaan');
            Route::get('/viewHistoryEditPerencanaan', 'viewHistoryEditPerencanaan')->name('viewHistoryEditPerencanaan');
            Route::post('/edit_perencanaan', 'edit_perencanaan')->name('edit_perencanaan');
            Route::get('/hasilLayer', 'hasilLayer')->name('hasilLayer');
            Route::get('/hasilInputTelur', 'hasilInputTelur')->name('hasilInputTelur');

            // penjualan martadah


            // transfer stok
            Route::get('/transfer_stok', 'transfer_stok')->name('transfer_stok');
            Route::get('/transfer_stok_export/{tgl1}/{tgl2}', 'transfer_stok_export')->name('transfer_stok_export');
            Route::get('/add_transfer_stok', 'add_transfer_stok')->name('add_transfer_stok');
            Route::get('/edit_transfer_stok', 'edit_transfer_stok')->name('edit_transfer_stok');
            Route::get('/tbh_baris_transfer_mtd', 'tbh_baris_transfer_mtd')->name('tbh_baris_transfer_mtd');
            Route::get('/cek_transfer', 'cek_transfer')->name('cek_transfer');
            Route::get('/void_transfer', 'void_transfer')->name('void_transfer');
            Route::get('/detail_transfer/{no_nota}', 'detail_transfer')->name('detail_transfer');
            Route::get('/delete_transfer', 'delete_transfer')->name('delete_transfer');
            Route::post('/save_transfer', 'save_transfer')->name('save_transfer');
            Route::post('/update_transfer', 'update_transfer')->name('update_transfer');

            // penjualan umum
            Route::get('/penjualan_umum', 'penjualan_umum')->name('penjualan_umum');
            Route::get('/penjualan_umum_export/{tgl1}/{tgl2}', 'penjualan_umum_export')->name('penjualan_umum_export');
            Route::get('/tbh_add', 'tbh_add')->name('tbh_add');
            Route::get('/get_stok', 'get_stok')->name('get_stok');
            Route::get('/void_penjualan_umum', 'void_penjualan_umum')->name('void_penjualan_umum');
            Route::get('/edit_penjualan', 'edit_penjualan')->name('edit_penjualan');
            Route::get('/add_penjualan_umum', 'add_penjualan_umum')->name('add_penjualan_umum');
            Route::get('/detail/{urutan}', 'detail')->name('detail');
            Route::get('/load_detail_nota/{urutan}', 'load_detail_nota')->name('load_detail_nota');
            Route::post('/update_penjualan', 'update_penjualan')->name('update_penjualan');
            Route::post('/save_penjualan_umum', 'save_penjualan_umum')->name('save_penjualan_umum');
            // Transfer Ayam
            Route::post('/transfer_ayam', 'transfer_ayam')->name('transfer_ayam');
            Route::post('/penjualan_ayam', 'penjualan_ayam')->name('penjualan_ayam');
            Route::get('/cek_penjualan_ayam', 'cek_penjualan_ayam')->name('cek_penjualan_ayam');
            Route::get('/export_vitamin_accurate', 'export_vitamin_accurate')->name('export_vitamin_accurate');
            Route::get('/export_penjualan_accurate', 'export_penjualan_accurate')->name('export_penjualan_accurate');
        });


    Route::controller(Penjualan_telurmartadahController::class)
        ->prefix('dashboard_kandang')
        ->name('dashboard_kandang.')
        ->group(function () {
            Route::get('/penjualan_telur', 'penjualan_telur')->name('penjualan_telur');
            Route::get('/penjualan_telur_export/{tgl1}/{tgl2}', 'penjualan_telur_export')->name('penjualan_telur_export');
            Route::get('/tambah_baris_jual_mtd', 'tambah_baris_jual_mtd')->name('tambah_baris_jual_mtd');
            Route::get('/add_penjualan_telur', 'add_penjualan_telur')->name('add_penjualan_telur');
            Route::get('/edit_telur', 'edit_telur')->name('edit_telur');
            Route::get('/detail_penjualan_mtd', 'detail_penjualan_mtd')->name('detail_penjualan_mtd');
            Route::get('/get_detail_penjualan_mtd', 'get_detail_penjualan_mtd')->name('get_detail_penjualan_mtd');
            Route::get('/delete_penjualan_mtd', 'delete_penjualan_mtd')->name('delete_penjualan_mtd');
            Route::post('/save_penjualan_telur', 'save_penjualan_telur')->name('save_penjualan_telur');
            Route::post('/save_edit_telur', 'save_edit_telur')->name('save_edit_telur');
            Route::get('/cek_penjualan_telur', 'cek_penjualan_telur')->name('cek_penjualan_telur');
            Route::get('/void_penjualan_mtd', 'void_penjualan_mtd')->name('void_penjualan_mtd');
        });
    Route::controller(rakController::class)
        ->prefix('rak')
        ->name('rak.')
        ->group(function () {
            Route::post('/create', 'create')->name('create');
            Route::post('/opname', 'opname')->name('opname');
            Route::get('/print_opname/{no_nota}/{print?}', 'print_opname')->name('print_opname');
        });

    Route::controller(ObatPakanController::class)
        ->prefix('dashboard_kandang')
        ->name('dashboard_kandang.')
        ->group(function () {
            Route::get('/tambah_pakan_stok', 'tambah_pakan_stok')->name('tambah_pakan_stok');
            Route::get('/load_stok_pakan', 'load_stok_pakan')->name('load_stok_pakan');
            Route::get('/tambah_vitamin', 'tambah_vitamin')->name('tambah_vitamin');
            Route::get('/tambah_baris_stok', 'tambah_baris_stok')->name('tambah_baris_stok');
            Route::get('/tambah_baris_stok_vitamin', 'tambah_baris_stok_vitamin')->name('tambah_baris_stok_vitamin');
            Route::get('/opname_pakan', 'opname_pakan')->name('opname_pakan');
            Route::get('/opnme_vitamin', 'opnme_vitamin')->name('opnme_vitamin');
            Route::get('/history_stok', 'history_stok')->name('history_stok');
            Route::get('/get_satuan_vitmin_mtd', 'get_satuan')->name('get_satuan_vitmin_mtd');
            Route::get('/history_pakvit', 'history_pakvit')->name('history_pakvit');
            Route::get('/history_pakan', 'history_pakan')->name('history_pakan');
            Route::get('/history_pakvit', 'history_pakvit')->name('history_pakvit');
            Route::get('/print_opname/{no_nota}/{print?}', 'print_opname')->name('print_opname');
            Route::get('/search_history_pakvit', 'history_pakvit')->name('search_history_pakvit');
            Route::post('/save_opname_pakan', 'save_opname_pakan')->name('save_opname_pakan');
            Route::post('/save_tambah_pakan_stok', 'save_tambah_pakan')->name('save_tambah_pakan_stok');
            Route::post('/save_vaksin', 'save_vaksin')->name('save_vaksin');
        });
});
