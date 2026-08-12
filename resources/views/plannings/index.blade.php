<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Production Planner</title>
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
        color: #222;
    }

    h1 {
        font-size: 1.4rem;
    }

    h2 {
        font-size: 1.1rem;
    }

    section {
        border: 1px solid #ccc;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    label {
        display: block;
        font-size: 0.85rem;
        margin-bottom: 0.2rem;
    }

    input[type="text"], input[type="number"] {
        padding: 0.3rem;
        font-size: 0.9rem;
        width: 100%;
        box-sizing: border-box;
    }

    .field-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .field-row > div {
        flex: 1;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 0.4rem;
        text-align: left;
        font-size: 0.9rem;
    }

    td input[type="number"] {
        width: 5rem;
    }

    button {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .hidden {
        display: none;
    }

    .error {
        margin-top: 1rem;
        padding: 0.6rem;
        border: 1px solid #c00;
        color: #c00;
    }

    .error ul {
        margin: 0.4rem 0 0;
        padding-left: 1.2rem;
    }

    .totals {
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .empty-state {
        color: #666;
        font-size: 0.9rem;
    }
</style>
</head>
<body>

<section>
    <h2>Input Rencana Produksi</h2>
    <form id="planningForm">
        <div class="field-row">
            <div>
                <label for="requestCode">Request Code</label>
                <input type="text" id="requestCode" readonly>
            </div>
            <div>
                <label for="candidateToken">Candidate Token</label>
                <input type="text" id="candidateToken" value="VEH-CANDIDATE_CODE">
            </div>
        </div>

        <table id="slotTable">
            <thead>
                <tr><th>Slot</th><th>Nama</th><th>Rencana awal</th></tr>
            </thead>
            <tbody></tbody>
        </table>

        <button type="submit" id="submitBtn">Submit</button>

        <div id="formError" class="error hidden"></div>
    </form>
</section>

<section class="hidden" id="resultSection">
    <h2>Hasil balancing (<span id="resultStatus"></span>)</h2>
    <table>
        <thead><tr><th>Nama</th><th>Awal</th><th>Hasil</th></tr></thead>
        <tbody id="resultTableBody"></tbody>
    </table>
    <div class="totals">
        Total awal: <strong id="totalAwal">-</strong>
        &nbsp;&nbsp;
        Total hasil: <strong id="totalHasil">-</strong>
    </div>
</section>

<section>
    <h2>History <span id="historyCount"></span></h2>
    <div id="historyLoading" class="empty-state">Memuat riwayat...</div>
    <div id="historyEmpty" class="empty-state hidden">Belum ada riwayat. Proses rencana pertamamu di atas.</div>
    <table id="historyTable" class="hidden">
        <thead><tr><th>Request Code</th><th>Status</th><th>Dibuat</th><th></th></tr></thead>
        <tbody></tbody>
    </table>
</section>

<section class="hidden" id="detailSection">
    <h2>Detail transaksi <button id="closeDetailBtn" type="button">Tutup</button></h2>
    <div id="detailContent"></div>
</section>

<script>
(() => {
    const API_BASE = '/api/plannings';

    const DEFAULT_SLOTS = [
        { slot_order: 1, slot_name: 'Senin' },
        { slot_order: 2, slot_name: 'Selasa' },
        { slot_order: 3, slot_name: 'Rabu' },
        { slot_order: 4, slot_name: 'Kamis' },
        { slot_order: 5, slot_name: 'Jumat' },
        { slot_order: 6, slot_name: 'Sabtu' },
        { slot_order: 7, slot_name: 'Minggu' },
    ];

    const el = (id) => document.getElementById(id);

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[c]));
    }

    function generateRequestCode() {
        return 'REQ-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
    }

    function renderSlotRows() {
        const tbody = document.querySelector('#slotTable tbody');
        tbody.innerHTML = DEFAULT_SLOTS.map((s) => `
            <tr data-order="${s.slot_order}" data-name="${escapeHtml(s.slot_name)}">
                <td>${s.slot_order}</td>
                <td>${escapeHtml(s.slot_name)}</td>
                <td><input type="number" min="0" step="1" value="0" required></td>
            </tr>
        `).join('');
    }

    function collectSlotInputs() {
        return Array.from(document.querySelectorAll('#slotTable tbody tr')).map((row) => ({
            slot_order: Number(row.dataset.order),
            slot_name: row.dataset.name,
            original_quantity: Number(row.querySelector('input').value),
        }));
    }

    function setLoading(loading) {
        const btn = el('submitBtn');
        btn.disabled = loading;
        btn.textContent = loading ? 'Memproses...' : 'Proses balancing';
    }

    function clearError() {
        const box = el('formError');
        box.classList.add('hidden');
        box.innerHTML = '';
    }

    function showError(payload) {
        const box = el('formError');
        box.classList.remove('hidden');
        if (payload.errors) {
            const messages = Object.values(payload.errors).flat();
            box.innerHTML = `<strong>${escapeHtml(payload.message || 'Validasi gagal')}</strong>`
                + `<ul>${messages.map((m) => `<li>${escapeHtml(m)}</li>`).join('')}</ul>`;
        } else {
            box.textContent = payload.message || payload.error || 'Terjadi kesalahan tak terduga.';
        }
    }

    function showResult(planning) {
        el('resultSection').classList.remove('hidden');
        el('resultStatus').textContent = planning.status;
        el('totalAwal').textContent = planning.original_total;
        el('totalHasil').textContent = planning.balanced_total;
        document.getElementById('resultTableBody').innerHTML = planning.slots.map((s) => `
            <tr><td>${escapeHtml(s.slot_name)}</td><td>${s.original_quantity}</td><td>${s.balanced_quantity}</td></tr>
        `).join('');
    }

    async function submitPlanning(event) {
        event.preventDefault();
        clearError();
        setLoading(true);

        const payload = {
            request_code: el('requestCode').value,
            candidate_token: el('candidateToken').value,
            slots: collectSlotInputs(),
        };

        try {
            const res = await fetch(API_BASE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (!res.ok) {
                showError(data);
                return;
            }

            showResult(data.data);
            el('requestCode').value = generateRequestCode();
            await loadHistory();
        } catch (err) {
            showError({ message: 'Gagal terhubung ke server: ' + err.message });
        } finally {
            setLoading(false);
        }
    }

    async function loadHistory() {
        el('historyLoading').classList.remove('hidden');
        el('historyEmpty').classList.add('hidden');
        el('historyTable').classList.add('hidden');
        el('historyCount').textContent = '';

        try {
            const res = await fetch(API_BASE, { headers: { Accept: 'application/json' } });
            const data = await res.json();
            const items = data.data || [];

            el('historyCount').textContent = `(${data.total ?? items.length})`;

            if (items.length === 0) {
                el('historyEmpty').classList.remove('hidden');
                return;
            }

            document.querySelector('#historyTable tbody').innerHTML = items.map((p) => `
                <tr>
                    <td>${escapeHtml(p.request_code)}</td>
                    <td>${escapeHtml(p.status)}</td>
                    <td>${escapeHtml(p.created_at ?? '-')}</td>
                    <td><button type="button" data-planning-id="${p.planning_id}">Detail</button></td>
                </tr>
            `).join('');
            el('historyTable').classList.remove('hidden');
        } catch (err) {
            el('historyEmpty').textContent = 'Gagal memuat history: ' + err.message;
            el('historyEmpty').classList.remove('hidden');
        } finally {
            el('historyLoading').classList.add('hidden');
        }
    }

    async function showDetail(id) {
        el('detailSection').classList.remove('hidden');
        const content = el('detailContent');
        content.textContent = 'Memuat detail...';

        try {
            const res = await fetch(`${API_BASE}/${id}`, { headers: { Accept: 'application/json' } });
            const data = await res.json();

            if (!res.ok) {
                content.textContent = data.message || 'Gagal memuat detail.';
                return;
            }

            const p = data.data;
            content.innerHTML = `
                <div class="totals">
                    Request Code: <strong>${escapeHtml(p.request_code)}</strong>
                    &nbsp;&nbsp; Status: <strong>${escapeHtml(p.status)}</strong>
                    &nbsp;&nbsp; Dibuat: <strong>${escapeHtml(p.created_at ?? '-')}</strong>
                </div>
                <table>
                    <thead><tr><th>Slot</th><th>Awal</th><th>Hasil</th><th>Aktif</th></tr></thead>
                    <tbody>
                        ${p.slots.map((s) => `
                            <tr>
                                <td>${escapeHtml(s.slot_name)}</td>
                                <td>${s.original_quantity}</td>
                                <td>${s.balanced_quantity}</td>
                                <td>${s.is_active ? 'Ya' : 'Tidak'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        } catch (err) {
            content.textContent = 'Gagal terhubung: ' + err.message;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        el('requestCode').value = generateRequestCode();
        renderSlotRows();
        el('planningForm').addEventListener('submit', submitPlanning);
        el('closeDetailBtn').addEventListener('click', () => el('detailSection').classList.add('hidden'));

        document.querySelector('#historyTable tbody').addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-planning-id]');
            if (btn) showDetail(btn.dataset.planningId);
        });

        loadHistory();
    });
})();
</script>

</body>
</html>