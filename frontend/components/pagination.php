<?php
/**
 * components/pagination.php — Pagination client-side untuk tabel
 *
 * Cara pakai: tambahkan atribut data-paginate pada <table>, opsional:
 *   data-paginate-size="10"   -> jumlah baris per halaman (default 10)
 *   data-paginate-search="id" -> id input pencarian terkait (bila tabel punya pencarian)
 *
 * Inisialisasi otomatis saat DOMContentLoaded untuk semua table[data-paginate].
 */
?>
<style>
    .kf-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 14px;
        flex-wrap: wrap;
    }
    .kf-pagination button {
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border-radius: 10px;
        border: 1px solid #eadfd4;
        background: #fff;
        color: #5e392e;
        font-weight: 700;
        font-size: 12px;
        cursor: pointer;
        transition: all .15s ease;
    }
    .kf-pagination button:hover:not(:disabled) { background: #f4ece1; }
    .kf-pagination button.kf-active { background: #5e392e; color: #fff; border-color: #5e392e; }
    .kf-pagination button:disabled { opacity: .4; cursor: not-allowed; }
    .kf-pagination .kf-info {
        font-size: 11px;
        font-weight: 600;
        color: #8a7a6c;
        margin-right: 6px;
    }
    .kf-pagination .kf-dots {
        min-width: 20px;
        text-align: center;
        color: #8a7a6c;
        font-weight: 700;
        font-size: 12px;
    }
</style>
<script>
(function () {
    // Baris data asli (baris placeholder dengan satu td colspan besar tidak dihitung)
    function kfRealRows(tbody) {
        var out = [];
        var rows = tbody.children;
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            if (r.tagName !== 'TR') continue;
            if (r.cells.length === 1 && r.cells[0].colSpan > 1) continue;
            out.push(r);
        }
        return out;
    }

    function kfVisibleRows(table, rows) {
        var sid = table.getAttribute('data-paginate-search');
        var q = '';
        if (sid) {
            var inp = document.getElementById(sid);
            if (inp) q = inp.value.toLowerCase().trim();
        }
        if (!q) return rows.slice();
        return rows.filter(function (r) {
            var d = (r.getAttribute('data-search') || '').toLowerCase();
            return d.indexOf(q) !== -1;
        });
    }

    function kfPageWindow(page, pages) {
        if (pages <= 7) {
            var all = [];
            for (var i = 0; i < pages; i++) all.push(i);
            return all;
        }
        var set = [0, pages - 1, page - 1, page, page + 1];
        var arr = [];
        for (var j = 0; j < set.length; j++) {
            if (set[j] >= 0 && set[j] < pages && arr.indexOf(set[j]) === -1) arr.push(set[j]);
        }
        arr.sort(function (x, y) { return x - y; });
        var out = [];
        var prev = -99;
        for (var k = 0; k < arr.length; k++) {
            if (arr[k] - prev > 1) out.push('...');
            out.push(arr[k]);
            prev = arr[k];
        }
        return out;
    }

    function kfPaginateTable(table, page) {
        var tbody = table.querySelector('tbody');
        if (!tbody) return;
        var rows = kfRealRows(tbody);
        var size = parseInt(table.getAttribute('data-paginate-size') || '10', 10) || 10;
        var visible = kfVisibleRows(table, rows);
        var total = visible.length;
        var pages = Math.max(1, Math.ceil(total / size));
        if (page < 0) page = 0;
        if (page >= pages) page = pages - 1;
        table.__kfPage = page;

        // Tampilkan/sembunyikan baris sesuai halaman
        for (var i = 0; i < rows.length; i++) rows[i].style.display = 'none';
        var start = page * size;
        for (var j = start; j < start + size && j < total; j++) visible[j].style.display = '';
        // Baris placeholder (kolom kosong) selalu tampil
        var trs = tbody.children;
        for (var m = 0; m < trs.length; m++) {
            if (trs[m].tagName !== 'TR') continue;
            if (trs[m].cells.length === 1 && trs[m].cells[0].colSpan > 1) trs[m].style.display = '';
        }

        // Kontrol pagination
        var ctrl = table.__kfCtrl;
        if (!ctrl) {
            ctrl = document.createElement('div');
            ctrl.className = 'kf-pagination';
            ctrl.addEventListener('click', function (e) {
                var btn = e.target.closest('button');
                if (!btn || btn.disabled) return;
                var cur = table.__kfPage || 0;
                var act = btn.getAttribute('data-kf');
                var np;
                if (act === 'prev') np = cur - 1;
                else if (act === 'next') np = cur + 1;
                else if (act === 'page') np = parseInt(btn.getAttribute('data-page'), 10);
                else return;
                kfPaginateTable(table, np);
            });
            var host = table.closest('.overflow-x-auto') || table;
            host.insertAdjacentElement('afterend', ctrl);
            table.__kfCtrl = ctrl;
        }

        if (pages <= 1) {
            ctrl.style.display = 'none';
            return;
        }
        ctrl.style.display = '';

        var startNum = start + 1;
        var endNum = Math.min(total, start + size);
        var html = '<span class="kf-info">Menampilkan ' + startNum + '\u2013' + endNum + ' dari ' + total + '</span>';
        html += '<button data-kf="prev"' + (page === 0 ? ' disabled' : '') + '>\u2039</button>';
        var win = kfPageWindow(page, pages);
        for (var n = 0; n < win.length; n++) {
            if (win[n] === '...') {
                html += '<span class="kf-dots">\u2026</span>';
            } else {
                html += '<button data-kf="page" data-page="' + win[n] + '"' + (win[n] === page ? ' class="kf-active"' : '') + '>' + (win[n] + 1) + '</button>';
            }
        }
        html += '<button data-kf="next"' + (page === pages - 1 ? ' disabled' : '') + '>\u203a</button>';
        ctrl.innerHTML = html;
    }

    function kfInit() {
        var tables = document.querySelectorAll('table[data-paginate]');
        for (var i = 0; i < tables.length; i++) {
            (function (table) {
                var sid = table.getAttribute('data-paginate-search');
                if (sid) {
                    var inp = document.getElementById(sid);
                    if (inp) {
                        inp.addEventListener('input', function () {
                            kfPaginateTable(table, 0);
                        });
                    }
                }
                kfPaginateTable(table, 0);
            })(tables[i]);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', kfInit);
    } else {
        kfInit();
    }
})();
</script>