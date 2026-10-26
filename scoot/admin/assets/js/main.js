document.querySelectorAll('table.dt').forEach(t => {
    const rows = [...t.rows], head = rows.shift(), tb = head.parentNode;
    let page = 0, sc = -1, asc = 1, q = '';
    const per = 10;
    
    const bar = document.createElement('div');
    bar.className = 'dtbar';
    bar.innerHTML = '<input placeholder="Search..."><span class="pg"></span>';
    t.before(bar);
    
    const inp = bar.querySelector('input'), pg = bar.querySelector('.pg');
    
    const draw = () => {
        const f = rows.filter(r => r.textContent.toLowerCase().includes(q));
        const pages = Math.max(1, Math.ceil(f.length / per));
        page = Math.min(page, pages - 1);
        
        rows.forEach(r => r.style.display = 'none');
        f.slice(page * per, page * per + per).forEach(r => {
            r.style.display = '';
            tb.appendChild(r);
        });
        
        pg.innerHTML = '<button type="button" class="pb">&lsaquo;</button> ' + (page + 1) + ' / ' + pages + ' &middot; ' + f.length + ' rows <button type="button" class="pn">&rsaquo;</button>';
        pg.querySelector('.pb').onclick = () => { if (page > 0) { page--; draw(); } };
        pg.querySelector('.pn').onclick = () => { if (page < pages - 1) { page++; draw(); } };
    };
    
    inp.oninput = () => { q = inp.value.toLowerCase(); page = 0; draw(); };
    
    const val = (r, i) => {
        const c = r.cells[i];
        const x = (c.dataset.s !== undefined ? c.dataset.s : c.textContent.trim());
        const n = x.replace(/^IDR\s/, '').replace(/[,%]/g, '');
        return /^-?\d+(\.\d+)?$/.test(n) ? +n : x.toLowerCase();
    };
    
    [...head.cells].forEach((th, i) => th.onclick = () => {
        asc = sc == i ? -asc : 1;
        sc = i;
        rows.sort((a, b) => (val(a, i) > val(b, i) ? 1 : -1) * asc);
        draw();
    });
    
    draw();
});

const bd = document.getElementById('bd');
if (bd) {
    const u = () => fetch('?p=unread').then(r => r.text()).then(n => bd.textContent = +n ? n : '').catch(() => {});
    u();
    setInterval(u, 5000);
}

const th = document.getElementById('thread');
if (th && window.THREAD) {
    let last = THREAD.last;
    const go = () => th.lastElementChild && th.lastElementChild.scrollIntoView();
    go();
    setInterval(() => fetch('?p=poll&u=' + THREAD.u + '&last=' + last)
        .then(r => r.json())
        .then(a => a.forEach(m => {
            const d = document.createElement('div');
            d.className = 'msg' + (+m.admin ? ' a' : '');
            d.dataset.id = m.id;
            d.textContent = m.body;
            th.appendChild(d);
            last = +m.id;
            go();
        }))
        .catch(() => {}), 4000);
}