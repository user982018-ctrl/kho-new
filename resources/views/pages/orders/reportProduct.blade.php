<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Danh sách sản phẩm - Hiển thị & tìm kiếm</title>
  <style>
    :root{
      --bg:#0f172a;           /* slate-900 */
      --panel:#111827;        /* gray-900 */
      --card:#0b1222;         /* custom dark */
      --text:#e5e7eb;         /* gray-200 */
      --muted:#9ca3af;        /* gray-400 */
      --brand:#22d3ee;        /* cyan-400 */
      --accent:#a78bfa;       /* violet-400 */
      --ok:#34d399;           /* emerald-400 */
      --warn:#f59e0b;         /* amber-500 */
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background:linear-gradient(120deg,#0b1020,#0f172a 40%,#0b1020);
      color:var(--text); font:15px/1.5 system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Inter,Arial,sans-serif;
    }
    .container{max-width:1100px;margin:32px auto;padding:0 16px}

    .heading{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;margin-bottom:16px}
    .heading h1{font-size:20px;margin:0;letter-spacing:.2px}

    .toolbar{display:flex;flex-wrap:wrap;gap:10px}
    .toolbar .field{position:relative}
    .input, .select, .btn{border:1px solid #1f2937;background:var(--panel);color:var(--text);padding:10px 12px;border-radius:12px;outline:none}
    .input{min-width:240px}
    .select{min-width:170px}
    .btn{cursor:pointer}
    .btn:hover{border-color:#374151}
    .pill{display:inline-flex;gap:8px;align-items:center;background:rgba(34,211,238,.1);color:var(--brand);padding:6px 10px;border-radius:999px;border:1px solid rgba(34,211,238,.25)}

    .board{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);backdrop-filter: blur(8px);border-radius:20px;padding:16px}

    .stats{display:flex;gap:12px;flex-wrap:wrap;margin:4px 0 14px}
    .stat{flex:1 1 160px;background:var(--card);border:1px solid rgba(255,255,255,.05);padding:12px 14px;border-radius:16px}
    .stat .label{color:var(--muted);font-size:12px}
    .stat .value{font-size:20px;font-weight:700;margin-top:2px}

    .grid{display:grid;grid-template-columns:repeat(1,minmax(0,1fr));gap:10px}
    @media (min-width:560px){.grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media (min-width:900px){.grid{grid-template-columns:repeat(3,minmax(0,1fr));}}

    .item{position:relative;background:var(--card);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px;display:flex;gap:10px;align-items:flex-start;transition:transform .12s ease, box-shadow .12s ease}
    .item:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.25)}

    .qty{min-width:48px;display:flex;align-items:center;justify-content:center;height:40px;border-radius:12px;font-weight:800;border:1px dashed rgba(255,255,255,.15)}
    .qty.small{background:rgba(167,139,250,.12);color:var(--accent)}
    .qty.mid{background:rgba(34,211,238,.12);color:var(--brand)}
    .qty.big{background:rgba(52,211,153,.12);color:var(--ok)}

    .name{font-weight:600}
    .sub{color:var(--muted);font-size:12px;margin-top:6px}

    .empty{padding:28px;text-align:center;color:var(--muted)}
  </style>
</head>
<body>
  <div class="container">
    <div class="heading">
      <h1>Danh sách sản phẩm</h1>
      <span class="pill" id="activeFilters">Đang hiển thị tất cả</span>
    </div>

    <div class="board">
      <div class="toolbar">
        <input class="input" id="q" placeholder="Tìm theo tên, NPK, dung tích, ..." />
        <select class="select" id="sort">
          <option value="qtyDesc">Sắp xếp: Số lượng giảm dần</option>
          <option value="qtyAsc">Sắp xếp: Số lượng tăng dần</option>
          <option value="nameAsc">Sắp xếp: Tên A→Z</option>
          <option value="nameDesc">Sắp xếp: Tên Z→A</option>
        </select>
        <button class="btn" id="export"></button>
      </div>

      <div class="stats">
        <div class="stat">
          <div class="label">Số mặt hàng</div>
          <div class="value" id="skuCount">—</div>
        </div>
        <div class="stat">
          <div class="label">Tổng số lượng</div>
          <div class="value" id="totalQty">—</div>
        </div>
      </div>

      <div id="list" class="grid"></div>
      <div id="empty" class="empty" style="display:none">Không tìm thấy kết quả…</div>
    </div>
  </div>

  <script>
    // Dữ liệu đầu vào (từ yêu cầu của bạn)
    const rawItems9 = [
      { name: "5kg 22-22-22", qty: 6 },
      { name: "5kg 30-10-10", qty: 18 },
      { name: "5kg 10-10-30", qty: 6 },
      { name: "20kg 22-22-22", qty: 6 },
      { name: "5kg 20-20-20", qty: 1 },
      { name: "20kg 30-10-10", qty: 8 },
      { name: "5kg DAP", qty: 3 },
      { name: "20kg 15-15-15", qty: 1 },
      { name: "5kg 15-15-15", qty: 1 },
      { name: "20kg 10-10-30", qty: 1 },
      { name: "5kg 10-50-10", qty: 4 },
      { name: "5kg 16-16-16", qty: 1 },
      { name: "20kg DAP", qty: 1 },
      { name: "20kg 10-50-10", qty: 1 },
      { name: "1 xô Tricho + 3kg Humic", qty: 14 },
      { name: "Men vi sinh Super Aqua 5L", qty: 2 },
      { name: "Đạm tôm 20l", qty: 5 },
      { name: "Vọt đọt 500ml", qty: 14 },
      { name: "1kg Humic", qty: 42 },
      { name: "Men vi sinh Super Aqua 20L", qty: 1 },
      { name: "Men tiêu hoá 1kg", qty: 1 },
      { name: "Siêu lớn trái", qty: 7 },
      { name: "1 Xô Tricho 10kg + 2 canxibo", qty: 2 },
      { name: "1 Xô Tricho 10kg + 3 Vọt đọt", qty: 3 },
      { name: "Xô Tricho 10kg", qty: 4 },
    ];
    // console.log('rawItems2', rawItems2);
    const rawItemsJson = '<?php echo $lastData;?>';
    
    const rawItems = JSON.parse(rawItemsJson);
    console.log('rawItems', rawItems);
    // Helper: bỏ dấu tiếng Việt để tìm kiếm mượt hơn
    const noAccent = (str) => str
      .normalize('NFD')
      .replace(/\p{Diacritic}/gu, '')
      .toLowerCase();

    const el = (id) => document.getElementById(id);
    const listEl = el('list');
    const emptyEl = el('empty');
    const skuCountEl = el('skuCount');
    const totalQtyEl = el('totalQty');
    const qEl = el('q');
    const sortEl = el('sort');
    const activeFiltersEl = el('activeFilters');

    let data = rawItems.map((x, i) => ({ id: i+1, ...x, key: noAccent(x.name) }));

    function formatNumber(n){return Intl.NumberFormat('vi-VN').format(n)}

    function render(items){
      listEl.innerHTML = '';
      if(!items.length){
        emptyEl.style.display = 'block';
      } else {
        emptyEl.style.display = 'none';
        const frag = document.createDocumentFragment();
        items.forEach(item => {
          const wrap = document.createElement('div');
          wrap.className = 'item';
          const qtyClass = item.id >= 15 ? 'big' : item.qty >= 5 ? 'mid' : 'small';
          wrap.innerHTML = `
            <div class="qty ${qtyClass}">${item.id}</div>
            <div style="flex:1">
              <div class="name">${item.name}</div>
              <div class="sub">Số lượng: ${item.qty}</div>
            </div>
          `;
          frag.appendChild(wrap);
        });
        listEl.appendChild(frag);
      }

      // Update stats
      skuCountEl.textContent = formatNumber(items.length);
      totalQtyEl.textContent = formatNumber(items.reduce((a,b)=>a+b.qty,0));
    }

    function apply(){
      const term = noAccent(qEl.value || '');
      const sort = sortEl.value;

      let items = data.filter(x => x.key.includes(term));

      switch (sort){
        case 'qtyAsc': items.sort((a,b)=>a.qty-b.qty); break;
        case 'nameAsc': items.sort((a,b)=>a.key.localeCompare(b.key)); break;
        case 'nameDesc': items.sort((a,b)=>b.key.localeCompare(a.key)); break;
        default: items.sort((a,b)=>b.qty-a.qty); // qtyDesc
      }

      activeFiltersEl.textContent = term ? `Lọc theo: "${qEl.value}"` : 'Đang hiển thị tất cả';
      render(items);
    }

    // Export CSV
    function exportCSV(){
      const header = 'Ten,Soluong\n';
      const rows = data.map(x => `${'"'+x.name.replaceAll('"','""')+'"'},${x.qty}`).join('\n');
      const blob = new Blob([header + rows], {type: 'text/csv;charset=utf-8;'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url; a.download = 'danhsach_sanpham.csv';
      document.body.appendChild(a); a.click(); a.remove();
      URL.revokeObjectURL(url);
    }

    // Events
    qEl.addEventListener('input', apply);
    sortEl.addEventListener('change', apply);
    el('export').addEventListener('click', exportCSV);

    // Init
    apply();
  </script>
</body>
</html>
